<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NatureGuard Dashboard - Instellingen</title>
  <link rel="stylesheet" href="./css/main.css" />
  <link rel="stylesheet" href="./css/instellingen.css" />
</head>

<body>
  <?php include("./partials/header.php"); ?>
  <div class="layout">
    <?php include("./partials/sidebar.php"); ?>
    <main class="page">
      <div class="page-top">
        <div>
          <h1 class="page-title">Systeeminstellingen</h1>
          <p class="page-subtitle">Configureer automatische drempelwaarden, missieparameters en notificatie-voorkeuren.
          </p>
        </div>
        <div class="page-actions">
          <a href="#" class="btn primary">Wijzigingen Opslaan</a>
        </div>
      </div>

      <div class="settings-grid">
        <section class="card settings-card">
          <h2>⚙ Drone Configuratie</h2>
          <div class="setting-row">
            <div class="setting-label"><span>Maximale vlieghoogte</span><span class="setting-value">120m</span></div>
            <input type="range" class="range-input" min="0" max="100" value="27" aria-label="Maximale vlieghoogte">
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Minimale batterij voor terugkeer</span><span
                class="setting-value">20%</span></div>
            <input type="range" class="range-input" min="0" max="100" value="7" aria-label="Minimale batterij voor terugkeer">
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Maximale windsnelheid</span><span class="setting-value">45 km/h</span>
            </div>
            <input type="range" class="range-input" min="0" max="100" value="15" aria-label="Maximale windsnelheid">
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Niet vliegen bij regen</span><span class="toggle on"></span></div>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Automatisch terugkeren bij storm</span><span class="toggle on"></span>
            </div>
          </div>
        </section>

        <section class="card settings-card">
          <h2>◎ Missie Standaarden</h2>
          <div class="setting-row">
            <div class="setting-label"><span>Standaard scanresolutie</span></div>
            <div class="select">Hoog (Multi-Spectraal)</div>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Foto-interval (seconden)</span></div>
            <div class="input">5 seconden</div>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Automatische AI-analyse</span><span class="toggle on"></span></div>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Automatische interventie toestaan</span><span class="toggle"></span></div>
          </div>
        </section>

        <section class="card settings-card">
          <h2>◌ Meldingen</h2>
          <div class="setting-row">
            <div class="setting-label"><span>Push meldingen via mobiele app</span><span class="toggle on"></span></div>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>E-mail samenvattingen</span><span class="toggle on"></span></div>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Frequentie e-mail rapport</span></div>
            <div class="select">Dagelijks om 18:00</div>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Kritieke storingen direct melden</span><span class="toggle on"></span>
            </div>
          </div>
        </section>

        <section class="card settings-card">
          <h2>⌁ Systeem &amp; Status</h2>
          <div class="setting-row">
            <div class="setting-label"><span>Firmware Versie</span><span class="status-pill active">UP TO DATE</span>
            </div>
            <strong>v4.2.1-stable</strong>
          </div>
          <div class="setting-row">
            <div class="setting-label"><span>Laatste Sensor Kalibratie</span></div>
            <strong>2 dagen geleden (Veluwe Post-West)</strong>
          </div>
          <div class="duo-actions">
            <a href="#" class="btn secondary" style="text-align: center;">Handmatige Kalibratie</a>
            <a href="#" class="btn" style="text-align: center; border-color: var(--cyan); color: var(--cyan);">Systeem
              Diagnostiek</a>
          </div>
        </section>
      </div>
    </main>
  </div>
</body>

</html>