<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NatureGuard Dashboard - Kaart</title>
  <link rel="stylesheet" href="./css/main.css" />
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
          <a href="#" class="btn primary">Scan Vernieuwen</a>
        </div>
      </div>

      <div class="map-layout">
        <section class="card map-card">
          <div class="card-head">
            <h2>Veluwe District</h2>
            <div class="map-badges">
              <span>Zoom 150%</span>
              <span class="badge">LIVE</span>
              <button id="save-marker-positions" type="button" class="btn secondary">Posities opslaan</button>
            </div>
          </div>
          <div class="map">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d25534.453872819813!2d6.010588891485108!3d52.04765963410813!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sen!2snl!4v1788955259685!5m2!1sen!2snl"
              width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy"
              referrerpolicy="strict-origin-when-cross-origin"></iframe>
            <div class="marker m1 ok">S01 · Stabiel</div>
            <div class="marker m2">S02 · Analyse</div>
            <div class="marker m3 ok">S03 · Patrouille</div>
            <div class="marker m4 warn">S04 · Droogte</div>
            <div class="overlay-box">
              <h4>SELECTIE S04</h4>
              Bodemvochtigheid 12%<br />
              Temperatuur 34°C<br />
              Wind 16 km/u
            </div>
          </div>
        </section>

      </div>
    </main>

    <aside class="rightbar kaart-rightbar">
      <section class="card">
        <div class="card-head">
          <h2>Sectorstatus</h2>
        </div>
        <ul class="sector-list">
          <li><span>Sector 01</span><span class="status-pill done">Stabiel</span></li>
          <li><span>Sector 02</span><span class="status-pill active">Monitoring</span></li>
          <li><span>Sector 03</span><span class="status-pill active">Actief</span></li>
          <li><span>Sector 04</span><span class="status-pill pending">Kritiek</span></li>
        </ul>
      </section>

      <section class="card mission">
        <h4>MAP-LAAG DETAILS</h4>
        <h2>Sensor Matrix</h2>
        <p>Beschikbaarheid <strong>98%</strong></p>
        <div class="progress">
          <div style="width: 98%"></div>
        </div>
        <ul>
          <li>Actieve nodes <span>124</span></li>
          <li>Offline nodes <span>3</span></li>
          <li class="active">Waarschuwingen <span>2</span></li>
        </ul>
      </section>
    </aside>
  </div>
</body>

</html>