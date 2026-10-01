<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respondJson(405, 'method_not_allowed', 'Gebruik een POST-request.');
}

$contentType = strtolower(trim(explode(';', (string) ($_SERVER['CONTENT_TYPE'] ?? ''))[0]));
if ($contentType !== 'application/json') {
    respondJson(415, 'unsupported_media_type', 'Gebruik Content-Type: application/json.');
}

require_once __DIR__ . '/DB/DBConnect.php';

$rawBody = file_get_contents('php://input');
$maxJsonBytes = 8 * 1024 * 1024;
if ($rawBody === false || $rawBody === '') {
    respondJson(400, 'invalid_body', 'De JSON-body ontbreekt.');
}
if (strlen($rawBody) > $maxJsonBytes) {
    respondJson(413, 'body_too_large', 'De JSON-body mag maximaal 8 MB zijn.');
}

try {
    $payload = json_decode($rawBody, true, 32, JSON_THROW_ON_ERROR);
} catch (JsonException) {
    respondJson(400, 'invalid_json', 'De request-body bevat geen geldige JSON.');
}

if (!is_array($payload) || array_is_list($payload)) {
    respondJson(400, 'invalid_body', 'De JSON-body moet een object zijn.');
}

$payload = normalizeClaimPayload($payload);

$title = readText($payload, 'title', true, 255);
$description = readText($payload, 'description', false, 10000);
$source = readText($payload, 'source', false, 255);
$authorEmail = readText($payload, 'author_email', true, 255);

if (!filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
    respondJson(422, 'validation_error', 'author_email moet een geldig e-mailadres zijn.', 'author_email');
}

$sector = readSector($payload);
[$xValue, $yValue] = coordinatesForSector($sector);

if ($source === '' && $sector !== null) {
    $source = 'Sector ' . $sector;
}

$storedImage = null;
try {
    $storedImage = storeOptionalImage($payload);
} catch (Throwable $exception) {
    error_log('Firepatch Unreal API-afbeelding opslaan mislukt: ' . $exception->getMessage());
    respondJson(500, 'image_storage_error', 'De bewijsafbeelding kon niet worden opgeslagen.');
}

