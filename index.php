<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NatureGuard Dashboard - Overzicht</title>
    <link rel="stylesheet" href="./css/main.css" />
    <script src="./js/main.js" defer></script>
</head>

<body>
    <?php include("./partials/header.php"); ?>
    <div class="layout">
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
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d25534.453872819813!2d6.010588891485108!3d52.04765963410813!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sen!2snl!4v1788955259685!5m2!1sen!2snl"
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    <div class="marker m1">Sector 01</div>
                    <div class="marker m2">Sector 02</div>
                    <div class="marker m3">Sector 03</div>
                    <div class="marker m4">Sector 04</div>
                </div>
            </section>

            <section class="alert-card">
                <div>
                    <h3>⚠ ACTIE GEVRAAGD</h3>
                    <p>Droogte gedetecteerd in Sector 04.</p>
                    <p>Aanbevolen: 3 deployment drones inzetten ter bewatering.</p>
                </div>
                <div class="page-actions">
                    <a href="#" class="btn">Aanpassen</a>
                    <a href="#" class="btn primary">Goedkeuren</a>
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
            <section class="card">
                <div class="card-head">
                    <h2>Drone Status</h2>
                    <span class="drone-state">Actief</span>
                </div>
                <div class="stats">
                    <p>Huidige Zone <strong>Sector 03</strong></p>
                    <p>Accuniveau <strong>78%</strong></p>
                    <div class="progress">
                        <div style="width: 78%"></div>
                    </div>
                    <p>Verwachte Terugkomst <strong>16:42 uur</strong></p>
                    <p>Sub-drones Actief <strong>3 eenheden</strong></p>
                </div>
            </section>

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
        </aside>
    </div>
</body>

</html>