<?php
require 'db.php';
session_start();
 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Átirányítás, ha nincs bejelentkezve
    exit;
}

$userId = $_SESSION['user_id'];

// Rendelésekhez szükséges címek lekérdezése
$stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt->execute([$userId]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kosár ellenőrzés
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: index.php");
    exit;
}

// Rendelés elküldése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $addressId = $_POST['address_id'] ?? null;
    $paymentMethod = $_POST['payment_method'];
    $deliveryMethod = $_POST['delivery_method'];

    if ($deliveryMethod === 'pickup') {
        // Ha a szállítási mód "pickup", a cím legyen "pickup"
        $fullAddress = "pickup";
    } else {
        // Cím lekérdezése az ID alapján, ha házhoz szállítást választottak
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
        $stmt->execute([$addressId, $userId]);
        $address = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($address) {
            // A cím tárolása
            $fullAddress = htmlspecialchars($address['street'] . ' ' . $address['house_number'] . ', ' . $address['city'] . ', ' . $address['state'] . ' ' . $address['zipcode']);
        } else {
            // Hibakezelés, ha a cím nem található
            echo "Hiba: A kiválasztott cím nem található.";
            exit;
        }
    }

    $total = array_sum(array_column($_SESSION['cart'], 'price'));

    // Rendelés létrehozása
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, address, payment_method, delivery_method) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $total, $fullAddress, $paymentMethod, $deliveryMethod]);

    // Rendelési részletek hozzáadása
    $orderId = $pdo->lastInsertId();
    foreach ($_SESSION['cart'] as $cartItem) {
        $stmt = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $cartItem['id'], 1, $cartItem['price']]);
    }

    // Kosár kiürítése
    $_SESSION['cart'] = [];
    header("Location: index.php?order_success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pénztár - Mini Webshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Mini Webshop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Összes Termék</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profilom</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Kosár</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="my-4">Pénztár</h1>

        <form method="POST" action="checkout.php">
            <div class="mb-3">
                <label for="delivery_method" class="form-label">Szállítási mód</label>
                <select class="form-select" id="delivery_method" name="delivery_method" required>
                    <option value="home">Házhoz szállítás</option>
                    <option value="pickup">Személyes bolti átvétel</option>
                </select>
            </div>

            <!-- Címválasztó szekció, csak akkor jelenik meg, ha házhoz szállítás van kiválasztva -->
            <div class="mb-3" id="address_section" style="display:none;">
                <label for="address_id" class="form-label">Szállítási cím</label>
                <select class="form-select" id="address_id" name="address_id">
                    <?php foreach ($addresses as $address): ?>
                        <option value="<?= $address['id'] ?>"><?= htmlspecialchars($address['street'] . ' ' . $address['house_number'] . ', ' . $address['city'] . ', ' . $address['state'] . ' ' . $address['zipcode']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Fizetési mód</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="cash">Készpénz</option>
                    <option value="credit_card">Bankkártya</option>
                </select>
            </div>

            <h2>Kosár tartalma</h2>
            <ul class="list-group mb-3">
                <?php foreach ($_SESSION['cart'] as $cartItem): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($cartItem['name']) ?> - <?= htmlspecialchars($cartItem['price']) ?> Ft
                    </li>
                <?php endforeach; ?>
            </ul>
            <h3>Összesen: <?= array_sum(array_column($_SESSION['cart'], 'price')) ?> Ft</h3>
            <button type="submit" name="place_order" class="btn btn-primary">Rendelés leadása</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Szállítási mód alapján címválasztó szekció elrejtése/megjelenítése
        document.getElementById('delivery_method').addEventListener('change', function() {
            var deliveryMethod = this.value;
            var addressSection = document.getElementById('address_section');
            if (deliveryMethod === 'home') {
                addressSection.style.display = 'block'; // Cím megjelenítése
            } else {
                addressSection.style.display = 'none'; // Cím elrejtése
            }
        });

        // Az oldal betöltésekor ellenőrizzük az alapértelmezett választást
        window.addEventListener('DOMContentLoaded', function() {
            var deliveryMethod = document.getElementById('delivery_method').value;
            var addressSection = document.getElementById('address_section');
            if (deliveryMethod === 'home') {
                addressSection.style.display = 'block';
            } else {
                addressSection.style.display = 'none';
            }
        });
    </script>
</body>
</html>
