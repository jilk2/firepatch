<?php

declare(strict_types=1);

require_once __DIR__ . '/DB/DBConnect.php';

try {
    $pdo = firepatchPdo();
} catch (Throwable $exception) {
    error_log('Firepatch databasefout: ' . $exception->getMessage());
    http_response_code(503);
    die(json_encode([
        'success' => false,
        'error' => 'De database is tijdelijk niet beschikbaar.',
    ]));
}
