<?php
session_start();
date_default_timezone_set("Europe/Amsterdam");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/verifinet.css">
</head>

<body>
    <section class="card">
        <form action="upload.php" method="post" enctype="multipart/form-data" style="padding: 15px; display: grid; gap: 12px;">
            <label for="time">Tijd:</label>
            <input type="text" id="time" name="time" value="<?= date("d.M.Y - H:i"); ?>" readonly>

            <label for="name">Activiteit:</label>
            <input class="input" type="text" id="name" name="name" required>

            <label for="sector">Kies een sector:</label>
            <select class="input" name="sector" id="sector" required>
                <?php for ($i = 1; $i <= 32; $i++): ?>
                    <option value="<?= $i ?>">Sector <?= $i ?></option>
                <?php endfor; ?>
            </select>

            <label for="fileToUpload">Selecteer foto voor upload (optioneel):</label>
            <input class="input" type="file" name="fileToUpload" id="fileToUpload" accept="image/jpeg,image/png,image/gif,image/webp">
            <input type="hidden" name="author_email" id="author_email">
            <button class="btn primary" type="submit" name="submit">Verstuur claim</button>
        </form>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const email = localStorage.getItem("verifinet_user");
            const authorEmail = document.getElementById("author_email");
            if (email) {
                authorEmail.value = email;
            }
        });
    </script>
</body>

</html>