<?php
require_once __DIR__ . '/config/database.php';

$sql = "SELECT * FROM claims ORDER BY id DESC";
$result = mysqli_query($db, $sql);

$claims = [];
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
    <title>VerifyNET</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/verifinet.css" />
</head>
<body>
    <?php include("./partials/header.php"); ?>

    <div class="layout">
        <?php include("./partials/sidebar.php"); ?>

        <main class="page">
            <div class="page-actions">
                <h2 style="margin:0; font-size: 24px; color: var(--white);">VerifyNET Monitor</h2>
                <a href="save_claim.php" class="btn primary" style="width: 15vw; justify-self: flex-end;">Nieuwe claim</a>
            </div>

            <section class="card">
                <div class="card-head">
                    <h2>Live Claims Overzicht</h2>
                </div>

                <div style="padding: 15px;">
                    <table class="verifinet-table">
                        <thead>
                            <tr>
                                <th>Tijd</th>
                                <th>Activiteit / Claim</th>
                                <th>Bron</th>
                                <th>Afbeelding</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($claims)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #888;">Geen claims gevonden in de database.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($claims as $claim):
                                        $tijd = isset($claim['timestamp']) ? date('H:i', strtotime($claim['timestamp'])) : 'N.v.t.';

                                    $statusKleur = '#ff9f0a'; // Oranje (In onderzoek / Pending) als standaard
                                    if (isset($claim['Status'])) {
                                        if ($claim['Status'] === 'true') $statusKleur = '#22f693'; // Groen
                                        if ($claim['Status'] === 'false') $statusKleur = '#96031A'; // Rood
                                    }
                                ?>

                                    <tr style="--row-color: <?= $statusKleur; ?>;" onclick="window.location.href='article.php?id=<?= $claim['id']; ?>'">

                                        <td class="tijd-col"><?= $tijd; ?></td>
                                        <td><?= htmlspecialchars($claim['Title'] ?? 'Geen titel'); ?></td>
                                        <td><?= htmlspecialchars($claim['source'] ?? 'Onbekend'); ?></td>
                                        <td>
                                            <?php if (!empty($claim['image_path'])): ?>
                                                <div style="width: 80px; height: 40px; background: url('<?= htmlspecialchars($claim['image_path']); ?>') center/cover; border-radius: 4px; border: 1px solid #6D676E;"></div>
                                            <?php else: ?>
                                                <span style="color: #777; font-size: 12px;">Geen beeld</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <aside class="rightbar">
        </aside>
    </div>
</body>
</html>
