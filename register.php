
<?php
// 1. Zeg tegen de browser: "Alles is welkom, ook Vite op poort 5173!"
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// 2. Vang het onzichtbare 'klopje op de deur' (OPTIONS verzoek) van de browser af
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'database.php';

$json = file_get_contents("php://input");
$data = json_decode($json);

if(isset($data->email) && isset($data->password)) {
    
    // 1. Controleer of het e-mailadres al bestaat in de tabel 'accounts'
    $checkStmt = $pdo->prepare("SELECT * FROM accounts WHERE Email = ?");
    $checkStmt->execute([$data->email]);
    
    if($checkStmt->rowCount() > 0) {
        // E-mailadres is al in gebruik
        http_response_code(409); // Conflict
        echo json_encode(["success" => false, "message" => "Dit e-mailadres is al geregistreerd in het systeem."]);
    } else {
        // 2. Hash het wachtwoord veilig!
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
        
        // 3. Sla de nieuwe gebruiker op in de database
        $insertStmt = $pdo->prepare("INSERT INTO accounts (Email, Password) VALUES (?, ?)");
        
        if($insertStmt->execute([$data->email, $hashedPassword])) {
            echo json_encode(["success" => true, "message" => "Account succesvol aangemaakt."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Systeemfout: Kon account niet opslaan."]);
        }
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Fout: E-mail en wachtwoord zijn verplicht."]);
}
?>