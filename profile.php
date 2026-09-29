<?php
// 1. API: Handel de data-aanvraag af als het een POST verzoek is
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    require 'database.php'; // Gebruikt PDO net als login.php

    $json = file_get_contents("php://input");
    $data = json_decode($json);

    if (isset($data->email)) {
        // Haal alle claims van deze gebruiker op
        $stmtClaims =$pdo->prepare("SELECT * FROM claims WHERE author_email = ? ORDER BY timestamp DESC");
        $stmtClaims->execute([$data->email]);
        $claims =$stmtClaims->fetchAll(PDO::FETCH_ASSOC);

        // Haal alle comments/notities van deze gebruiker op (inclusief de titel van de claim)
        $stmtNotes =$pdo->prepare("
            SELECT cn.*, c.Title as claim_title 
            FROM community_notes cn 
            LEFT JOIN claims c ON cn.claim_id = c.id 
            WHERE cn.author_email = ? 
            ORDER BY cn.timestamp DESC
        ");
        $stmtNotes->execute([$data->email]);
        $notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "claims" => $claims,
            "notes" => $notes
        ]);
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Geen e-mailadres doorgegeven."]);
    }
    exit();
}
// 2. HTML Tekenen als het een GET verzoek is
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerifyNET - Mijn Profiel</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/verifinet.css" />
</head>
<body>
    <?php include("./partials/header.php"); ?>
    
    <div class="layout">
        <?php include("./partials/sidebar.php"); ?>
        
        <main class="page">
            <div class="page-actions">
                <a href="verifynet.php" class="btn primary">&larr; Terug naar Monitor</a>
            </div>

            <!-- Profiel Header -->
            <section class="card" style="margin-bottom: 20px;">
                <div style="padding: 25px; display: flex; align-items: center; gap: 20px;">
                    <div id="profile-avatar" style="width: 80px; height: 80px; border-radius: 50%; background: var(--gray); border: 2px solid var(--cyan); display: grid; place-items: center; font-size: 28px; font-weight: bold; color: var(--white);">
                        ?
                    </div>
                    <div>
                        <h2 id="profile-email" style="margin: 0 0 5px 0; font-size: 24px; color: var(--white);">Laden...</h2>
                        <p style="margin: 0 0 15px 0; color: var(--lightgreen); font-weight: bold; font-size: 13px;">VerifiNET Node</p>
                        <!-- De knop is voorbereid, doet nu visueel z'n ding -->
                        <button onclick="alert('Deze functie wordt later toegevoegd!')" class="btn ghost" style="padding: 6px 12px; font-size: 12px;">Profielfoto wijzigen</button>
                    </div>
                </div>
            </section>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <!-- Mijn Claims -->
                <section class="card">
                    <div class="card-head">
                        <h2>Mijn Ingediende Claims</h2>
                    </div>
                    <div style="padding: 15px;" id="claims-container">
                        <p class="text-muted" style="font-style: italic;">Data wordt geladen...</p>
                    </div>
                </section>

                <!-- Mijn Verificaties (Comments) -->
                <section class="card">
                    <div class="card-head">
                        <h2>Mijn Verificaties (Notities)</h2>
                    </div>
                    <div style="padding: 15px;" id="notes-container">
                        <p class="text-muted" style="font-style: italic;">Data wordt geladen...</p>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", async () => {
        const currentUser = localStorage.getItem('verifinet_user');
        
        // Stuur bezoeker weg als hij niet is ingelogd
        if (!currentUser) {
            window.location.href = 'login.php';
            return;
        }

        // Update profiel header
        document.getElementById('profile-email').textContent = currentUser;
        document.getElementById('profile-avatar').textContent = currentUser.substring(0, 2).toUpperCase();

        try {
            // Haal de data op uit de backend
            const response = await fetch('profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: currentUser })
            });
            const data = await response.json();

            if (data.success) {
                renderClaims(data.claims);
                renderNotes(data.notes);
            } else {
                alert("Fout bij laden van profiel: " + data.message);
            }
        } catch (err) {
            console.error(err);
            document.getElementById('claims-container').innerHTML = "<p style='color: red;'>Fout bij verbinding met de server.</p>";
        }
    });

    // Functie om de claims in een tabel te tekenen
    function renderClaims(claims) {
        const container = document.getElementById('claims-container');
        if (claims.length === 0) {
            container.innerHTML = '<p class="text-muted" style="font-style: italic;">Je hebt nog geen claims ingediend.</p>';
            return;
        }

        let html = '<table class="verifinet-table" style="margin-top: 0;"><tbody>';
        claims.forEach(claim => {
            let statusColor = '#ff9f0a'; // Oranje standaard
            if (claim.Status === 'true') statusColor = '#22f693'; // Groen
            if (claim.Status === 'false') statusColor = '#96031A'; // Rood

            // Knip tijd uit de timestamp
            const time = claim.timestamp ? claim.timestamp.substring(11, 16) : '??:??';

            // OPGELOST: We gebruiken nu style="--row-color: ..." op de <tr>
            // En we gebruiken class="tijd-col" op de <td>
            // Hierdoor pakt hij exact dezelfde, goed werkende styling op als in verifinet.php!
            html += `
                <tr style="--row-color: ${statusColor};" onclick="window.location.href='article.php?id=${claim.id}'">
                    <td class="tijd-col" style="width: 80px;">${time}</td>
                    <td>${claim.Title}</td>
                </tr>
            `;
        });
        html += '</tbody></table>';
        container.innerHTML = html;
    }

    // Functie om de notities te tekenen
    function renderNotes(notes) {
        const container = document.getElementById('notes-container');
        if (notes.length === 0) {
            container.innerHTML = '<p class="text-muted" style="font-style: italic;">Je hebt nog geen verificaties geplaatst.</p>';
            return;
        }

        let html = '<div style="display: grid; gap: 10px;">';
        notes.forEach(note => {
            // Laat zien op welke claim deze comment is geplaatst
            const claimTitel = note.claim_title ? note.claim_title : 'Onbekende claim';
            
            html += `
                <div class="note-box" style="background: #111b2a; border: 1px solid #2a3b52; padding: 12px; border-radius: 8px; cursor: pointer;" onclick="window.location.href='article.php?id=${note.claim_id}'">
                    <div style="font-size: 11px; color: var(--cyan); margin-bottom: 6px;">
                        Op claim: <strong>${claimTitel}</strong>
                    </div>
                    <p style="margin: 0 0 4px 0; color: var(--white); font-size: 13px;">"${note.text}"</p>
                    <small style="color: #888; font-size: 11px;">${note.timestamp}</small>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }
    </script>
</body>
</html>