try {
    $pdo = firepatchPdo();
    $statement = $pdo->prepare(
        'INSERT INTO claims '
        . '(title, description, source, status, author_email, image_path, x_value, y_value) '
        . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $statement->execute([
        $title,
        $description !== '' ? $description : null,
        $source !== '' ? $source : null,
        'pending',
        $authorEmail,
        $storedImage['relative_path'] ?? null,
        $xValue,
        $yValue,
    ]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'claim' => [
            'id' => (int) $pdo->lastInsertId(),
            'title' => $title,
            'status' => 'pending',
            'sector' => $sector,
            'image_path' => $storedImage['relative_path'] ?? null,
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $exception) {
    if ($storedImage !== null && is_file($storedImage['absolute_path'])) {
        @unlink($storedImage['absolute_path']);
    }

    error_log('Firepatch Unreal claim opslaan mislukt: ' . $exception->getMessage());
    respondJson(500, 'database_error', 'De claim kon niet worden opgeslagen.');
}

/**
 * Blueprint-structs kunnen veldnamen als Title, AuthorEmail of image_base64
 * opleveren. Zet de bekende varianten om naar de vaste API-veldnamen.
 */
function normalizeClaimPayload(array $payload): array
{
    $fieldNames = [
        'title' => 'title',
        'description' => 'description',
        'source' => 'source',
        'sector' => 'sector',
        'authoremail' => 'author_email',
        'imagemime' => 'image_mime',
        'imagebase64' => 'image_base64',
    ];
    $normalized = [];

    foreach ($payload as $field => $value) {
        if (!is_string($field)) {
            continue;
        }

        $lookup = strtolower((string) preg_replace('/[^a-z0-9]+/i', '', $field));
        $canonicalField = $fieldNames[$lookup] ?? null;

        if ($canonicalField !== null && !array_key_exists($canonicalField, $normalized)) {
            $normalized[$canonicalField] = $value;
        }
    }

    return $normalized;
}

function readText(array $payload, string $field, bool $required, int $maxLength): string
{
    if (!array_key_exists($field, $payload) || $payload[$field] === null) {
        if ($required) {
            respondJson(422, 'validation_error', $field . ' is verplicht.', $field);
        }

        return '';
    }

    if (!is_string($payload[$field])) {
        respondJson(422, 'validation_error', $field . ' moet tekst zijn.', $field);
    }

    $value = trim($payload[$field]);
    if ($required && $value === '') {
        respondJson(422, 'validation_error', $field . ' mag niet leeg zijn.', $field);
    }
    if (strlen($value) > $maxLength) {
        respondJson(
            422,
            'validation_error',
            $field . ' mag maximaal ' . $maxLength . ' tekens bevatten.',
            $field
        );
    }

    return $value;
}

function readSector(array $payload): ?int
{
    if (!array_key_exists('sector', $payload) || $payload['sector'] === null || $payload['sector'] === '') {
        return null;
    }

    $sector = filter_var($payload['sector'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 36],
    ]);

    if ($sector === false) {
        respondJson(422, 'validation_error', 'sector moet een geheel getal van 1 t/m 36 zijn.', 'sector');
    }

    return (int) $sector;
}

function coordinatesForSector(?int $sector): array
{
    if ($sector === null) {
        return [null, null];
    }

    $column = ($sector - 1) % 6;
    $row = intdiv($sector - 1, 6);

    return [
        ($column + 0.5) / 6,
        ($row + 0.5) / 6,
    ];
}

/**
 * image_base64 mag ruwe Base64 zijn met image_mime, of een volledige data-URL.
 * Geeft null terug als er geen afbeelding is meegestuurd.
 */
function storeOptionalImage(array $payload): ?array
{
    $hasImage = array_key_exists('image_base64', $payload)
        && $payload['image_base64'] !== null
        && $payload['image_base64'] !== '';

    if (!$hasImage) {
        if (array_key_exists('image_mime', $payload)
            && $payload['image_mime'] !== null
            && (!is_string($payload['image_mime']) || trim($payload['image_mime']) !== '')) {
            respondJson(
                422,
                'validation_error',
                'image_mime is alleen geldig samen met image_base64.',
                'image_mime'
            );
        }

        return null;
    }

    if (!is_string($payload['image_base64'])) {
        respondJson(422, 'validation_error', 'image_base64 moet tekst zijn.', 'image_base64');
    }

    $encoded = trim($payload['image_base64']);
    $suppliedMime = '';

    if (str_starts_with($encoded, 'data:')) {
        if (!preg_match('/^data:(image\/(?:jpeg|png|webp));base64,(.+)$/is', $encoded, $matches)) {
            respondJson(
                422,
                'validation_error',
                'De data-URL moet een Base64 JPEG-, PNG- of WebP-afbeelding bevatten.',
                'image_base64'
            );
        }

        $suppliedMime = strtolower($matches[1]);
        $encoded = $matches[2];
    } else {
        if (!array_key_exists('image_mime', $payload) || !is_string($payload['image_mime'])) {
            respondJson(422, 'validation_error', 'image_mime is verplicht bij ruwe Base64.', 'image_mime');
        }

        $suppliedMime = strtolower(trim($payload['image_mime']));
    }

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (!array_key_exists($suppliedMime, $allowedMimeTypes)) {
        respondJson(
            422,
            'validation_error',
            'image_mime moet image/jpeg, image/png of image/webp zijn.',
            'image_mime'
        );
    }

    $encoded = preg_replace('/\s+/', '', $encoded);
    $binary = is_string($encoded) ? base64_decode($encoded, true) : false;
    if ($binary === false || $binary === '') {
        respondJson(422, 'validation_error', 'image_base64 bevat geen geldige afbeelding.', 'image_base64');
    }
    if (strlen($binary) > 5 * 1024 * 1024) {
        respondJson(413, 'image_too_large', 'De uitgepakte afbeelding mag maximaal 5 MB zijn.', 'image_base64');
    }

    if (!class_exists('finfo')) {
        throw new RuntimeException('De PHP-extensie fileinfo is niet ingeschakeld.');
    }

    $detectedMime = (new finfo(FILEINFO_MIME_TYPE))->buffer($binary);
    if (!is_string($detectedMime)
        || !array_key_exists($detectedMime, $allowedMimeTypes)
        || $detectedMime !== $suppliedMime) {
        respondJson(
            422,
            'validation_error',
            'De inhoud van de afbeelding komt niet overeen met image_mime.',
            'image_base64'
        );
    }

    $uploadDirectory = __DIR__ . '/uploads';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('De uploads-map kon niet worden aangemaakt.');
    }
    if (!is_writable($uploadDirectory)) {
        throw new RuntimeException('De uploads-map is niet schrijfbaar.');
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedMimeTypes[$detectedMime];
    $absolutePath = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;
    $writtenBytes = file_put_contents($absolutePath, $binary, LOCK_EX);
    if ($writtenBytes === false || $writtenBytes !== strlen($binary)) {
        @unlink($absolutePath);
        throw new RuntimeException('De afbeelding kon niet volledig worden geschreven.');
    }

    return [
        'relative_path' => 'uploads/' . $fileName,
        'absolute_path' => $absolutePath,
    ];
}

function respondJson(int $status, string $error, string $message, ?string $field = null): never
{
    http_response_code($status);
    $response = [
        'success' => false,
        'error' => $error,
        'message' => $message,
    ];

    if ($field !== null) {
        $response['field'] = $field;
    }

    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
