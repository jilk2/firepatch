<!doctype html>
<html lang="nl">

<head>    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NatureGuard Dashboard - Overzicht</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/kaart.css" />
    <link rel="stylesheet" href="./css/mission.css" />
    <script src="./js/main.js" defer></script>
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
                    <img id="map" src="image/map.png" alt="map of the region">
                </div>
            </section>

            <section class="alert-card">
                <div>
                    <h3>⚠ ACTIE GEVRAAGD</h3>
                    <p>Brand gedetecteerd in Sector <?php echo "4"?>.</p>
                </div>
                <div class="page-actions">
                    <a href="mission.php" class="btn primary">Bekijk</a>
                </div>
            </section>

            <section class="card">
                <div class="card-head">
                    <h2>Systeem Logboek (Live)</h2>
                    <span class="text-muted">Frequentie: Realtime</span>
                </div>
                <ul class="log-list">
                    <li><span>14:26</span>Water succesvol toegediend op droge coördinaten in Sector 04.<em>VOLTOOID</em>
                    </li>
                    <li><span>14:20</span>3 Bewaterings mini-drones succesvol ingezet vanaf ranger
                        post.<em>VOLTOOID</em></li>
                    <li><span>14:15</span>Automatisch verzoek mini-drones inzetten gegenereerd.<em class="pending">IN
                            AFWACHTING</em></li>
                    <li><span>14:14</span>Kritieke lage bodemvochtigheid (12%) gedetecteerd door sensor
                        matrix.<em>VOLTOOID</em></li>
                </ul>
            </section>
        </main>

        <aside class="rightbar">


            <section class="card mission">
                <h4>HUIDIGE MISSIE</h4>
                <h2>Ecosysteemscan</h2>
                <p>Missievoortgang <strong>66% Voltooid</strong></p>
                <div class="progress cyan">
                    <div style="width: 66%"></div>
                </div>
                <ul>
                    <li>Biomassascan <span>Gereed</span></li>
                    <li>Wateranalyse <span>Gereed</span></li>
                    <li class="active">Inventarisatie <span>Actief</span></li>
                </ul>
            </section>
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
    <script src="js/map.js"></script>
</body>

</html>