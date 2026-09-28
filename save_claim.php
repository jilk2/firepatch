<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: POST, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type");
// header("Content-Type: application/json; charset=UTF-8");

// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

// require 'database.php';
// $data = json_decode(file_get_contents("php://input"));

// if(isset($data->title) && isset($data->author_email)) {
//     $status = "pending"; 
    
//     $description = isset($data->description) ? $data->description : '';
//     $source = isset($data->source) ? $data->source : '';
    
    
//     $stmt = $pdo->prepare("INSERT INTO claims (Title, description, source, Status, author_email) VALUES (?, ?, ?, ?, ?)");
    
//     if($stmt->execute([$data->title, $description, $source, $status, $data->author_email])) {
//         echo json_encode(["success" => true, "message" => "Claim succesvol ingediend.", "id" => $pdo->lastInsertId()]);
//     } else {
//         http_response_code(500);
//         echo json_encode(["success" => false, "message" => "Fout bij opslaan claim in database."]);
//     }
// } else {
//     http_response_code(400);
//     echo json_encode(["success" => false, "message" => "Fout: Titel en auteur ontbreken."]);
// }

session_start();
date_default_timezone_set("Europe/Amsterdam");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "Selected sector: " . $_POST["sector"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="time">Time:</label>
        <input type="text" id="" name="text" value="<?= date("H:i"); ?>" disabled>
        
        Activity: <input type="text" name="name"><br>

        <label for="sector">Choose a sector:</label>
        <select name="sector" id="sector">
            <?php for ($i = 1; $i <= 32; $i++): ?>
                <option value="<?= $i ?>">Sector <?= $i ?></option>
            <?php endfor; ?>
        </select>

        Select image to upload: <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" name="submit">
    </form>
</body>

</html>