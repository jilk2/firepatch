<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $checkStmt = $pdo->prepare("SELECT * FROM accounts WHERE Email = ?");
        $checkStmt->execute([$data->email]);
        
        if($checkStmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["success" => false, "message" => "Dit e-mailadres is al geregistreerd in het systeem."]);
        } else {
            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
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
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerifyNET - Register</title>
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/verifinet.css" />
</head>
<body>
    <?php include("./partials/header.php"); ?>
    
    <div class="layout short">
        <?php include("./partials/sidebar.php"); ?>
        
        <main class="page" style="display: flex; justify-content: center; align-items: center;">
            <div class="card" style="width: 100%; max-width: 450px; padding: 25px;">
                <div class="card-head">
                    <h2>Node Registratie</h2>
                </div>
                
                <form id="register-form" onsubmit="submitRegister(event)" style="margin-top: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--text);">E-mailadres</label>
                        <input type="email" id="email" class="input" required placeholder="naam@domein.nl">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--text);">Wachtwoord</label>
                        <input type="password" id="password" class="input" required placeholder="••••••••">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--text);">Bevestig Wachtwoord</label>
                        <input type="password" id="confirm_password" class="input" required placeholder="••••••••">
                    </div>
                    
                    <button type="submit" class="btn primary" style="width: 100%; margin-bottom: 15px;">REGISTREREN</button>
                    
                    <p style="text-align: center; font-size: 13px; color: #888;">
                        Al een account? <a href="login.php" style="color: var(--cyan); text-decoration: none;">Log hier in</a>
                    </p>
                </form>
            </div>
        </main>
    </div>

    <script>
    async function submitRegister(event) {
        event.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Check of wachtwoorden overeenkomen
        if (password !== confirmPassword) {
            alert("Fout: De ingevoerde wachtwoorden komen niet met elkaar overeen.");
            return;
        }

        try {
            const response = await fetch('register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const result = await response.json();

            if (result.success) {
                alert("Registratie gelukt! Je kunt nu inloggen.");
                window.location.href = 'login.php';
            } else {
                alert("Fout: " + result.message);
            }
        } catch (err) {
            console.error(err);
            alert("Kan geen verbinding maken met de server.");
        }
    }
    </script>
</body>
</html>