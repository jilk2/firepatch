<?php
header('Content-Type: application/json; charset=UTF-8');

require 'database.php';

try {
    $stmt = $pdo->query(
        'SELECT sector_number, state FROM map_sectors ORDER BY sector_number'
    );

    echo json_encode([
        'success' => true,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
} catch (PDOException $error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sector data is niet beschikbaar.',
    ]);
}