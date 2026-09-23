<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require 'database.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->title) && isset($data->author_email)) {
    $status = "pending"; 
    
    $description = isset($data->description) ? $data->description : '';
    $source = isset($data->source) ? $data->source : '';
    
    
    $stmt = $pdo->prepare("INSERT INTO claims (Title, description, source, Status, author_email) VALUES (?, ?, ?, ?, ?)");
    
    if($stmt->execute([$data->title, $description, $source, $status, $data->author_email])) {
        echo json_encode(["success" => true, "message" => "Claim succesvol ingediend.", "id" => $pdo->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Fout bij opslaan claim in database."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Fout: Titel en auteur ontbreken."]);
}
?>