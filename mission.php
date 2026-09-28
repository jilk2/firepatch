<?php

require_once __DIR__ . '/config/database.php';

date_default_timezone_set('Europe/Amsterdam');
$today = date('Y-m-d');

//add to $db the values from input fields
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $sector = trim((string) ($_POST['sector'] ?? ''));

    $goalsArray = array_values(array_filter(
        (array) ($_POST['goals'] ?? []),
        static fn ($goal): bool => is_string($goal) && trim($goal) !== ''
    ));

    $interventionsArray = array_values(array_filter(
        (array) ($_POST['interventions'] ?? []),
        static fn ($intervention): bool => is_string($intervention) && trim($intervention) !== ''
    ));



    // Tijden

    $startMinutes = (int) ($_POST['start_time'] ?? 0);

    $endMinutes = (int) ($_POST['end_time'] ?? 0);

    $startMinutes = max(0, min(1440, $startMinutes));
    $endMinutes = max(0, min(1440, $endMinutes));

    if ($sector === '' || $goalsArray === [] || $endMinutes <= $startMinutes) {
        http_response_code(422);
        exit('Kies een sector, minimaal een missiedoel en een eindtijd na de starttijd.');
    }
    $dayStart = new DateTimeImmutable($today . ' 00:00:00');
    $startTime = $dayStart->modify('+' . $startMinutes . ' minutes')->format('Y-m-d H:i:s');
    $endTime = $dayStart->modify('+' . $endMinutes . ' minutes')->format('Y-m-d H:i:s');
    $states = [];
    foreach ($goalsArray as $goal) {
        $states[] = 'Gepland';
    }
    $goals = json_encode($goalsArray, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $interventions = json_encode($interventionsArray, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $states = json_encode($states, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);


    $query = "INSERT INTO missions (area, purpose, interventions, `start-time`, `end-time`, state) VALUES (?, ?, ?, ?, ?, ?)";
    $result = mysqli_prepare($db, $query);


    $result->bind_param(
        'ssssss',
        $sector,
        $goals,
        $interventions,
        $startTime,
        $endTime,
        $states
    );


    $result->execute();
    $result->close();




    /* =====================================================
       LOGBOEK TOEVOEGEN
    ===================================================== */


    // Interventies worden het type

    $logType =
        implode(", ", $interventionsArray);


    // Doelen worden de activiteit

    $logActivity =
        implode(", ", $goalsArray);


    // Sector wordt locatie

    $logLocation = $sector;


    // Missie is bezig

    $logStatus = "active";


    $logQuery = "
        INSERT INTO logboek
        (
            type,
            activity,
            location,
            status
        )
        VALUES (?, ?, ?, ?)
    ";


    $logResult =
        mysqli_prepare($db, $logQuery);


    $logResult->bind_param(
        'ssss',
        $logType,
        $logActivity,
        $logLocation,
        $logStatus
    );


    $logResult->execute();

    $logResult->close();

}

require_once __DIR__ . '/partials/currentmission.php';

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
            <?php if ($nextMission): ?>
                <section class="card mission">
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
                                    class="<?= strtolower($goalState) ?>"><?= htmlspecialchars($goalState, ENT_QUOTES, 'UTF-8') ?></span></li>
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

            <section class="card queue">
                <div class="content">
                    <div>
                        <h4>GEPLANDE INTERVENTIE</h4>
                        <h3>Brand gedetecteerd - VerifyNET</h3>
                        <p>Brand gedetecteerd door 4 mensen op 51°56'31.2"N - 4°31'10.1"E</p>
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

            <!-- <section class="mission-queue">
                <div class="queue-heading">
                    <h3>MISSIES IN DE QUEUE</h3>
                    <span><?= count($queuedMissions) ?></span>
                </div>

                <?php if ($queuedMissions): ?>
                    <div class="queue-list">
                        <?php foreach ($queuedMissions as $queuedMission): ?>
                            <article class="card queue-item">
                                <div>
                                    <h4><?= htmlspecialchars($queuedMission['area'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <p><?= htmlspecialchars(date('d-m-Y H:i', strtotime($queuedMission['start-time'])), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars(date('H:i', strtotime($queuedMission['end-time'])), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <span class="queue-status">In queue</span>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="queue-empty">Er staan geen andere missies in de queue.</p>
                <?php endif; ?>
            </section> -->
        </main>

        <aside class="rightbar mission-rightbar">
            <form class="mission-form" method="POST">
                <div class="form-header">
                    <h3>NIEUWE MISSIE INITIALISEREN</h3>
                </div>

                <div class="form-field">
                    <label for="sector-select">Selecteer Gebied / Sector</label>
                    <select id="sector-select" name="sector" class="mission-select">
                        <option value="Kralingse Bos">Kralingse Bos</option>
                    </select>
                </div>

                <div class="form-field">
                    <label>Missie Doelen</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" name="goals[]" value="Planten scannen" checked> Planten
                                scannen </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Grondvruchtbaarheid meten" checked>
                                Grondvruchtbaarheid meten </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Dieren monitoren"> Dieren monitoren
                            </label></li>
                        <li><label><input type="checkbox" name="goals[]" value="Lichtlevels controleren"> Lichtlevels
                                controleren </label></li>
                    </ul>
                </div>

                <div class="form-field">
                    <label>Interventies Toestaan</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" name="interventions[]" value="Water geven aan planten"
                                    checked> Water geven aan planten </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Grond bemesten"> Grond bemesten
                            </label></li>
                        <li><label><input type="checkbox" name="interventions[]"
                                    value="Invasieve dierensoorten verwijderen"> Invasieve dierensoorten verwijderen
                            </label></li>
                        <li><label><input type="checkbox" name="interventions[]" value="Onkruid verwijderen"> Onkruid
                                verwijderen </label></li>
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

                        <input id="start" name="start_time" type="range" min="0" max="1440" step="15" value="360">

                        <input id="end" name="end_time" type="range" min="0" max="1440" step="15" value="1080">
                    </div>
                </div>

                <!-- <ul class="checklist compact">
                    <li><label><input type="checkbox" name="adapt_to_weather" value="1" checked> Aanpassen aan weer (bijv. regen/windvlagen)</label></li>
                    <li><label><input type="checkbox" name="adapt_to_animal_activity" value="1" checked> Aanpassen aan dierenactiviteit (nacht/rusttijden)</label>
                    </li>
                </ul> -->

                <button type="submit" name="submit" class="mission-submit">MISSIE STARTEN</button>
            </form>
        </aside>
    </div>
</body>

</html>
