<?php
require "database.php";

require "database.php";
$sectors = [];

try {
    $result = $pdo->query(
            "SELECT `sector_number`, `sector_name` FROM `map_sectors` ORDER BY `sector_name`"
        );
    $sectors = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log("Sectoren ophalen mislukt: " . $exception->getMessage());
}

date_default_timezone_set('Europe/Amsterdam');
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firepatch - Nieuwe claim</title>
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/saveclaimes.css">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="layout short">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="page claim-create-page">
        <section class="card claim-create-card">
            <div class="card-head">
                <h2>Nieuwe VerifyNET-claim</h2>
                <a href="verifynet.php" class="btn ghost">Annuleren</a>
            </div>

            <form action="upload.php" method="post" enctype="multipart/form-data" id="claim-upload-form">
                <input type="hidden" name="author_email" id="author-email">

                <label for="title">Claim / activiteit</label>
                <input class="input" type="text" id="title" name="title" maxlength="255" required>

                <label for="description">Beschrijving</label>
                <textarea class="input" id="description" name="description" rows="5"></textarea>

                <!-- <label for="source">Bron of locatie</label>
                <input class="input" type="text" id="source" name="source" maxlength="2048"
                       placeholder="Bijvoorbeeld Sector 4 of een https://-link"> -->

                <label for="sector">Sector</label>
                <select class="input" name="sector" id="sector">
                    <option value="">Geen sector gekozen</option>
                    <?php foreach ($sectors as $sectorRow): ?>
                        <?php $sectorName = $sectorRow['sector_name']; ?>
                        <option value="<?= htmlspecialchars($sectorRow["sector_number"]) ?>"><?= htmlspecialchars($sectorName, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- 1. HIER IS DE 'required' TAG TOEGEVOEGD: Afbeelding is nu verplicht -->
                <label for="evidence">Bewijsafbeelding <small>(Verplicht, JPG/PNG/WebP, maximaal 5 MB)</small></label>
                <input class="input file-input" type="file" name="evidence" id="evidence" required
                       accept="image/jpeg,image/png,image/webp">

                <button type="submit" class="btn primary submit-claim">Claim indienen</button>
            </form>
        </section>
    </main>
</div>

<script>
    // 2. CHECK: Als niet ingelogd, stuur gewoon "Gast" door
    const currentUser = localStorage.getItem('verifinet_user');
    const authorInput = document.getElementById('author-email');

    if (!currentUser) {
        authorInput.value = 'Gast'; // Dit wordt meegestuurd naar upload.php
    } else {
        authorInput.value = currentUser;
    }
</script>
</body>
</html>