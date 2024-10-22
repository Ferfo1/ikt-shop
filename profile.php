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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Profilom</h1>

        <h2>Felhasználói adatok</h2>
        <p><strong>Felhasználónév:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <h2>Új cím mentése</h2>
        <form method="POST" action="profile.php">
            <div class="mb-3">
                <label for="street" class="form-label">Utca</label>
                <input type="text" class="form-control" id="street" name="street" required>
            </div>
            <div class="mb-3">
                <label for="house_number" class="form-label">Házszám</label>
                <input type="text" class="form-control" id="house_number" name="house_number" required>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">Város</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="mb-3">
                <label for="state" class="form-label">Megye</label>
                <input type="text" class="form-control" id="state" name="state" required>
            </div>
            <div class="mb-3">
                <label for="zipcode" class="form-label">Irányítószám</label>
                <input type="text" class="form-control" id="zipcode" name="zipcode" required>
            </div>
            <button type="submit" name="save_address" class="btn btn-primary">Cím mentése</button>
        </form>

        <h2 class="my-4">Rendeléseim</h2>
        <table class="table table-striped">
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

        <a href="index.php" class="btn btn-secondary">Vissza a főoldalra</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
