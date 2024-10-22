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
    <title>Bejelentkezés</title>
</head>
<body>
    <h1>Bejelentkezés</h1>

    <form action="auth.php" method="POST">
        <label for="username">Felhasználónév:</label>
        <input type="text" name="username" required>

        <label for="password">Jelszó:</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Bejelentkezés</button>
    </form>

    <p>Még nincs fiókod? <a href="register.php">Regisztrálj itt!</a></p>
</body>
</html>
