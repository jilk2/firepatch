<!-- 
|||||      |||
||   |    |   |
||   |    |   |
||||||   |||||||        ckend
||   |   |     |
||   |  |       |
|||||   |       |
-->

<?php

require_once('./DB/DBConnect.php');

$db = firepatchMysqli();

date_default_timezone_set('Europe/Amsterdam');
$today = date('Y-m-d');

$errors = [];
$editMissionId = null;
$claimMissionMessage = null;
$urgentMissionSuggestion = null;
$claimMissionId = 0;

function activateMission(mysqli $db, int $missionId): void
{
    mysqli_begin_transaction($db);

    try {
        $missionCheck = mysqli_prepare($db, 'SELECT id FROM missions WHERE id = ?');
        $missionCheck->bind_param('i', $missionId);
        $missionCheck->execute();
        $missionExists = $missionCheck->get_result()->num_rows === 1;
        $missionCheck->close();

        if (!$missionExists) {
            throw new RuntimeException('De missie kon niet worden gevonden.');
        }

        // set current active mission to queued
        mysqli_query($db, "UPDATE missions SET mission_state = 'queued' WHERE mission_state = 'active'");

        // set specific mission to active
        $activateStatement = mysqli_prepare(
            $db,
            "UPDATE missions SET mission_state = 'active' WHERE id = ?"
        );
        $activateStatement->bind_param('i', $missionId);
        $activateStatement->execute();
        $activateStatement->close();

        // Update logboek statuses
        $pendingStatement = mysqli_prepare(
            $db,
            "UPDATE logboek SET status = 'pending' WHERE status = 'active' AND mission_id <> ?"
        );
        $pendingStatement->bind_param('i', $missionId);
        $pendingStatement->execute();
        $pendingStatement->close();

        $activeStatement = mysqli_prepare(
            $db,
            "UPDATE logboek SET status = 'active' WHERE mission_id = ? AND status <> 'done'"
        );
        $activeStatement->bind_param('i', $missionId);
        $activeStatement->execute();
        $activeStatement->close();

        mysqli_commit($db);
    } catch (Throwable $exception) {
        mysqli_rollback($db);
        throw $exception;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'activate_mission') {
    $missionId = filter_var($_POST['mission_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if (!$missionId) {
        $errors[] = 'De missie kon niet worden gevonden.';
    } else {
        try {
            activateMission($db, $missionId);
            header('Location: mission.php');
            exit();
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

function missionSuggestionFromClaim(array $claim): array
{
    $claimText = strtolower(trim(implode(' ', [
        (string) ($claim['title'] ?? ''),
        (string) ($claim['description'] ?? ''),
    ])));

    $suggestion = [
        'goal' => 'Inspectie uitvoeren',
        'intervention' => '',
        'priority' => 'normaal',
    ];

    if (str_contains($claimText, 'brand') || str_contains($claimText, 'vuur')) {
        $suggestion = [
            'goal' => 'Brand blussen',
            'intervention' => 'Brand blussen',
            'priority' => 'hoog',
        ];
    } elseif (str_contains($claimText, 'afval') || str_contains($claimText, 'vuilnis')) {
        $suggestion = [
            'goal' => 'Afval opruimen',
            'intervention' => 'Afval opruimen',
            'priority' => 'normaal',
        ];
    }

    $sector = '';
    
    
    if (isset($claim['x_value']) && isset($claim['y_value'])) {
        
        $x_index = floor((float)$claim['x_value'] * 6);
        $y_index = floor((float)$claim['y_value'] * 6);
        
        
        $sectorNumber = ($y_index * 6) + $x_index + 1;
        
        if ($sectorNumber >= 1 && $sectorNumber <= 36) {
            $sector = 'Section ' . $sectorNumber;
        }
    }

    $suggestion['sector'] = $sector;
    return $suggestion;
}



if (isset($_POST['action']) && $_POST['action'] === 'prepare_mission') {
    $claimId = filter_var($_POST['claim_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if (!$claimId) {
        $errors[] = 'De melding kon niet worden gevonden.';
    } else {    // get claim from database and check if it is confirmed
        $claimStatement = mysqli_prepare($db, "SELECT * FROM claims WHERE id = ? AND `status` = 'true'");
        $claimStatement->bind_param('i', $claimId);
        $claimStatement->execute();
        $claim = $claimStatement->get_result()->fetch_assoc();
        $claimStatement->close();

        if (!$claim) {
            $errors[] = 'Alleen bevestigde meldingen kunnen een missievoorstel maken.';
        } else {    // prepare mission suggestion from claim
            $suggestion = missionSuggestionFromClaim($claim);
            $_POST['sector'] = $suggestion['sector'];
            $_POST['goals'] = [$suggestion['goal']];
            $_POST['interventions'] = $suggestion['intervention'] === ''
                ? []
                : [$suggestion['intervention']];
            $currentTime = date('H') * 60 + date('i'); // current time in minutes
            $_POST['start_time'] = $currentTime;
            $_POST['end_time'] = $currentTime + 120; // default to 2 hours later
            $_POST['claim_id'] = $claimId;
            $_POST['priority'] = $suggestion['priority'] === 'hoog' ? 'high' : 'normal';
            $claimMissionId = $claimId;
            $urgentMissionSuggestion = $suggestion['priority'] === 'hoog' ? $suggestion : null;
            $claimMissionMessage = sprintf(
                'Missievoorstel voor "%s" geladen. Prioriteit: %s.',
                (string) ($claim['title'] ?? 'de melding'),
                $suggestion['priority']
            );

            if ($suggestion['sector'] === '') {
                $errors[] = 'De sector kon niet uit de melding worden gehaald. Kies deze handmatig.';
            }
        }
    }
}


// Missie toevoegen of aanpassen
if (isset($_POST['submit'])) {

    $editMissionId = filter_var($_POST['edit_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]) ?: null;

    $sector = trim((string) ($_POST['sector'] ?? ''));

    $goalsArray = is_array($_POST['goals'] ?? null)
        ? $_POST['goals']
        : [];

    $interventionsArray = is_array($_POST['interventions'] ?? null)
        ? $_POST['interventions']
        : [];

    $claimMissionId = filter_var($_POST['claim_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]) ?: 0;

    $priority = ($_POST['priority'] ?? '') === 'high' ? 'high' : 'normal';

    if ($claimMissionId > 0) {
        $claimCheck = mysqli_prepare($db, "SELECT id FROM claims WHERE id = ? AND `status` = 'true'");
        $claimCheck->bind_param('i', $claimMissionId);
        $claimCheck->execute();
        $claimExists = $claimCheck->get_result()->num_rows === 1;
        $claimCheck->close();

        if (!$claimExists) {
            $errors[] = 'De bevestigde melding bestaat niet meer.';
        }
    }


    // Validatie
    if (count($goalsArray) < 1) {
        $errors[] = 'Selecteer minimaal één missiedoel.';
    }

    if (empty($sector)) {
        $errors[] = 'Selecteer een gebied / sector.';
    }


    // Tijden
    $startMinutes = (int) ($_POST['start_time'] ?? 0);
    $endMinutes = (int) ($_POST['end_time'] ?? 0);

    if ($startMinutes < 0 || $startMinutes >= 1440 || $endMinutes <= 0 || $endMinutes > 1440) {

        $errors[] = 'Kies geldige operationele tijden.';

    } elseif ($endMinutes <= $startMinutes) {

        $errors[] = 'De eindtijd moet later zijn dan de starttijd.';

    } else {

        $startTime = $today . ' ' . sprintf(
            '%02d:%02d:00',
            intdiv($startMinutes, 60),
            $startMinutes % 60
        );

        $endTime = $today . ' ' . sprintf(
            '%02d:%02d:00',
            intdiv($endMinutes, 60),
            $endMinutes % 60
        );

        if ($endMinutes === 1440) {
            $endTime = date('Y-m-d', strtotime($today . ' +1 day')) . ' 00:00:00';
        }
    }

    // Status van de doelen
    $states = [];

    foreach ($goalsArray as $goal) {
        $states[] = 'Gepland';
    }

    $goals = json_encode(
        $goalsArray,
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );

    $interventions = json_encode(
        $interventionsArray,
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );

    $states = json_encode(
        $states,
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );


    if (empty($errors)) {

        $claimTransactionStarted = $claimMissionId > 0 && !$editMissionId;
        if ($claimTransactionStarted) {
            mysqli_begin_transaction($db);
        }

        // Bestaande missie aanpassen
        if ($editMissionId) {

            $query = "UPDATE missions
                      SET area = ?, purpose = ?, interventions = ?, `start-time` = ?, `end-time` = ?, purpose_state = ?
                      WHERE id = ?";

            $result = mysqli_prepare($db, $query);

            $result->bind_param(
                'ssssssi',
                $sector,
                $goals,
                $interventions,
                $startTime,
                $endTime,
                $states,
                $editMissionId
            );

            $result->execute();
            $result->close();

            $missionId = $editMissionId;


            // Oude logboekregels van deze missie verwijderen
            $deleteLogQuery = "DELETE FROM logboek WHERE mission_id = ?";

            $deleteLogResult = mysqli_prepare($db, $deleteLogQuery);

            $deleteLogResult->bind_param(
                'i',
                $missionId
            );

            $deleteLogResult->execute();
            $deleteLogResult->close();

        } else {
            // Nieuwe missie toevoegen

            $query = "INSERT INTO missions
                      (area, purpose, interventions, `start-time`, `end-time`, purpose_state, mission_state, priority)
                      VALUES (?, ?, ?, ?, ?, ?, 'queued', ?)";

            $result = mysqli_prepare($db, $query);

            $result->bind_param(
                'sssssss',
                $sector,
                $goals,
                $interventions,
                $startTime,
                $endTime,
                $states,
                $priority
            );

            $result->execute();

            $missionId = mysqli_insert_id($db);

            $result->close();
        }


        // Logboek entry for new mission
        // if (!$editMissionId) {
            // Logboek
            $logQuery = "INSERT INTO logboek
                     (mission_id, activity, location, status)
                     VALUES (?, ?, ?, ?)";

            $logStatus = 'pending';


            // Elk missiedoel apart in het logboek
            foreach ($goalsArray as $goal) {

                $logActivity = $goal;

                $logResult = mysqli_prepare($db, $logQuery);

                $logResult->bind_param(
                    'isss',
                    $missionId,
                    $logActivity,
                    $sector,
                    $logStatus
                );

                $logResult->execute();
                $logResult->close();
            }


            // Elke interventie apart in het logboek
            foreach ($interventionsArray as $intervention) {

                $logActivity = $intervention;

                $logResult = mysqli_prepare($db, $logQuery);

                $logResult->bind_param(
                    'isss',
                    $missionId,
                    $logActivity,
                    $sector,
                    $logStatus
                );

                $logResult->execute();
                $logResult->close();
            }
        // }

        if ($claimTransactionStarted) {
            $deleteClaimQuery = "DELETE FROM claims WHERE id = ? AND `status` = 'true'";
            $deleteClaimResult = mysqli_prepare($db, $deleteClaimQuery);
            $deleteClaimResult->bind_param('i', $claimMissionId);
            $deleteClaimResult->execute();
            $claimDeleted = $deleteClaimResult->affected_rows === 1;
            $deleteClaimResult->close();

            if ($claimDeleted) {
                mysqli_commit($db);
            } else {
                mysqli_rollback($db);
                $errors[] = 'De melding kon niet worden verwerkt en is behouden.';
            }
        }

        if (empty($errors) && !$editMissionId && $priority === 'high') {
            try {
                activateMission($db, $missionId);
            } catch (Throwable $exception) {
                $errors[] = 'De high-priority missie kon niet worden geactiveerd.';
            }
        }

        if (empty($errors)) {
            header('Location: mission.php');
            exit();
        }
    }
}

// DELETE LOGIC
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && is_numeric($_GET['id'])) {

    $missionId = (int) $_GET['id'];


    // Eerst logboekregels verwijderen
    $deleteLogQuery = "DELETE FROM logboek WHERE mission_id = ?";

    $deleteLogResult = mysqli_prepare($db, $deleteLogQuery);

    $deleteLogResult->bind_param(
        'i',
        $missionId
    );

    $deleteLogResult->execute();
    $deleteLogResult->close();


    // Daarna missie verwijderen
    $deleteQuery = "DELETE FROM missions WHERE id = ?";

    $deleteResult = mysqli_prepare($db, $deleteQuery);

    $deleteResult->bind_param(
        'i',
        $missionId
    );

    $deleteResult->execute();
    $deleteResult->close();


    header("Location: mission.php");
    exit();
}


// Missie aanpassen
else if (
    isset($_GET["action"]) &&
    $_GET["action"] === "edit" &&
    isset($_GET["id"]) &&
    is_numeric($_GET["id"])
) {

    $missionId = (int) $_GET["id"];


    // Missie ophalen
    $fetchQuery = "SELECT * FROM missions WHERE id = ?";

    $fetchResult = mysqli_prepare($db, $fetchQuery);

    $fetchResult->bind_param(
        "i",
        $missionId
    );

    $fetchResult->execute();

    $missionData = $fetchResult
        ->get_result()
        ->fetch_assoc();

    $fetchResult->close();


    if ($missionData) {

        // Formulier invullen met bestaande gegevens
        $_POST["sector"] = $missionData["area"];

        $_POST["goals"] = json_decode(
            $missionData["purpose"],
            true
        );

        $_POST["interventions"] = json_decode(
            $missionData["interventions"],
            true
        );


        // Tijden terug omzetten naar minuten
        $startDateTime = new DateTime(
            $missionData["start-time"]
        );

        $endDateTime = new DateTime(
            $missionData["end-time"]
        );

        $_POST["start_time"] =
            ($startDateTime->format("H") * 60)
            + (int) $startDateTime->format("i");

        $_POST["end_time"] =
            $endDateTime->format('H:i') === '00:00'
            ? 1440
            : ($endDateTime->format("H") * 60)
            + (int) $endDateTime->format("i");

        $_POST['edit_id'] = (int) $missionData['id'];

    } else {

        header("Location: mission.php");
        exit();
    }
}


// Waarden voor formulier
$formSector = (string) ($_POST['sector'] ?? '');

$formGoals = is_array($_POST['goals'] ?? null)
    ? $_POST['goals']
    : [];

$formInterventions = is_array($_POST['interventions'] ?? null)
    ? $_POST['interventions']
    : [];

$formStartTime = (int) ($_POST['start_time'] ?? 360);

$formEndTime = (int) ($_POST['end_time'] ?? 1080);

$formEditId = (int) ($_POST['edit_id'] ?? 0);

$formPriority = ($_POST['priority'] ?? '') === 'high' ? 'high' : 'normal';


require_once('./partials/currentmission.php');

// Fetch the latest true notification from the claims table
$notificationQuery = "SELECT * FROM claims WHERE `status` = 'true' ORDER BY `timestamp` DESC LIMIT 1";
$notification = mysqli_query($db, $notificationQuery);
$notification = mysqli_fetch_assoc($notification);

?>









<!-- 
            frontend
-->








<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Firepatch - Missies</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/mission.css" />
    <script src="./js/mission.js" defer></script>
</head>

<body>
    <?php include("./partials/header.php"); ?>
    <div class="layout mission-layout">
        <?php include("./partials/sidebar.php"); ?>
        <main class="page">
            <?php if ($notification): ?>

                <section class="card notification">
                    <div class="content">
                        <div>
                            <h4>GEPLANDE INTERVENTIE</h4>
                            <h3><?= htmlspecialchars($notification['title'] ?? 'Geen titel', ENT_QUOTES, 'UTF-8') ?> -
                                VerifyNET</h3>
                            <p><?= htmlspecialchars($notification['description'] ?? 'Geen beschrijving', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <div class="alert-actions">
                            <a href="article.php?id=<?= htmlspecialchars($notification['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                class="btn ghost">check verifyNET</a>
                            <form method="POST">
                                <input type="hidden" name="action" value="prepare_mission">
                                <input type="hidden" name="claim_id" value="<?= (int) $notification['id'] ?>">
                                <button type="submit" class="btn primary">Stuur drone</button>
                            </form>
                        </div>
                    </div>
                    <div class="image-container">
                        <img src="<?= htmlspecialchars($notification['image_path'] ?? 'images/no-image.jpg', ENT_QUOTES, 'UTF-8') ?>"
                            alt="verifynet img">
                    </div>
                </section>
                <?php if ($urgentMissionSuggestion): ?>
                    <dialog class="urgent-mission-dialog" id="urgentMissionDialog">
                        <form method="dialog">
                            <h2>Urgente missie starten?</h2>
                            <p>Er is een melding met hoge prioriteit gevonden.</p>
                            <dl>
                                <div>
                                    <dt>Doel</dt>
                                    <dd><?= htmlspecialchars($urgentMissionSuggestion['goal'], ENT_QUOTES, 'UTF-8') ?></dd>
                                </div>
                                <div>
                                    <dt>Sector</dt>
                                    <dd><?= htmlspecialchars($urgentMissionSuggestion['sector'] ?: 'Handmatig kiezen', ENT_QUOTES, 'UTF-8') ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt>Duur</dt>
                                    <dd>2 uur</dd>
                                </div>
                            </dl>
                            <div class="urgent-mission-actions">
                                <button type="submit" value="adjust" class="btn ghost">Aanpassen</button>
                                <button type="submit" form="mission-form" name="submit" class="btn primary">Missie
                                    starten</button>
                            </div>
                        </form>
                    </dialog>
                <?php endif; ?>
            <?php endif; ?>


            <!-- CURRENT MISSION -->
            <?php if ($nextMission): ?>
                <section class="card mission">
                    <div class="current-mission buttons">
                        <a href="?action=edit&id=<?= $nextMission['id'] ?>" class="queue-button edit">Aanpassen</a>
                        <a href="?action=delete&id=<?= $nextMission['id'] ?>" class="queue-button delete">Verwijderen</a>
                    </div>
                    <h4>HUIDIGE MISSIE</h4>
                    <h2><?= htmlspecialchars($nextMission['area'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars(date('d-m-Y', strtotime($nextMission['start-time'])), ENT_QUOTES, 'UTF-8') ?>
                        <span><?= htmlspecialchars(date('H:i', strtotime($nextMission['start-time'])) . ' - ' . date('H:i', strtotime($nextMission['end-time'])), ENT_QUOTES, 'UTF-8') ?></span>
                    </p>
                    <div class="progress cyan">
                        <div style="width: <?= $timeProgress ?>%;"></div>
                    </div>
                    <p class="mission-progress">Missievoortgang <strong><?= floor($timeProgress) ?>%</strong></p>

                    <h3>Missiedoelen</h3>
                    <ul>
                        <?php for ($i = 0; $i < count($nextMission['purpose'] ?? []); $i++): ?>
                            <?php $goalState = $state[$i] ?? 'Gepland'; ?>
                            <li><?= htmlspecialchars($nextMission['purpose'][$i], ENT_QUOTES, 'UTF-8') ?><span
                                    class="<?= strtolower($goalState) ?>"><?= htmlspecialchars($goalState, ENT_QUOTES, 'UTF-8') ?></span>
                            </li>
                        <?php endfor; ?>
                    </ul>

                    <?php if (!empty($nextMission['interventions'])): ?>
                        <h3>Toegestane interventies</h3>
                        <ul>
                            <?php foreach ($nextMission['interventions'] as $intervention): ?>
                                <li><?= htmlspecialchars($intervention, ENT_QUOTES, 'UTF-8') ?><span>Toegestaan</span></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <section class="card mission empty-state">
                    <h4>MISSIES</h4>
                    <h2>Geen missies gepland</h2>
                    <p>Maak rechts een nieuwe missie aan om de planning te starten.</p>
                </section>
            <?php endif; ?>
            <!-- QUEUED MISSIONS -->
            <section class="card mission queue">
                <div class="queue-heading">
                    <h3>MISSIES IN DE QUEUE</h3>
                    <span><?= count($queuedMissions) ?></span>
                </div>

                <?php if ($queuedMissions): ?>
                    <div class="queue-list">
                        <?php foreach ($queuedMissions as $queuedMission): ?>
                            <article class="card queue-item">
                                <div class="item-header">
                                    <h4><?= htmlspecialchars($queuedMission['area'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <div>
                                        <p><?= htmlspecialchars(date('d-m-Y', strtotime($queuedMission['start-time'])), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <p>
                                            <?= htmlspecialchars(date('H:i', strtotime($queuedMission['start-time'])), ENT_QUOTES, 'UTF-8') ?>
                                            -
                                            <?= htmlspecialchars(date('H:i', strtotime($queuedMission['end-time'])), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>
                                </div>
                                <ul>
                                    <?php foreach ($queuedMission['purpose'] as $goal): ?>
                                        <li>- <?= htmlspecialchars($goal, ENT_QUOTES, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="buttons">
                                    <form method="POST" class="queue-activation-form">
                                        <input type="hidden" name="action" value="activate_mission">
                                        <input type="hidden" name="mission_id" value="<?= (int) $queuedMission['id'] ?>">
                                        <button type="submit" class="queue-button activate">Activeren</button>
                                    </form>
                                    <a href="?action=edit&id=<?= $queuedMission['id'] ?>" class="queue-button edit">Aanpassen</a>
                                    <a href="?action=delete&id=<?= $queuedMission['id'] ?>" class="queue-button delete">Verwijderen</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="queue-empty">Er staan geen andere missies in de queue.</p>
                <?php endif; ?>
            </section>
        </main>

        <aside class="rightbar mission-rightbar">
            <form class="mission-form" id="mission-form" method="POST">
                <div class="form-header">
                    <h3><?= $formEditId ? 'MISSIE AANPASSEN' : 'NIEUWE MISSIE INITIALISEREN' ?></h3>
                </div>

                <input type="hidden" name="edit_id" value="<?= $formEditId ?>">
                <input type="hidden" name="claim_id" value="<?= $claimMissionId ?>">
                <input type="hidden" name="priority" value="<?= htmlspecialchars($formPriority, ENT_QUOTES, 'UTF-8') ?>">

                <?php if ($claimMissionMessage): ?>
                    <div class="form-message" role="status">
                        <?= htmlspecialchars($claimMissionMessage, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="form-errors" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="form-field">
                    <label for="sector-select">Selecteer Gebied / Sector</label>
                    <select id="sector-select" name="sector" class="mission-select">
                        <!-- onfocus="this.size=10;" onblur="this.size=1;" onchange="this.size=1; this.blur();" -->
                        <?php for ($i = 1; $i <= 36; $i++): ?>
                            <?php $sectorOption = "Section $i"; ?>
                            <option value="<?= $sectorOption ?>" <?= $formSector === $sectorOption ? 'selected' : '' ?>>
                                <?= $sectorOption ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label>Missie Doelen</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" name="goals[]" value="Planten scannen" <?= in_array('Planten scannen', $formGoals, true) ? 'checked' : '' ?>> Planten
                                scannen </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Grondvruchtbaarheid meten"
                                    <?= in_array('Grondvruchtbaarheid meten', $formGoals, true) ? 'checked' : '' ?>>
                                Grondvruchtbaarheid meten </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Dieren monitoren" <?= in_array('Dieren monitoren', $formGoals, true) ? 'checked' : '' ?>> Dieren monitoren
                            </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Lichtlevels controleren"
                                    <?= in_array('Lichtlevels controleren', $formGoals, true) ? 'checked' : '' ?>>
                                Lichtlevels
                                controleren </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Inspectie uitvoeren"
                                    <?= in_array('Inspectie uitvoeren', $formGoals, true) ? 'checked' : '' ?>> Inspectie
                                uitvoeren</label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Brand blussen" <?= in_array('Brand blussen', $formGoals, true) ? 'checked' : '' ?>> Brand blussen</label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Afval opruimen" <?= in_array('Afval opruimen', $formGoals, true) ? 'checked' : '' ?>> Afval opruimen</label></li>
                        <!-- <span>+add new goal</span> -->
                        <!-- <li><label><input type="checkbox" name="goals[]" value="Brand blussen" <?= in_array('Brand blussen', $formGoals, true) ? 'checked' : '' ?>> Brand blussen
                            </label></li> -->
                    </ul>
                </div>

                <div class="form-field">
                    <label>Interventies Toestaan</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" name="interventions[]" value="Water geven aan planten"
                                    <?= in_array('Water geven aan planten', $formInterventions, true) ? 'checked' : '' ?>>
                                Water geven aan planten </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Grond bemesten"
                                    <?= in_array('Grond bemesten', $formInterventions, true) ? 'checked' : '' ?>>
                                Grond bemesten </label></li>
                        <li><label><input type="checkbox" name="interventions[]"
                                    value="Invasieve dierensoorten verwijderen" <?= in_array('Invasieve dierensoorten verwijderen', $formInterventions, true) ? 'checked' : '' ?>>
                                Invasieve dierensoorten verwijderen </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Onkruid verwijderen"
                                    <?= in_array('Onkruid verwijderen', $formInterventions, true) ? 'checked' : '' ?>>
                                Onkruid verwijderen </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Brand blussen"
                                    <?= in_array('Brand blussen', $formInterventions, true) ? 'checked' : '' ?>> Brand
                                blussen</label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Afval opruimen"
                                    <?= in_array('Afval opruimen', $formInterventions, true) ? 'checked' : '' ?>> Afval
                                opruimen</label></li>
                    </ul>
                </div>

                <div class="form-field time-field">
                    <label>Actieve Operationele Tijden</label>
                    <div class="time-row">
                        <span id="startLabel">08:00</span>
                        <span id="endLabel">18:00</span>
                    </div>
                    <div class="slider">
                        <div class="slider-track"></div>
                        <div class="slider-range" id="range"></div>

                        <input id="start" name="start_time" type="range" min="0" max="1440" step="15"
                            value="<?= $formStartTime ?>">

                        <input id="end" name="end_time" type="range" min="0" max="1440" step="15"
                            value="<?= $formEndTime ?>">
                    </div>
                </div>

                <!-- <ul class="checklist compact">
                    <li><label><input type="checkbox" name="adapt_to_weather" value="1" checked> Aanpassen aan weer (bijv. regen/windvlagen)</label></li>
                    <li><label><input type="checkbox" name="adapt_to_animal_activity" value="1" checked> Aanpassen aan dierenactiviteit (nacht/rusttijden)</label>
                    </li>
                </ul> -->

                <button type="submit" name="submit"
                    class="mission-submit"><?= $formEditId ? 'MISSIE AANPASSEN' : 'MISSIE STARTEN' ?></button>
            </form>

        </aside>
    </div>
</body>

</html>