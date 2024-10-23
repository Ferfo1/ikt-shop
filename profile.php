<?php
require 'db.php';
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Ha nem, irányítsuk át a bejelentkezési oldalra
    exit;
}

$userId = $_SESSION['user_id'];

// Rendelések lekérdezése (az `orders` tábla `address` mezőjének felhasználásával)
$stmt = $pdo->prepare("SELECT o.id, o.total, o.address, o.payment_method, o.delivery_method, o.created_at 
                       FROM orders o
                       WHERE o.user_id = ?");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_address'])) {
    $street = $_POST['street'];
    $houseNumber = $_POST['house_number'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipcode = $_POST['zipcode'];

    // Cím mentése az adatbázisba
    $stmt = $pdo->prepare("INSERT INTO addresses (user_id, street, house_number, city, state, zipcode) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $street, $houseNumber, $city, $state, $zipcode]);

    header("Location: profile.php");
    exit;
}

// Felhasználói adatok lekérdezése
$stmtUser = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Címadatok lekérdezése
$stmtAddress = $pdo->prepare("SELECT street, house_number, city, state, zipcode FROM addresses WHERE user_id = ?");
$stmtAddress->execute([$userId]);
$address = $stmtAddress->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Mini Webshop</title>
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

        .profile-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            text-align: center;
            transition: transform 0.3s;
        }

        .profile-container:hover {
            transform: scale(1.02);
        }

        h1, h2 {
            color: #007bff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
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

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
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
    <div class="profile-container">
        <h1>Profilom</h1>

        <h2>Felhasználói adatok</h2>
        <p><strong>Felhasználónév:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <h2>Új cím mentése</h2>
        <form method="POST" action="profile.php">
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

            <button type="submit" name="save_address">Cím mentése</button>
        </form>

        <h2>Rendeléseim</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Összeg (Ft)</th>
                    <th>Cím</th>
                    <th>Fizetési mód</th>
                    <th>Szállítási mód</th>
                    <th>Dátum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['total']) ?></td>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                    <td><?= htmlspecialchars($order['delivery_method']) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="index.php">Vissza a főoldalra</a>
    </div>
</body>
</html>
