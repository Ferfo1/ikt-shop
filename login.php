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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #343a40;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 320px;
            text-align: center;
            transition: transform 0.3s;
        }

        .login-container:hover {
            transform: scale(1.02);
        }

        h1 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #007bff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
            font-weight: bold;
        }

        input {
            width: 290px;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
            
        }

        input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            margin-top: 20px;
            font-size: 14px;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Bejelentkezés</h1>
        <form action="auth.php" method="POST">
            <label for="username">Felhasználónév:</label>
            <input type="text" name="username" required>

            <label for="password">Jelszó:</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Bejelentkezés</button>
        </form>
        <p>Még nincs fiókod? <a href="register.php">Regisztrálj itt!</a></p>
    </div>
</body>
</html>
