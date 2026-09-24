<?php
require_once('./DB/DBConnect.php');
date_default_timezone_set('Europe/Amsterdam');

//add to $db the values from input fields
if (isset($_POST['submit'])) {
    $sector = $_POST['sector'] ?? '';
    $goals = json_encode($_POST['goals'] ?? []);
    $interventions = json_encode($_POST['interventions'] ?? []);
    $startMinutes = (int) ($_POST['start_time'] ?? 0);
    $endMinutes = (int) ($_POST['end_time'] ?? 0);
    $startTime = date('Y-m-d') . ' ' . sprintf('%02d:%02d:00', intdiv($startMinutes, 60), $startMinutes % 60);
    $endTime = date('Y-m-d') . ' ' . sprintf('%02d:%02d:00', intdiv($endMinutes, 60), $endMinutes % 60);
    //Bijv:     2026-9-23          %d-> int, 02-> 2cijfers met 0 vooraf  360/60 = 6(uur)    360%60 = 0(minuten)     geeft 2026-09-23 06:00:00

    $query = "INSERT INTO missions (area, purpose, interventions, `start-time`, `end-time`) VALUES (?, ?, ?, ?, ?)";
    $result = mysqli_prepare($db, $query);
    $result->bind_param('sssss', $sector, $goals, $interventions, $startTime, $endTime);
    $result->execute();
}

$query = "SELECT * FROM missions ORDER BY `start-time` ASC";
$result = mysqli_prepare($db, $query);
$result->execute();
$result = $result->get_result();
$missions = [];
while ($row = $result->fetch_assoc()) {
    $row['purpose'] = json_decode($row['purpose']);
    $row['interventions'] = json_decode($row['interventions']);
    $missions[] = $row;
}
$nextMission = $missions[0] ?? null;
$queuedMissions = array_slice($missions, 1);
?>

<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NatureGuard | Missies</title>
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
                    <h4>EERSTVOLGENDE MISSIE</h4>
                    <h2><?= htmlspecialchars($nextMission['area'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars(date('d-m-Y', strtotime($nextMission['start-time'])), ENT_QUOTES, 'UTF-8') ?> 
                        <span><?= htmlspecialchars(date('H:i', strtotime($nextMission['start-time'])) . ' - ' . date('H:i', strtotime($nextMission['end-time'])), ENT_QUOTES, 'UTF-8') ?></span></p>
                    <div class="progress cyan">
                        <div style="width: 40%;"></div>
                    </div>
                    <p class="mission-progress">Missievoortgang <strong>40%</strong></p>

                    <h3>Missiedoelen</h3>
                    <ul>
                        <?php foreach ($nextMission['purpose'] ?? [] as $goal): 
                            $state = htmlspecialchars($nextMission['state'], ENT_QUOTES, 'UTF-8'); ?>
                            <li><?= htmlspecialchars($goal, ENT_QUOTES, 'UTF-8') ?><span class="<?= strtolower($state) ?>"><?= $state ?></span></li>
                        <?php endforeach; ?>
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

            <section class="mission-queue">
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
            </section>
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