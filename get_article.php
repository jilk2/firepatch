<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require 'database.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Haal de claim zelf op
    $stmt = $pdo->prepare("SELECT * FROM claims WHERE id = ?");
    $stmt->execute([$id]);
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);

    if($claim) {
        // 2. Haal alle bijbehorende notes op
        $stmtNotes = $pdo->prepare("SELECT * FROM community_notes WHERE claim_id = ? ORDER BY timestamp DESC");
        $stmtNotes->execute([$id]);
        $claim['notes'] = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $claim]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Claim niet gevonden."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Geen ID opgegeven."]);
}
?>