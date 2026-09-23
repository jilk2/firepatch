<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/header.css"/>
</head>
<body>
    <header>
        <div class="brand">
            <span class="shield">◈</span>
            <div>
                <h1><span>FIRE</span>PATCH</h1>
                <p>AUTONOMOUS BIOSPHERE DEFENSE</p>
            </div>
        </div>
        <div class="topbar-right">
            <span class="online-dot"></span>
            <span class="status">Connection: ONLINE</span>
            <div class="user">
                <div>
                    <strong id="header-user-email">Gast</strong>
                    <!-- HIER ZAT DE FOUT: id toegevoegd zodat JS de tekst kan wijzigen -->
                    <p id="header-user-role">Niet ingelogd</p>
                </div>
                <div class="avatar" id="header-avatar">?</div>
                <button id="auth-action-btn" onclick="handleHeaderAuth()" class="btn ghost" style="padding: 6px 12px; font-size: 11px; cursor: pointer; margin-left: 10px;">Login</button>
            </div>
        </div>
    </header>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const loggedInUser = localStorage.getItem('verifinet_user');
        const emailEl = document.getElementById('header-user-email');
        const roleEl = document.getElementById('header-user-role');
        const avatarEl = document.getElementById('header-avatar');
        const authBtn = document.getElementById('auth-action-btn');

        if (loggedInUser) {
            emailEl.textContent = loggedInUser;
            roleEl.textContent = "VerifiNET Node"; // Dit verandert nu feilloos mee!
            avatarEl.textContent = loggedInUser.substring(0, 2).toUpperCase();
            authBtn.textContent = "Uitloggen";
            authBtn.style.borderColor = "var(--red)";
            authBtn.style.color = "var(--red)";
        } else {
            emailEl.textContent = "Bezoeker";
            roleEl.textContent = "Niet ingelogd";
            avatarEl.textContent = "?";
            authBtn.textContent = "Login";
        }
    });

    function handleHeaderAuth() {
        const loggedInUser = localStorage.getItem('verifinet_user');
        if (loggedInUser) {
            localStorage.removeItem('verifinet_user');
            window.location.href = 'login.php';
        } else {
            window.location.href = 'login.php';
        }
    }
    </script>
</body>
</html>