<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Alleen POST-verzoeken zijn toegestaan.',
    ]);
    exit;
}

require_once __DIR__ . '/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$sectorNumber = filter_var($data['sector_number'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 36],
]);
$state = $data['state'] ?? null;

if ($sectorNumber === false || !in_array($state, ['Healthy', 'problematic'], true)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Ongeldig sectornummer of ongeldige sectorstatus.',
    ]);
    exit;
}

try {
    $statement = $pdo->prepare(
        'UPDATE map_sectors SET state = ? WHERE sector_number = ?'
    );
    $statement->execute([$state, $sectorNumber]);

    echo json_encode([
        'success' => true,
        'sector_number' => $sectorNumber,
        'state' => $state,
    ]);
} catch (Throwable $exception) {
    error_log('Firepatch sectorstatus opslaan mislukt: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'De sectorstatus kon niet worden opgeslagen.',
    ]);
}
