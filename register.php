<?php
// Indítsuk el a munkamenetet
session_start();

// Ha már bejelentkezett, irányítsuk át a főoldalra
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?> 
 
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
</head>
<body>
    <h1>Regisztráció</h1>

    <form action="auth.php" method="POST">
        <label for="username">Felhasználónév:</label>
        <input type="text" name="username" required>

        <label for="email">Email cím:</label>
        <input type="email" name="email" required>

        <label for="password">Jelszó:</label>
        <input type="password" name="password" required>

        <label for="password_confirm">Jelszó megerősítése:</label>
        <input type="password" name="password_confirm" required>

        <button type="submit" name="register">Regisztráció</button>
    </form>

    <p>Már van fiókod? <a href="login.php">Jelentkezz be!</a></p>
</body>
</html>
