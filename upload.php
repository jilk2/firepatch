<?php
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Ongeldige request.");
}

$title = trim($_POST["name"] ?? "");
$time = trim($_POST["time"] ?? "");
$sector = filter_var($_POST["sector"] ?? null, FILTER_VALIDATE_INT);
$authorEmail = trim($_POST["author_email"] ?? "");

if ($title === "" || $time === "" || $sector === false || $sector < 1 || $sector > 32 || $authorEmail === "") {
    die("Vul een activiteit, sector en ingelogde gebruiker in.");
}

$imagePath = null;

if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
        die("De foto kon niet worden geüpload.");
    }

    $allowedTypes = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp",
    ];

    $finfo = @getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($finfo === false || !isset($allowedTypes[$finfo["mime"]])) {
        die("Kies een geldige JPG, PNG, GIF of WebP foto.");
    }

    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        die("Uploadmap kon niet worden aangemaakt.");
    }

    $extension = $allowedTypes[$finfo["mime"]];
    $fileName = uniqid("claim_", true) . "." . $extension;
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetPath)) {
        die("De foto kon niet worden opgeslagen.");
    }

    $imagePath = "uploads/" . $fileName;
}

$description = "Sector " . $sector . " | Tijd: " . $time;

$stmt = $pdo->prepare("
    INSERT INTO claims (Title, description, source, Status, author_email, image_path)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $title,
    $description,
    "",
    "pending",
    $authorEmail,
    $imagePath
]);

header("Location: verifynet.php");
exit;
?>
