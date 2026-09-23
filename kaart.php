<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>NatureGuard Dashboard - Kaart</title>
    <link rel="stylesheet" href="./css/main.css"/>
    <link rel="stylesheet" href="./css/kaart.css"/>
    <script src="./js/main.js" defer></script>
</head>

<body>
<?php include("./partials/header.php"); ?>
<div class="layout">
    <?php include("./partials/sidebar.php"); ?>
    <main class="page kaart-main">
        <div class="page-top">
            <div>
                <h1 class="page-title">Tactische Kaart</h1>
                <p class="page-subtitle">Realtime kaartlaag met sectorstatus, sensornodes en droneposities.</p>
            </div>
            <div class="page-actions">
                <span class="badge">SATELLIET</span>
                <a href="" class="btn refresh">Scan Vernieuwen</a>
            </div>
        </div>

        <div class="map-layout">
            <section class="card map-card">
                <div class="card-head">
                    <h2>Kralingse bos</h2>
                    <div class="map-badges">
                        <span>Zoom 100%</span>
                        <span class="badge">LIVE</span>
                    </div>
                </div>
                <div>
                    <div class="map">
                        <img src="./image/map.png" alt="Kaart voor simulatie" id="map">

                        <div class="overlay-box">
                            <p>filler</p>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

</div>
</body>

</html>