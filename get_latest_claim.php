<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/database.php';

$statement = $pdo->query(
    'SELECT id, Title, source, image_path, timestamp FROM claims ORDER BY id DESC LIMIT 1'
);
$claim = $statement->fetch() ?: null;

echo json_encode([
    'success' => true,
    'data' => $claim,
], JSON_UNESCAPED_UNICODE);