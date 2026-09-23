<?php
require_once "DB/DBConnect.php";

$sql = "SELECT * FROM claims";

$result = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $claims[] = $row;
}

mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>verifyNET</title>
    <link rel="stylesheet" href="./css/main.css" />
</head>

<body>
    <?php include("./partials/header.php"); ?>
    <div class="layout">
        <?php include("./partials/sidebar.php"); ?>
        <main class="page">
            <section>
                <table>
                    <thead>
                        <tr>
                            <th>Tijd</th>
                            <th>Activiteit</th>
                            <th>Locatie</th>
                            <th>Afbeelding</th>
                        </tr>
                    </thead>
                    <tfoot></tfoot>
                    <tbody>
                        <?php
                        foreach ($claims as $claim):
                            ?>
                            <tr>
                                <td>
                                    <?= $claim['timestamp']; ?>
                                </td>
                                <td>
                                    <?= $claim['Title']; ?>
                                </td>
                                <td>
                                    <?= $claim['Locatie']; ?>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </section>
        </main>

        <aside class="rightbar">
        </aside>
    </div>
</body>
<style>
table {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th, tr {
  border: 1px solid black;
  padding: 8px;
  color: black
}

tr:nth-child(even){background-color: #f2f2f2;}

tr:hover {background-color: #ddd;}

th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: black;
}
</style>
</html>