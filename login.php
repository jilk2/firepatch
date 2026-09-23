<?php
// 1. Als het een POST verzoek is via JavaScript fetch, handel dan de API backend af
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
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE Email = ?");
        $stmt->execute([$data->email]);
        $user = $stmt->fetch();

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
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerifyNET - Login</title>
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
                    <h2>Node Authenticatie</h2>
                </div>
                
                <form id="login-form" onsubmit="submitLogin(event)" style="margin-top: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--text);">E-mailadres</label>
                        <input type="email" id="email" class="input" required placeholder="naam@domein.nl">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--text);">Wachtwoord</label>
                        <input type="password" id="password" class="input" required placeholder="••••••••">
                    </div>
                    
                    <button type="submit" class="btn primary" style="width: 100%; margin-bottom: 15px;">INLOGGEN</button>
                    
                    <p style="text-align: center; font-size: 13px; color: #888;">
                        Nog geen node-account? <a href="register.php" style="color: var(--cyan); text-decoration: none;">Registreer hier</a>
                    </p>
                </form>
            </div>
        </main>
    </div>

    <script>
    async function submitLogin(event) {
        event.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const result = await response.json();

            if (result.success) {
                localStorage.setItem('verifinet_user', result.email);
                window.location.href = 'verifynet.php';
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