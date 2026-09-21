<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NatureGuard | Missies</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/mission.css" />
</head>

<body>
    <?php include("./partials/header.php"); ?>
    <div class="layout mission-layout">
        <?php include("./partials/sidebar.php"); ?>
        <main class="page">
            <section class="card mission">
                <h4>HUIDIGE MISSIE - RUNNING</h4>
                <h2>Ecosysteemscan - Sector 03</h2>
                <p>Missievoortgang <strong>66% Voltooid</strong></p> <!-- DEZE WERKT NOG NIET -->
                <div class="progress cyan">
                    <div style="width: 66%;"></div>
                </div>
                <ul>
                    <li>Biomassascan <span>Gereed</span></li>
                    <li>Wateranalyse <span>Gereed</span></li>
                    <li class="active">Inventarisatie <span>Actief</span></li>
                </ul>
            </section>

            <section class="card queue">
                <div>
                    <h4>GEPLANDE INTERVENTIE</h4>
                    <h3>Droogte interventie - Sector 04</h3>
                    <p>Wachten op goedkeuring of start-trigger. Gepland met 3 mini-E drones
                        ter gerichte irrigatie van droogtestress-hotspots.</p>
                </div>
                <div class="alert-actions">
                    <a href="#" class="btn ghost">Aanpassen</a>
                    <a href="#" class="btn primary">Goedkeuren</a>
                </div>
            </section>
        </main>

        <aside class="rightbar mission-rightbar">
            <form class="mission-form">
                <div class="form-header">
                    <h3>NIEUWE MISSIE INITIALISEREN</h3>
                </div>

                <div class="form-field">
                    <label for="sector-select">Selecteer Gebied / Sector</label>
                    <select id="sector-select" class="mission-select">
                        <option>Sector 04 (Veluwe-Oost)</option>
                        <option>Sector 03 (Veluwe-Midden)</option>
                        <option>Sector 02 (Veluwe-Noord)</option>
                    </select>
                </div>

                <div class="form-field">
                    <label>Missie Doelen</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" checked> Ecosysteem monitoren</label></li>
                        <li><label><input type="checkbox" checked> Droogte detecteren</label></li>
                        <li><label><input type="checkbox"> Dieren monitoren</label></li>
                        <li><label><input type="checkbox"> Biodiversiteit meten</label></li>
                    </ul>
                </div>

                <div class="form-field">
                    <label>Interventies Toestaan</label>
                    <ul class="checklist">
                        <li><label><input type="checkbox" checked> Water geven (irrigatie-droplink)</label></li>
                        <li><label><input type="checkbox" checked> Temperatuur begeleiden (neveling)</label></li>
                        <li><label><input type="checkbox"> Planten handmatig verwijderen (exoten)</label></li>
                    </ul>
                </div>

                <div class="form-field time-field">
                    <label>Actieve Operationele Tijden</label>
                    <div class="time-row">
                        <span>06:00</span>
                        <span>18:00</span>
                    </div>
                    <input type="range" class="time-range" min="0" max="100" value="50" aria-label="Actieve operationele tijden">
                </div>

                <ul class="checklist compact">
                    <li><label><input type="checkbox" checked> Aanpassen aan weer (bijv. regen/windvlagen)</label></li>
                    <li><label><input type="checkbox" checked> Aanpassen aan dierenactiviteit (nacht/rusttijden)</label></li>
                </ul>

                <button type="submit" class="mission-submit">MISSIE STARTEN</button>
            </form>
        </aside>
    </div>
</body>

</html>