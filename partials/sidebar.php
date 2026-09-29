<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/sidebar.css" />
    <script src="./js/connection.js" defer></script>
</head>

<body>
    
    <aside id="firepatch-sidebar" style="display: none;"> 
        <nav>
            <?php $currentpage = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php" class="nav-item <?= $currentpage === 'index.php' ? ' current' : '' ?>"><span class="nav-icon">⌂</span>Overzicht</a>
            <a href="mission.php" class="nav-item <?= $currentpage === 'mission.php' ? ' current' : '' ?>"><span class="nav-icon">●</span>Missies</a>
            <a href="kaart.php" class="nav-item <?= $currentpage === 'kaart.php' ? ' current' : '' ?>"><span class="nav-icon">🗺</span>Kaart</a>
            <a href="logboek.php" class="nav-item <?= $currentpage === 'logboek.php' ? ' current' : '' ?>"><span class="nav-icon">🗋</span>Logboek</a>
            <a href="verifynet.php" class="nav-item <?= $currentpage === 'verifynet.php' ? ' current' : '' ?>"><span class="nav-icon">⚙</span>VerifyNET</a>
        </nav>
        <a href="verifynet.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="fleet">
                <h3>VerifyNET</h3>
                <p>Status: <span>Online</span></p>
                <p>Claims: <span id="unread"></span></p>
            </div>
        </a>
    </aside>

    
    <script src="./js/connection.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const currentUser = localStorage.getItem('verifinet_user');
        
        
        const adminEmail = "admin@firepatch.nl"; 
        
        
        const currentPage = window.location.pathname.split("/").pop();

        
        const allowedPages = ["verifynet.php", "article.php", "login.php", "register.php", "save_claim.php", "profile.php"];

        const sidebar = document.getElementById('firepatch-sidebar');
        const layoutContainer = document.querySelector('.layout');

        
        if (currentUser === adminEmail) {
            
            sidebar.style.display = "flex";
        } else {
            
            if (layoutContainer) {
                layoutContainer.style.gridTemplateColumns = "1fr"; 
            }

            
            if (currentPage && !allowedPages.includes(currentPage)) {
            
                window.location.href = "verifynet.php";
            }
        }
    });
    </script>
</body>

</html>