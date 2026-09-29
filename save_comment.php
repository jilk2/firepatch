<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require 'database.php';
$data = json_decode(file_get_contents("php://input"));


$admin_emails = ["admin@firepatch.nl"]; 

if(isset($data->claim_id) && isset($data->text) && isset($data->author_email)) {
    
    $source = !empty($data->source) ? $data->source : 'Geen bron opgegeven';
    
   
    $stmt = $pdo->prepare("INSERT INTO community_notes (claim_id, author_email, text, source) VALUES (?, ?, ?, ?)");
    
    if($stmt->execute([$data->claim_id, $data->author_email, $data->text, $source])) {
        
        $isAdmin = in_array($data->author_email, $admin_emails);
        $newStatus = null;

        if ($isAdmin) {
            
            $text = $data->text;
            
            $isFalse = preg_match('/\b(incorrect|niet klopt|klopt niet|niet waar|onwaar|nep|fake)\b/i', $text);
            $cleanText = preg_replace('/\b(incorrect|niet klopt|klopt niet|niet waar|onwaar|nep|fake)\b/i', '', $text);
            $isTrue = preg_match('/\b(waar|klopt|correct|echt|juist)\b/i', $cleanText);

            if ($isFalse) {
                $newStatus = "false";
            } elseif ($isTrue) {
                $newStatus = "true";
            }

        } else {
            
            $claimStmt = $pdo->prepare("SELECT author_email FROM claims WHERE id = ?");
            $claimStmt->execute([$data->claim_id]);
            $claim = $claimStmt->fetch();
            
            
            $notesStmt = $pdo->prepare("SELECT author_email, text FROM community_notes WHERE claim_id = ?");
            $notesStmt->execute([$data->claim_id]);
            $allNotes = $notesStmt->fetchAll();

            
            $adminInvolved = false;
            
            
            if ($claim && in_array($claim['author_email'], $admin_emails)) {
                $adminInvolved = true;
            } else {
                // 2. Heeft een admin hier in het verleden al een comment op geplaatst?
                foreach($allNotes as $note) {
                    if (in_array($note['author_email'], $admin_emails)) {
                        $adminInvolved = true;
                        break;
                    }
                }
            }

            
            if (!$adminInvolved) {
                $true_votes = 0;
                $false_votes = 0;

                foreach($allNotes as $note) {
                    $text = $note['text'];
                    $isFalse = preg_match('/\b(incorrect|niet klopt|klopt niet|niet waar|onwaar|nep|fake)\b/i', $text);
                    $cleanText = preg_replace('/\b(incorrect|niet klopt|klopt niet|niet waar|onwaar|nep|fake)\b/i', '', $text);
                    $isTrue = preg_match('/\b(waar|klopt|correct|echt|juist)\b/i', $cleanText);

                    if ($isFalse) $false_votes++;
                    if ($isTrue) $true_votes++;
                }
                
                if ($true_votes >= 3 || $false_votes >= 3) {
                    if ($true_votes > $false_votes) {
                        $newStatus = "true";
                    } elseif ($false_votes > $true_votes) {
                        $newStatus = "false";
                    }
                }
            }
        }

        
        if ($newStatus !== null) {
            $updateStmt = $pdo->prepare("UPDATE claims SET status = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $data->claim_id]);
        }

        echo json_encode([
            "success" => true, 
            "message" => $isAdmin ? "Admin override toegepast." : "Notitie opgeslagen."
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