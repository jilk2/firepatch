<?php

declare(strict_types=1);

require_once __DIR__ . '/../DB/DBConnect.php';

try {
    $db = firepatchMysqli();
} catch (Throwable $exception) {
    error_log('Firepatch databasefout: ' . $exception->getMessage());
    http_response_code(503);
    exit('De database is tijdelijk niet beschikbaar. Controleer de centrale databaseconfiguratie.');
}
