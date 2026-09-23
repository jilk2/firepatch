<?php
require_once "DB/DBConnect.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Haal de claim op
$stmt = mysqli_prepare($db, "SELECT * FROM claims WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$claim = mysqli_fetch_assoc($result);

if (!$claim) {
    die("Claim niet gevonden.");
}

// Haal de community notes (comments) op bij deze claim
$notesStmt = mysqli_prepare($db, "SELECT * FROM community_notes WHERE claim_id = ? ORDER BY timestamp DESC");
mysqli_stmt_bind_param($notesStmt, "i", $id);
mysqli_stmt_execute($notesStmt);
$notesResult = mysqli_stmt_get_result($notesStmt);

$notes = [];
while ($note = mysqli_fetch_assoc($notesResult)) {
    $notes[] = $note;
}

mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerifyNET - Claim Details</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/verifinet.css" />
</head>
<body>
    <?php include("./partials/header.php"); ?>
    
    <div class="layout">
        <?php include("./partials/sidebar.php"); ?>
        
        <main class="page">
            <div class="page-actions">
                <a href="verifynet.php" class="btn ghost">&larr; Terug naar Overzicht</a>
            </div>

            <div class="card claim-detail-card">
                <div class="card-head">
                    <h2>Claim Details [#<?= $claim['id']; ?>]</h2>
                    <?php 
                        $statusText = "IN ONDERZOEK";
                        $statusClass = "badge-pending";
                        if ($claim['Status'] === 'true') { $statusText = "WAAR"; $statusClass = "badge-true"; }
                        if ($claim['Status'] === 'false') { $statusText = "ONWAAR"; $statusClass = "badge-false"; }
                    ?>
                    <span class="badge <?= $statusClass; ?>"><?= $statusText; ?></span>
                </div>

                <div class="claim-body" style="padding: 20px;">
                    <h1 style="font-size: 1.5rem; margin-bottom: 10px; color: var(--white);"><?= htmlspecialchars($claim['Title']); ?></h1>
                    <p class="text-muted" style="margin-bottom: 15px; font-size: 0.9rem;">Indiener: <strong><?= htmlspecialchars($claim['author_email']); ?></strong> op <?= $claim['timestamp']; ?></p>
                    
                    <?php if (!empty($claim['description'])): ?>
                        <div class="claim-desc-box" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 3px solid var(--cyan);">
                            <p style="margin: 0; color: #d9ecff;"><?= nl2br(htmlspecialchars($claim['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($claim['source'])): ?>
                        <p style="font-size: 0.9rem; margin-bottom: 20px;"><strong>Bron:</strong> <a href="<?= htmlspecialchars($claim['source']); ?>" target="_blank" style="color: var(--cyan);"><?= htmlspecialchars($claim['source']); ?></a></p>
                    <?php endif; ?>

                    <?php if (!empty($claim['image_path'])): ?>
                        <div style="margin-bottom: 20px;">
                            <img src="<?= htmlspecialchars($claim['image_path']); ?>" alt="Bewijsmateriaal" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 1px solid var(--gray);">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Community Notes Sectie -->
            <div class="card">
                <div class="card-head">
                    <h2>Community Verificaties (<?= count($notes); ?>)</h2>
                </div>
                
                <div style="padding: 20px;">
                    <?php if (empty($notes)): ?>
                        <p class="text-muted" style="font-style: italic;">Nog geen notities toegevoegd. Wees de eerste node die een beoordeling deelt.</p>
                    <?php else: ?>
                        <div style="display: grid; gap: 12px;">
                            <?php foreach ($notes as $note): ?>
                                <div class="note-box" style="background: #111b2a; border: 1px solid #2a3b52; padding: 14px; border-radius: 8px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--cyan); margin-bottom: 6px;">
                                        <span>Node: <?= htmlspecialchars($note['author_email']); ?></span>
                                        <span class="text-muted"><?= $note['timestamp']; ?></span>
                                    </div>
                                    <p style="margin: 0 0 8px 0; color: var(--white);"><?= nl2br(htmlspecialchars($note['text'])); ?></p>
                                    <small style="color: #888;">[Bron: <?= htmlspecialchars($note['source']); ?>]</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulier om een nieuwe Community Note toe te voegen -->
                    <div style="margin-top: 30px; border-top: 1px solid var(--gray); padding-top: 20px;">
                        <h3 style="margin-bottom: 12px; font-size: 1.1rem; color: var(--lightgreen);">Voeg Verificatie / Notitie toe</h3>
                        <form id="comment-form" onsubmit="stuurNotitie(event, <?= $claim['id']; ?>)">
                            <div style="margin-bottom: 12px;">
                                <textarea id="note-text" class="input" rows="3" placeholder="Typ je onderbouwing (gebruik woorden als 'klopt', 'waar' of juist 'niet waar', 'incorrect')..." required></textarea>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <input type="text" id="note-source" class="input" placeholder="Bronvermelding / URL (optioneel)">
                            </div>
                            <button type="submit" class="btn primary" style="width: 100%;">PLAATS VERIFICATIE</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <aside class="rightbar"></aside>
    </div>

    <script>
    async function stuurNotitie(event, claimId) {
        event.preventDefault();
        const text = document.getElementById('note-text').value;
        const source = document.getElementById('note-source').value;
        const currentUser = localStorage.getItem('verifinet_user');

        if (!currentUser) {
            alert("Toegang geweigerd: Je moet ingelogd zijn  om te stemmen.");
            return;
        }

        try {
            const response = await fetch('save_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    claim_id: claimId,
                    text: text,
                    source: source,
                    author_email: currentUser
                })
            });

            const result = await response.json();
            if (result.success) {
                window.location.reload();
            } else {
                alert("Fout: " + result.message);
            }
        } catch (err) {
            console.error(err);
            alert("Kan geen verbinding maken met de server.");
        }
    }
    </script>
</body>
</html>