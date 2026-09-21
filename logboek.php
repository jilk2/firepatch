<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NatureGuard Dashboard - Logboek</title>
  <link rel="stylesheet" href="./css/main.css" />
  <link rel="stylesheet" href="./css/logboek.css" />
</head>

<body>
  <?php include("./partials/header.php"); ?>
  <div class="layout">
    <?php include("./partials/sidebar.php"); ?>
    <main class="page">
      <div class="page-top">
        <div>
          <h1 class="page-title">Systeem Logboek</h1>
          <p class="page-subtitle">Bekijk real-time biosfeer gebeurtenissen en geautomatiseerde interventies.</p>
        </div>
        <div class="page-actions">
          <a href="#" class="btn secondary">Exporteren</a>
          <a href="#" class="btn primary">Live Pauzeren</a>
        </div>
      </div>

      <div class="card log-filters">
        <ul class="filter-keywords">
          <?php
          $activeFilter = $_GET['f'] ?? 'all';
          ?>
          <li <?php if ($activeFilter === 'all')
            echo 'class="active"'; ?>><a href="?f=all">Alles</a></li>
          <li <?php if ($activeFilter === 'scans')
            echo 'class="active"'; ?>><a href="?f=scans">Scans</a></li>
          <li <?php if ($activeFilter === 'interventies')
            echo 'class="active"'; ?>><a
              href="?f=interventies">Interventies</a></li>
          <li <?php if ($activeFilter === 'drones')
            echo 'class="active"'; ?>><a href="?f=drones">Drones</a></li>
          <li <?php if ($activeFilter === 'problemen')
            echo 'class="active"'; ?>><a href="?f=problemen">Problemen</a></li>
        </ul>
        <div class="filter-group">
          <label hidden for="filter-search">search keyword</label>
          <input type="text" id="filter-search" name="filter-search" placeholder="Zoek op trefwoord..." />

          <label hidden for="filter-date">Datum</label>
          <input type="date" id="filter-date" name="filter-date" />
        </div>
      </div>

      <table class="card log-table">
        <thead>
          <tr>
            <th>Tijd</th>
            <th>Activiteit</th>
            <th>Locatie</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>14:26 · Vandaag</td>
            <td><strong>Bewateringsactie afgerond</strong></td>
            <td>Sector 04</td>
            <td><span class="status-pill done">Voltooid</span></td>
          </tr>
          <tr>
            <td>14:20 · Vandaag</td>
            <td><strong>Deployment gestart</strong></td>
            <td>Sector 04</td>
            <td><span class="status-pill done">Voltooid</span></td>
          </tr>
          <tr>
            <td>14:15 · Vandaag</td>
            <td><strong>Interventieverzoek gegenereerd</strong></td>
            <td>Sector 04</td>
            <td><span class="status-pill pending">Wacht</span></td>
          </tr>
          <tr>
            <td>14:14 · Vandaag</td>
            <td><strong>Sensorpiek gedetecteerd</strong></td>
            <td>Sector 04</td>
            <td><span class="status-pill done">Gelogd</span></td>
          </tr>
          <tr>
            <td>13:52 · Vandaag</td>
            <td><strong>Patrouillepad aangepast</strong></td>
            <td>Sector 04</td>
            <td><span class="status-pill active">Monitoring</span></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4">
              <div class="log-footer-content">
                <p>Getoond: 5/89 logregels</p>
                <div class="pagination">
                  <a class="log-link" href="#" class="page-link">Vorige</a>
                  <span>Pagina 1 van 9</span>
                  <a class="log-link" href="#" class="page-link">Volgende</a>
                </div>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </main>
  </div>
</body>

</html>