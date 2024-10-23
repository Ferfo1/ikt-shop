<?php
require 'db.php';
session_start();

// Ha már be van jelentkezve, irányítsuk át a főoldalra
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $street = $_POST['street'];
    $houseNumber = $_POST['house_number'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipcode = $_POST['zipcode'];

    // Felhasználó hozzáadása
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password]);
    $userId = $pdo->lastInsertId();

    // Cím hozzáadása
    $stmt = $pdo->prepare("INSERT INTO addresses (user_id, street, house_number, city, state, zipcode) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $street, $houseNumber, $city, $state, $zipcode]);

    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
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

        .register-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 320px;
            text-align: center;
            transition: transform 0.3s;
        }

        .register-container:hover {
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
    <div class="register-container">
        <h1>Regisztráció</h1>
        <form action="register.php" method="POST">
            <label for="username">Felhasználónév</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Jelszó</label>
            <input type="password" id="password" name="password" required>

            <label for="street">Utca</label>
            <input type="text" id="street" name="street" required>

            <label for="house_number">Házszám</label>
            <input type="text" id="house_number" name="house_number" required>

            <label for="city">Város</label>
            <input type="text" id="city" name="city" required>

            <label for="state">Megye</label>
            <input type="text" id="state" name="state" required>

            <label for="zipcode">Irányítószám</label>
            <input type="text" id="zipcode" name="zipcode" required>

            <button type="submit" name="register">Regisztráció</button>
        </form>
        <p>Van már fiókod? <a href="login.php">Bejelentkezés</a></p>
    </div>
</body>
</html>
