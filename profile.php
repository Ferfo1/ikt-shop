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
