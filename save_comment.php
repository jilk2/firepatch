<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require 'database.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->claim_id) && isset($data->text) && isset($data->author_email)) {
    
    $source = !empty($data->source) ? $data->source : 'Geen bron opgegeven';
    
    // 1. Sla de nieuwe notitie op
    $stmt = $pdo->prepare("INSERT INTO community_notes (claim_id, author_email, text, source) VALUES (?, ?, ?, ?)");
    
    if($stmt->execute([$data->claim_id, $data->author_email, $data->text, $source])) {
        
        // --- START CONSENSUS ALGORITME ---
        
        // --- START CONSENSUS ALGORITME ---
        
        $notesStmt = $pdo->prepare("SELECT text FROM community_notes WHERE claim_id = ?");
        $notesStmt->execute([$data->claim_id]);
        $allNotes = $notesStmt->fetchAll();

        $true_votes = 0;
        $false_votes = 0;

        foreach($allNotes as $note) {
            $text = $note['text'];
            
            // Zoek heel exact naar negatieve beweringen (met \b voor hele woorden)
            $isFalse = preg_match('/\b(incorrect|niet klopt|klopt niet|niet waar|onwaar|nep|fake)\b/i', $text);
            
            // Haal deze termen tijdelijk weg zodat ze niet overlappen met positieve woorden
            $cleanText = preg_replace('/\b(incorrect|niet klopt|klopt niet|niet waar|onwaar|nep|fake)\b/i', '', $text);
            
            //  Zoek naar positieve beweringen in de overgebleven tekst
            $isTrue = preg_match('/\b(waar|klopt|correct|echt|juist)\b/i', $cleanText);

            if ($isFalse) $false_votes++;
            if ($isTrue) $true_votes++;
        }

        $newStatus = null;
        
        // Zodra 3 of meer mensen aan één kant staan, checken we de winnaar
        if ($true_votes >= 3 || $false_votes >= 3) {
            if ($true_votes > $false_votes) {
                $newStatus = "true";
            } elseif ($false_votes > $true_votes) {
                $newStatus = "false";
            }
        }

        if ($newStatus !== null) {
            $updateStmt = $pdo->prepare("UPDATE claims SET Status = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $data->claim_id]);
        }

        // --- EINDE CONSENSUS ALGORITME ---

        // --- EINDE CONSENSUS ALGORITME ---

        echo json_encode([
            "success" => true, 
            "message" => "Notitie opgeslagen. Consensus gecheckt."
        ]);
        
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Fout bij opslaan notitie."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Niet alle velden zijn ingevuld."]);
}
?>