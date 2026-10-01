<?php

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST, OPTIONS');
    resolveClaimResponse(405, false, 'Gebruik een POST-request.');
}

$contentType = strtolower(trim(explode(';', (string) ($_SERVER['CONTENT_TYPE'] ?? ''))[0]));
if ($contentType !== 'application/json') {
    resolveClaimResponse(415, false, 'Gebruik Content-Type: application/json.');
}

$rawBody = file_get_contents('php://input');
if ($rawBody === false || trim($rawBody) === '') {
    resolveClaimResponse(400, false, 'De JSON-body ontbreekt.');
}
if (strlen($rawBody) > 16384) {
    resolveClaimResponse(413, false, 'De JSON-body is te groot.');
}

try {
    $payload = json_decode($rawBody, true, 16, JSON_THROW_ON_ERROR);
} catch (JsonException) {
    resolveClaimResponse(400, false, 'De request-body bevat geen geldige JSON.');
}

if (!is_array($payload) || array_is_list($payload)) {
    resolveClaimResponse(400, false, 'De JSON-body moet een object zijn.');
}

$sectorValue = null;
foreach ($payload as $field => $value) {
    if (!is_string($field)) {
        continue;
    }

    $normalizedField = strtolower((string) preg_replace('/[^a-z0-9]+/i', '', $field));
    if (in_array($normalizedField, ['sector', 'sectorid'], true)) {
        $sectorValue = $value;
        break;
    }
}

$sectorId = filter_var($sectorValue, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 36],
]);
if ($sectorId === false) {
    resolveClaimResponse(422, false, 'sector moet een geheel getal van 1 t/m 36 zijn.');
}

require_once __DIR__ . '/DB/DBConnect.php';

try {
    $pdo = firepatchPdo();
    $claims = $pdo->query(
        'SELECT id, status, source, x_value, y_value FROM claims'
    )->fetchAll(PDO::FETCH_ASSOC);

    $claimIds = [];
    foreach ($claims as $claim) {
        $claim = firepatchNormalizeClaimRow($claim);
        $status = strtolower(trim((string) ($claim['status'] ?? '')));

        if (in_array($status, ['resolved', 'true', 'false'], true)) {
            continue;
        }

        if (firepatchClaimSectorNumber($claim) === (int) $sectorId) {
            $claimIds[] = (int) $claim['id'];
        }
    }

    if ($claimIds !== []) {
        $placeholders = implode(',', array_fill(0, count($claimIds), '?'));
        $statement = $pdo->prepare(
            "UPDATE claims SET status = 'resolved' WHERE id IN ({$placeholders})"
        );
        $statement->execute($claimIds);
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'sector' => (int) $sectorId,
        'resolved_count' => count($claimIds),
        'claim_ids' => $claimIds,
        'message' => $claimIds === []
            ? 'Er waren geen openstaande claims in deze sector.'
            : 'De openstaande claims in deze sector zijn opgelost.',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $exception) {
    error_log('Firepatch sector oplossen mislukt: ' . $exception->getMessage());
    resolveClaimResponse(500, false, 'De claims konden niet worden bijgewerkt.');
}

function resolveClaimResponse(int $statusCode, bool $success, string $message): never
{
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
