<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/database.php';

try {
    $statement = $pdo->query(
        'SELECT id, Title, source, image_path, timestamp
         FROM claims
         ORDER BY id DESC
         LIMIT 1'
    );

    $claim = $statement->fetch() ?: null;

    echo json_encode([
        'success' => true,
        'data' => $claim,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    error_log('Latest claim ophalen mislukt: ' . $exception->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'De nieuwste claim kon niet worden opgehaald.',
    ]);
}

echo json_encode([
    'success' => true,
    'data' => $claim,
], JSON_UNESCAPED_UNICODE);