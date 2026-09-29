<?php
//session_start();
//if(!isset($_SESSION['username'])){
//    header('Location: login.php');
//    exit;
//}
require_once __DIR__ . '/config/database.php';
date_default_timezone_set('Europe/Amsterdam');
$today = date('Y-m-d');

require_once __DIR__ . '/partials/currentmission.php';


// Logboek gegevens ophalen

$query = "SELECT * FROM logboek ORDER BY created_at DESC LIMIT 5";

$result = mysqli_query($db, $query);

$logs = [];

while ($row = mysqli_fetch_assoc($result)) {
    $logs[] = $row;
}

?>



<!doctype html>
<html lang="nl">

<head>    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Firepatch - Dashboard</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/kaart.css" />
    <link rel="stylesheet" href="./css/mission.css" />
    <script src="js/map.js" defer></script>
</head>

<body>
    <?php include("./partials/header.php"); ?>
    <div class="layout dashboard-layout">
        <?php include("./partials/sidebar.php"); ?>
        <main class="page">
            <section class="card">
                <div class="card-head">
                    <h2>Live Kaart - Actieve Patrouilles</h2>
                    <div class="map-badges">
                        <span>RTR-03 Gekoppeld</span>
                        <span class="badge">SAT-VIEW V4</span>
                    </div>
                </div>
                <div class="map">
                    <img id="map" src="images/map.png" alt="map of the region">
                    <img id="vlam" src="images/vlammetjes-6-st.jpg" alt="flames">
                    <img id="drone" src="images/drone.png" alt="Drone">
                </div>
            </section>

            <section class="alert-card" id="claim-alert" hidden>
                <div>
                    <h3>⚠ ACTIE GEVRAAGD</h3>
                    <p id="claim-alert-message"></p>
                </div>
                <div class="page-actions">
                    <a href="article.php" id="claim-alert-link" class="btn primary">Bekijk</a>
                </div>
            </section>

            <section class="card">

                <div class="card-head">

                    <h2>Systeem Logboek (Live)</h2>

                    <span class="text-muted">
                        Frequentie: Realtime
                    </span>

                </div>


                <ul class="log-list">

                    <?php foreach ($logs as $log): ?>

                        <li>

                            <span>
                                <?= date(
                                    'H:i',
                                    strtotime($log['created_at'])
                                ) ?>
                            </span>

                            <?= htmlspecialchars($log['activity']) ?>

                            <?php if ($log['status'] === 'done'): ?>

                                <em>
                                    VOLTOOID
                                </em>

                            <?php elseif ($log['status'] === 'pending'): ?>

                                <em class="pending">
                                    IN AFWACHTING
                                </em>

                            <?php elseif ($log['status'] === 'active'): ?>

                                <em>
                                    MONITORING
                                </em>

                            <?php endif; ?>

                        </li>

                    <?php endforeach; ?>

                </ul>

            </section>
        </main>

        <aside class="rightbar">

            <?php if ($nextMission): ?>
                <section class="card mission">
                    <h4>HUIDIGE MISSIE</h4>
                    <h2><?= htmlspecialchars($nextMission['area'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <!-- <p><?= htmlspecialchars(date('d-m-Y', strtotime($nextMission['start-time'])), ENT_QUOTES, 'UTF-8') ?>
                    <span><?= htmlspecialchars(date('H:i', strtotime($nextMission['start-time'])) . ' - ' . date('H:i', strtotime($nextMission['end-time'])), ENT_QUOTES, 'UTF-8') ?></span>
                </p> -->
                    <p class="mission-progress">Missievoortgang <strong><?= floor($timeProgress) ?>%</strong></p>
                    <div class="progress cyan">
                        <div style="width: <?= $timeProgress ?>%;"></div>
                    </div>
                    <ul>
                        <?php for ($i = 0; $i < count($nextMission['purpose'] ?? []); $i++): ?>
                            <li>
                                <?= htmlspecialchars($nextMission['purpose'][$i], ENT_QUOTES, 'UTF-8') ?><span
                                    class="<?= strtolower($state[$i]) ?>">
                                    <?= $state[$i] ?>
                                </span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </section>
            <?php else: ?>
                <section class="card mission empty-state">
                    <h4>MISSIES</h4>
                    <h2>Geen missies gepland</h2>
                    <p>Maak rechts een nieuwe missie aan om de planning te starten.</p>
                </section>
            <?php endif; ?>

            <section class="card">
                <div class="card-head">
                    <h2>Drone Status</h2>
                    <span class="drone-state">Actief</span>
                </div>
                <div class="stats">
                    <p>Accuniveau <strong>78%</strong></p>
                    <div class="progress">
                        <div style="width: 78%"></div>
                    </div>
                    <p>Verwachte Terugkomst <strong>16:42 uur</strong></p>
                    <p>Signaalsterkte <strong>98%</strong></p>
                </div>
            </section>
        </aside>
    </div>
    <script src="./js/dashboard-alert.js"></script>
</body>

</html>
