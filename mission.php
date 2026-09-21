<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NatureGuard | Missies</title>
    <link rel="stylesheet" href="./css/main.css" />
</head>

<body>
    <?php include("./partials/header.php"); ?>
    <div class="layout">
        <?php include("./partials/sidebar.php"); ?>
        <main class="page">
            <section class="card mission">
                <h4>HUIDIGE MISSIE - RUNNING</h4>
                <h2>Ecosysteemscan - Sector 03</h2>
                <p>Missievoortgang <strong>66% Voltooid</strong></p>
                <div class="progress cyan">
                    <div></div>
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
                        <div></div>
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
                    <div></div>
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