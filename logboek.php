<?php

require_once('./DB/DBConnect.php');


// Databaseverbinding ophalen
$db = firepatchMysqli();


// =============================================
// LOGBOEK GEGEVENS OPHALEN
// =============================================

$query = "
    SELECT *
    FROM logboek
    ORDER BY created_at DESC
";

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

    <title>
        Firepatch - Logboek
    </title>


    <!-- Algemene Firepatch CSS -->

    <link
        rel="stylesheet"
        href="./css/main.css"
    >


    <!-- Logboek CSS -->

    <link
        rel="stylesheet"
        href="./css/logboek.css"
    >


    <!-- Logboek JavaScript -->

    <script
        src="./js/logboek.js"
        defer
    ></script>

</head>


<body>


<?php include("./partials/header.php"); ?>


<div class="layout">


    <?php include("./partials/sidebar.php"); ?>


    <main class="page">


        <!-- =============================================
             HEADER
        ============================================== -->

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



        <!-- =============================================
             FILTERS
        ============================================== -->

        <div class="card log-filters">


            <!-- =============================================
                 LINKERKANT
            ============================================== -->

            <div class="filter-left">


                <!-- KIES FILTER -->

                <div class="filter-group">

                    <select id="filter-sort">

                        <option value="all">
                            Alles
                        </option>

                        <option value="activity">
                            Activiteit
                        </option>

                        <option value="location">
                            Locatie
                        </option>

                        <option value="status">
                            Status
                        </option>

                    </select>

                </div>



                <!-- =============================================
                     FILTER WAARDE
                ============================================== -->

                <div
                    class="filter-group"
                    id="value-filter-group"
                >

                    <select
                        id="filter-value"
                        disabled
                    >

                        <option value="all">
                            Alles
                        </option>

                    </select>

                </div>


            </div>



            <!-- =============================================
                 DATUM ALTIJD RECHTS
            ============================================== -->

            <div class="filter-right">

                <div class="filter-group">

                    <input
                        type="date"
                        id="filter-date"
                    >

                </div>

            </div>


        </div>



        <!-- =============================================
             LOGBOEK TABEL
        ============================================== -->

        <table class="card log-table">


            <!-- =============================================
                 TABEL HEADER
            ============================================== -->

            <thead>

                <tr>

                    <th>
                        Tijd
                    </th>

                    <th>
                        Activiteit
                    </th>

                    <th>
                        Locatie
                    </th>

                    <th>
                        Status
                    </th>

                </tr>

            </thead>



            <!-- =============================================
                 LOGBOEK REGELS
            ============================================== -->

            <tbody id="logbook-body">


                <?php foreach ($logs as $log): ?>


                    <tr

                        data-activity="<?=
                            htmlspecialchars(
                                $log['activity'],
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ?>"

                        data-location="<?=
                            htmlspecialchars(
                                $log['location'],
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ?>"

                        data-status="<?=
                            htmlspecialchars(
                                $log['status'],
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ?>"

                        data-date="<?=
                            date(
                                'Y-m-d',
                                strtotime($log['created_at'])
                            )
                        ?>"

                    >



                        <!-- =============================================
                             TIJD
                        ============================================== -->

                        <td>

                            <?=
                                date(
                                    'H:i',
                                    strtotime($log['created_at'])
                                )
                            ?>

                            -

                            <?=
                                date(
                                    'd-m-Y',
                                    strtotime($log['created_at'])
                                )
                            ?>

                        </td>



                        <!-- =============================================
                             ACTIVITEIT
                        ============================================== -->

                        <td>

                            <strong>

                                <?=
                                    htmlspecialchars(
                                        $log['activity'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>

                            </strong>

                        </td>



                        <!-- =============================================
                             LOCATIE
                        ============================================== -->

                        <td>

                            <?=
                                htmlspecialchars(
                                    $log['location'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ?>

                        </td>



                        <!-- =============================================
                             STATUS
                        ============================================== -->

                        <td>


                            <span
                                class="status-pill <?=
                                    htmlspecialchars(
                                        $log['status'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>"
                            >


                                <?php

                                if ($log['status'] === 'done') {

                                    echo "Voltooid";

                                } elseif ($log['status'] === 'pending') {

                                    echo "Wacht";

                                } elseif ($log['status'] === 'active') {

                                    echo "Monitoring";

                                } else {

                                    echo htmlspecialchars(
                                        $log['status'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                }

                                ?>


                            </span>


                        </td>


                    </tr>


                <?php endforeach; ?>


            </tbody>



            <!-- =============================================
                 FOOTER
            ============================================== -->

            <tfoot>

                <tr>

                    <td colspan="4">


                        <div class="log-footer-content">


                            <!-- =============================================
                                 AANTAL LOGREGELS
                            ============================================== -->

                            <p id="logbook-count">

                                Getoond:
                                <?= count($logs) ?>
                                logregels

                            </p>



                            <!-- =============================================
                                 PAGINATION
                            ============================================== -->

                            <div class="pagination">


                                <button
                                    type="button"
                                    id="previous-page"
                                    class="log-link"
                                >

                                    Vorige

                                </button>



                                <span id="page-info">

                                    Pagina 1

                                </span>



                                <button
                                    type="button"
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


</body>

</html>