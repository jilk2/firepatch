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
        </main>

        <aside class="rightbar">
        </aside>
    </div>


    <main>
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
                            <td><?= $claim[''];?></td>
                            <td><?= $claim[''];?></td>
                            <td><?= $claim[''];?></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>