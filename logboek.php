<?php

require_once __DIR__ . '/config/database.php';


// Logboek gegevens ophalen

$query = "SELECT * FROM logboek ORDER BY created_at DESC";

$result = mysqli_query($db, $query);

$logs = [];

while ($row = mysqli_fetch_assoc($result)) {
    $logs[] = $row;
}

?>

<!doctype html>

<html lang="nl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>NatureGuard Dashboard - Logboek</title>

    <link
        rel="stylesheet"
        href="./css/logboek.css"
    >

</head>


<body>


<?php include __DIR__ . '/partials/header.php'; ?>


<div class="layout">


    <?php include __DIR__ . '/partials/sidebar.php'; ?>


    <main class="page">


        <!-- HEADER -->

        <div class="page-top">

            <div>

                <h1 class="page-title">
                    Systeem Logboek
                </h1>

                <p class="page-subtitle">
                    Bekijk biosfeer gebeurtenissen en interventies.
                </p>

            </div>
        </div>


        <!-- FILTERS -->

        <div class="card log-filters">


            <ul class="filter-keywords">

                <li class="active">

                    <a
                        href="#"
                        class="log-filter"
                        data-filter="all"
                    >
                        Alles
                    </a>

                </li>


                <li>

                    <a
                        href="#"
                        class="log-filter"
                        data-filter="scan"
                    >
                        Scans
                    </a>

                </li>


                <li>

                    <a
                        href="#"
                        class="log-filter"
                        data-filter="interventie"
                    >
                        Interventies
                    </a>

                </li>


                <li>

                    <a
                        href="#"
                        class="log-filter"
                        data-filter="drone"
                    >
                        Drones
                    </a>

                </li>


                <li>

                    <a
                        href="#"
                        class="log-filter"
                        data-filter="probleem"
                    >
                        Problemen
                    </a>

                </li>

            </ul>


            <div class="filter-group">

                <!-- <input
                    type="text"
                    id="filter-search"
                    placeholder="Zoek op trefwoord..." -->
                <!-- > -->

                <input
                    type="date"
                    id="filter-date"
                >

            </div>


        </div>


        <!-- LOGBOEK -->

        <table class="card log-table">


            <thead>

                <tr>

                    <th>Tijd</th>

                    <th>Type</th>

                    <th>Activiteit</th>

                    <th>Locatie</th>

                    <th>Status</th>

                </tr>

            </thead>


            <tbody id="logbook-body">

                <?php foreach ($logs as $log): ?>

                    <tr
                        data-type="<?= htmlspecialchars($log['type']) ?>"
                        data-date="<?= date('Y-m-d', strtotime($log['created_at'])) ?>"
                    >

                        <td>

                            <?= date(
                                'H:i',
                                strtotime($log['created_at'])
                            ) ?>

                            ·

                            <?= date(
                                'd-m-Y',
                                strtotime($log['created_at'])
                            ) ?>

                        </td>


                        <td>

                            <?php

                            if ($log['type'] === 'scan') {
                                echo "Scan";
                            }

                            if ($log['type'] === 'interventie') {
                                echo "Interventie";
                            }

                            if ($log['type'] === 'drone') {
                                echo "Drone";
                            }

                            if ($log['type'] === 'probleem') {
                                echo "Probleem";
                            }

                            ?>

                        </td>


                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $log['activity']
                                ) ?>

                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $log['location']
                            ) ?>

                        </td>


                        <td>

                            <span
                                class="status-pill <?= htmlspecialchars($log['status']) ?>"
                            >

                                <?php

                                if ($log['status'] === 'done') {
                                    echo "Voltooid";
                                }

                                if ($log['status'] === 'pending') {
                                    echo "Wacht";
                                }

                                if ($log['status'] === 'active') {
                                    echo "Monitoring";
                                }

                                ?>

                            </span>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>


            <tfoot>

                <tr>

                    <td colspan="5">

                        <div class="log-footer-content">

                            <p id="logbook-count">
                                Getoond: <?= count($logs) ?> logregels
                            </p>

                            <div class="pagination">

                                <button
                                    id="previous-page"
                                    class="log-link"
                                >
                                    Vorige
                                </button>

                                <span id="page-info">
                                    Pagina 1
                                </span>

                                <button
                                    id="next-page"
                                    class="log-link"
                                >
                                    Volgende
                                </button>

                            </div>

                        </div>

                    </td>

                </tr>

            </tfoot>


        </table>


    </main>

</div>


<script src="js/logboek.js"></script>


</body>

</html>