<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: save_claim.php', true, 303);
    exit;
}

require_once __DIR__ . '/database.php';

$adminEmails = ['admin@firepatch.nl'];

$title = trim((string) ($_POST['title'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$source = trim((string) ($_POST['source'] ?? ''));
$sector = filter_var($_POST['sector'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 36],
]);
$authorEmail = trim((string) ($_POST['author_email'] ?? ''));

if ($title === '' || strlen($title) > 255) {
    failUpload('Vul een geldige claimtitel in.');
}

if (!filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
    failUpload('Log opnieuw in voordat je een claim plaatst.');
}

if ($source === '' && $sector !== false) {
    $source = 'Sector ' . $sector;
} elseif ($sector !== false && !str_contains($source, 'Sector ')) {
    $source .= ' (Sector ' . $sector . ')';
}

if (strlen($source) > 2048) {
    failUpload('De bron of locatie is te lang.');
}


$x_value = null;
$y_value = null;

if ($sector !== false) {
    $x_value = ($sector - 1) % 6; 
    $y_value = floor(($sector - 1) / 6); 
}


$claimStatus = in_array($authorEmail, $adminEmails, true) ? 'true' : 'pending';

$relativeImagePath = null;
$absoluteImagePath = null;
$upload = $_FILES['evidence'] ?? null;

if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        failUpload('De afbeelding kon niet worden ontvangen.');
    }

    if (($upload['size'] ?? 0) < 1 || $upload['size'] > 5 * 1024 * 1024) {
        failUpload('De afbeelding moet kleiner zijn dan 5 MB.');
    }

    if (!class_exists('finfo')) {
        failUpload('De PHP-extensie fileinfo is niet ingeschakeld.');
    }

    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file((string) $upload['tmp_name']);
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedTypes[$mimeType])) {
        failUpload('Alleen JPG-, PNG- en WebP-afbeeldingen zijn toegestaan.');
    }

    $uploadDirectory = __DIR__ . '/uploads';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0750, true) && !is_dir($uploadDirectory)) {
        failUpload('De uploadmap kon niet worden aangemaakt.');
    }

    if (!is_writable($uploadDirectory)) {
        @chmod($uploadDirectory, 0775);
        clearstatcache(true, $uploadDirectory);
    }

    if (!is_writable($uploadDirectory)) {
        failUpload('De map uploads is niet schrijfbaar door de lokale PHP-webserver. Controleer de maprechten van uploads.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
    $relativeImagePath = 'uploads/' . $filename;
    $absoluteImagePath = $uploadDirectory . '/' . $filename;

    if (!move_uploaded_file((string) $upload['tmp_name'], $absoluteImagePath)) {
        failUpload('De afbeelding kon niet worden opgeslagen.');
    }
}

try {
    
    $statement = $pdo->prepare(
        'INSERT INTO claims (Title, description, source, status, author_email, image_path, x_value, y_value) '
        . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    
    $statement->execute([
        $title,
        $description !== '' ? $description : null,
        $source !== '' ? $source : null,
        $claimStatus, 
        $authorEmail,
        $relativeImagePath,
        $x_value,
        $y_value
    ]);

    $claimId = (int) $pdo->lastInsertId();
    header('Location: article.php?id=' . $claimId, true, 303);
    exit;
} catch (Throwable $exception) {
    if ($absoluteImagePath !== null && is_file($absoluteImagePath)) {
        unlink($absoluteImagePath);
    }

    error_log('Firepatch claim opslaan mislukt: ' . $exception->getMessage());
    failUpload('De claim kon niet in de database worden opgeslagen. Controleer of het actuele databaseschema is geïmporteerd.');
}

function failUpload(string $message): never
{
    http_response_code(400);
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    echo '<!doctype html><html lang="nl"><head><meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Upload mislukt</title><link rel="stylesheet" href="css/main.css"></head>'
        . '<body><main class="page"><section class="card" style="padding:24px;max-width:760px;margin:40px auto">'
        . '<h1>Claim niet opgeslagen</h1><p>' . $safeMessage . '</p>'
        . '<a class="btn primary" href="save_claim.php">Terug</a></section></main></body></html>';
    exit;
}