<?php

require_once('./DB/DBConnect.php');

date_default_timezone_set('Europe/Amsterdam');
$today = date('Y-m-d');
$errors = [];
$editMissionId = null;

//add to $db the values from input fields
if (isset($_POST['submit'])) {
    $editMissionId = filter_var($_POST['edit_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]) ?: null;
    $sector = trim((string) ($_POST['sector'] ?? ''));
    $goalsArray = is_array($_POST['goals'] ?? null) ? $_POST['goals'] : [];
    $interventionsArray = is_array($_POST['interventions'] ?? null) ? $_POST['interventions'] : [];

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
        $startTime = $today . ' ' . sprintf('%02d:%02d:00', intdiv($startMinutes, 60), $startMinutes % 60);
        $endTime = $today . ' ' . sprintf('%02d:%02d:00', intdiv($endMinutes, 60), $endMinutes % 60);

        if ($endMinutes === 1440) {
            $endTime = date('Y-m-d', strtotime($today . ' +1 day')) . ' 00:00:00';
        }

        $now = new DateTimeImmutable();
        $startDateTime = new DateTimeImmutable($startTime);
        $endDateTime = new DateTimeImmutable($endTime);

        // if ($startDateTime <= $now) {
        //     $errors[] = 'De starttijd moet later zijn dan de huidige tijd.';
        // }
    }

    $states = [];
    foreach ($goalsArray as $goal) {
        $states[] = 'Gepland';
    }
    $goals = json_encode($goalsArray);
    $interventions = json_encode($interventionsArray);
    $states = json_encode($states);

    if (empty($errors)) {
        if ($editMissionId) {
            $query = "UPDATE missions SET area = ?, purpose = ?, interventions = ?, `start-time` = ?, `end-time` = ?, state = ? WHERE id = ?";
        } else {
            $query = "INSERT INTO missions (area, purpose, interventions, `start-time`, `end-time`, state) VALUES (?, ?, ?, ?, ?, ?)";
        }

        $result = mysqli_prepare($db, $query);
        if ($editMissionId) {
            $result->bind_param('ssssssi', $sector, $goals, $interventions, $startTime, $endTime, $states, $editMissionId);
        } else {
            $result->bind_param('ssssss', $sector, $goals, $interventions, $startTime, $endTime, $states);
        }
        $result->execute();
        $result->close();

        if (!$editMissionId) {
            $logQuery = "INSERT INTO logboek (type, activity, location, status) VALUES (?, ?, ?, ?)";
            $logResult = mysqli_prepare($db, $logQuery);
            $logType = implode(', ', $interventionsArray);
            $logActivity = implode(', ', $goalsArray);
            $logStatus = 'active';
            $logResult->bind_param('ssss', $logType, $logActivity, $sector, $logStatus);
            $logResult->execute();
            $logResult->close();
        }

        header('Location: mission.php');
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $missionId = $_GET['id'];

    // Delete the mission from the database
    $deleteQuery = "DELETE FROM missions WHERE id = ?";
    $deleteResult = mysqli_prepare($db, $deleteQuery);
    $deleteResult->bind_param('i', $missionId);
    $deleteResult->execute();
    $deleteResult->close();

    // Optionally, you can also delete related logs if needed
    // $deleteLogQuery = "DELETE FROM logboek WHERE mission_id = ?";
    // $deleteLogResult = mysqli_prepare($db, $deleteLogQuery);
    // $deleteLogResult->bind_param('i', $missionId);
    // $deleteLogResult->execute();
    // $deleteLogResult->close();

    // Redirect to avoid resubmission
    header("Location: mission.php");
    exit();
} else if (isset($_GET["action"]) && $_GET["action"] === "edit" && isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $missionId = $_GET["id"];

    // Fetch the mission data from the database
    $fetchQuery = "SELECT * FROM missions WHERE id = ?";
    $fetchResult = mysqli_prepare($db, $fetchQuery);
    $fetchResult->bind_param("i", $missionId);
    $fetchResult->execute();
    $missionData = $fetchResult->get_result()->fetch_assoc();
    $fetchResult->close();

    if ($missionData) {
        // Pre-fill the form with the existing mission data
        $_POST["sector"] = $missionData["area"];
        $_POST["goals"] = json_decode($missionData["purpose"], true);
        $_POST["interventions"] = json_decode($missionData["interventions"], true);

        // Convert start and end times to minutes for the slider
        $startDateTime = new DateTime($missionData["start-time"]);
        $endDateTime = new DateTime($missionData["end-time"]);
        $_POST["start_time"] = ($startDateTime->format("H") * 60) + (int)$startDateTime->format("i");
        $_POST["end_time"] = $endDateTime->format('H:i') === '00:00'
            ? 1440
            : ($endDateTime->format("H") * 60) + (int) $endDateTime->format("i");
        $_POST['edit_id'] = (int) $missionData['id'];
    } else {
        // If the mission doesn't exist, redirect back to the mission page
        header("Location: mission.php");
        exit();
    }
}

$formSector = (string) ($_POST['sector'] ?? '');
$formGoals = is_array($_POST['goals'] ?? null) ? $_POST['goals'] : [];
$formInterventions = is_array($_POST['interventions'] ?? null) ? $_POST['interventions'] : [];
$formStartTime = (int) ($_POST['start_time'] ?? 360);
$formEndTime = (int) ($_POST['end_time'] ?? 1080);
$formEditId = (int) ($_POST['edit_id'] ?? 0);

require_once('./partials/currentmission.php');

?>

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
            <section class="card notification">
                <div class="content">
                    <div>
                        <h4>GEPLANDE INTERVENTIE</h4>
                        <h3>Brand gedetecteerd - VerifyNET</h3>
                        <p>Brand gedetecteerd door 4 mensen in Sector 04</p>
                    </div>
                    <div class="alert-actions">
                        <a href="#" class="btn ghost">check verifyNET</a>
                        <a href="#" class="btn primary">Stuur drone</a>
                    </div>
                </div>
                <div class="image-container">
                    <img src="images/brandje.jpg" alt="verifynet img">
                </div>
            </section>

            <!-- CURRENT MISSION -->
            <?php if ($nextMission): ?>
                <section class="card mission">
                    <div class="current-mission buttons">
                        <a href="?action=edit&id=<?= $nextMission['id'] ?>" class="queue-button">Aanpassen</a>
                        <a href="?action=delete&id=<?= $nextMission['id'] ?>" class="queue-button">Verwijder</a>
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
                            <li><?= htmlspecialchars($nextMission['purpose'][$i], ENT_QUOTES, 'UTF-8') ?><span
                                    class="<?= strtolower($state[$i]) ?>"><?= $state[$i] ?></span></li>
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
                                    <a href="#" class="queue-button">Activeer nu</a>
                                    <a href="?action=edit&id=<?= $queuedMission['id'] ?>" class="queue-button">Aanpassen</a>
                                    <a href="?action=delete&id=<?= $queuedMission['id'] ?>" class="queue-button">Verwijder</a>
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
            <form class="mission-form" method="POST">
                <div class="form-header">
                    <h3><?= $formEditId ? 'MISSIE AANPASSEN' : 'NIEUWE MISSIE INITIALISEREN' ?></h3>
                </div>

                <input type="hidden" name="edit_id" value="<?= $formEditId ?>">

                <?php if ($errors): ?>
                    <div class="form-errors" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="form-field">
                    <label for="sector-select">Selecteer Gebied / Sector</label>
                    <select id="sector-select" name="sector" class="mission-select" > <!-- onfocus="this.size=10;" onblur="this.size=1;" onchange="this.size=1; this.blur();" -->
                        <?php for ($i = 1; $i <= 36; $i++): ?>
                            <?php $sectorOption = "Section $i"; ?>
                            <option value="<?= $sectorOption ?>" <?= $formSector === $sectorOption ? 'selected' : '' ?>><?= $sectorOption ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label>Missie Doelen</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" name="goals[]" value="Planten scannen" <?= in_array('Planten scannen', $formGoals, true) ? 'checked' : '' ?>> Planten
                                scannen </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Grondvruchtbaarheid meten" <?= in_array('Grondvruchtbaarheid meten', $formGoals, true) ? 'checked' : '' ?>>
                                Grondvruchtbaarheid meten </label></li>
                            <li><label><input type="checkbox" name="goals[]" value="Dieren monitoren" <?= in_array('Dieren monitoren', $formGoals, true) ? 'checked' : '' ?>> Dieren monitoren
                            </label></li>
                            <li><label><input type="checkbox" name="goals[]" value="Lichtlevels controleren" <?= in_array('Lichtlevels controleren', $formGoals, true) ? 'checked' : '' ?>> Lichtlevels
                                controleren </label></li>
                    </ul>
                </div>

                <div class="form-field">
                    <label>Interventies Toestaan</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" name="interventions[]" value="Water geven aan planten" <?= in_array('Water geven aan planten', $formInterventions, true) ? 'checked' : '' ?>> 
                        Water geven aan planten </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Grond bemesten" <?= in_array('Grond bemesten', $formInterventions, true) ? 'checked' : '' ?>> 
                        Grond bemesten </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Invasieve dierensoorten verwijderen" <?= in_array('Invasieve dierensoorten verwijderen', $formInterventions, true) ? 'checked' : '' ?>> 
                        Invasieve dierensoorten verwijderen </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Onkruid verwijderen" <?= in_array('Onkruid verwijderen', $formInterventions, true) ? 'checked' : '' ?>> 
                        Onkruid verwijderen </label></li>
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

                        <input id="start" name="start_time" type="range" min="0" max="1440" step="15" value="<?= $formStartTime ?>">

                        <input id="end" name="end_time" type="range" min="0" max="1440" step="15" value="<?= $formEndTime ?>">
                    </div>
                </div>

                <!-- <ul class="checklist compact">
                    <li><label><input type="checkbox" name="adapt_to_weather" value="1" checked> Aanpassen aan weer (bijv. regen/windvlagen)</label></li>
                    <li><label><input type="checkbox" name="adapt_to_animal_activity" value="1" checked> Aanpassen aan dierenactiviteit (nacht/rusttijden)</label>
                    </li>
                </ul> -->

                <button type="submit" name="submit" class="mission-submit"><?= $formEditId ? 'MISSIE AANPASSEN' : 'MISSIE STARTEN' ?></button>
            </form>

        </aside>
    </div>
</body>

</html>