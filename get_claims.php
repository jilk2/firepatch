<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require 'database.php';

// Haal alle claims op en tel hoeveel comments elke claim heeft
$stmt = $pdo->query("
    SELECT c.*, COUNT(n.id) as note_count 
    FROM claims c 
    LEFT JOIN community_notes n ON c.id = n.claim_id 
    GROUP BY c.id 
    ORDER BY c.timestamp DESC
");

$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "data" => $claims]);
?>