<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require 'database.php';
$data = json_decode(file_get_contents("php://input"));

// VUL HIER JOUW ADMIN E-MAILS IN
$admin_emails = ["admin@firepatch.nl"]; 

if (isset($data->claim_id) && isset($data->email)) {
    // Strenge check: Is de persoon die dit aanvraagt wel echt een admin?
    if (in_array($data->email, $admin_emails)) {
        try {
            // 1. Verwijder eerst alle comments/notities die bij deze claim horen
            $stmtNotes = $pdo->prepare("DELETE FROM community_notes WHERE claim_id = ?");
            $stmtNotes->execute([$data->claim_id]);

            // 2. Verwijder de claim zelf
            $stmtClaim = $pdo->prepare("DELETE FROM claims WHERE id = ?");
            $stmtClaim->execute([$data->claim_id]);

            echo json_encode(["success" => true, "message" => "Claim succesvol verwijderd."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Fout bij verwijderen van claim in de database."]);
        }
    } else {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Toegang geweigerd: Je hebt geen admin-rechten."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Niet alle benodigde gegevens zijn meegestuurd."]);
}
?>