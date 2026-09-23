<?php
<<<<<<< HEAD



=======
// 1. Zeg tegen de browser: "Alles is welkom, ook Vite op poort 5173!"
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    require 'database.php';

    $json = file_get_contents("php://input");
    $data = json_decode($json);

    if(isset($data->email) && isset($data->password)) {
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE Email = ?");
        $stmt->execute([$data->email]);
        $user = $stmt->fetch();

    // 2. Controleer of de gebruiker bestaat, EN of het wachtwoord klopt met de hash
    if($user && password_verify($data->password, $user['Password'])) {
        
        echo json_encode([
            "success" => true,
            "message" => "Succesvol ingelogd.",
            "email" => $user['Email']
        ]);

    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false, 
            "message" => "Toegang geweigerd. E-mail of wachtwoord onjuist."
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Fout: E-mail en wachtwoord ontbreken."]);
}
>>>>>>> main
?>