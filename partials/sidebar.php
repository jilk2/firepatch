<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/sidebar.css" />
</head>

<body>
    <aside>
        <nav>
            <?php $currentpage = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php" class="nav-item <?= $currentpage === 'index.php' ? ' current' : '' ?>"><span class="nav-icon">⌂</span>Overzicht</a>
            <a href="mission.php" class="nav-item <?= $currentpage === 'mission.php' ? ' current' : '' ?>"><span class="nav-icon">●</span>Missies</a>
            <a href="kaart.php" class="nav-item <?= $currentpage === 'kaart.php' ? ' current' : '' ?>"><span class="nav-icon">🗺</span>Kaart</a>
            <a href="logboek.php" class="nav-item <?= $currentpage === 'logboek.php' ? ' current' : '' ?>"><span class="nav-icon">🗋</span>Logboek</a>
            <a href="instellingen.php" class="nav-item <?= $currentpage === 'instellingen.php' ? ' current' : '' ?>"><span class="nav-icon">⚙</span>Instellingen</a>
            <a href="verifynet.php" class="nav-item <?= $currentpage === 'verifynet.php' ? ' current' : '' ?>"><span class="nav-icon">◈</span>VerifyNET</a>
        </nav>
        <div class="fleet">
            <h3>FLEET MATRIX</h3>
            <p>Drone Signalen <span>12/12</span></p>
            <p>Signaalsterkte <span>98%</span></p>
        </div>
    </aside>
</body>

</html>