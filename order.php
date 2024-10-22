<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Termék részletek lekérése
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product && $quantity <= $product['stock']) {
        $totalPrice = $product['price'] * $quantity;

        // Rendelés létrehozása
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, shipping_method) VALUES (?, ?, 'store_pickup')");
        $stmt->execute([$userId, $totalPrice]);
        $orderId = $pdo->lastInsertId();

        // Rendelés tételek mentése
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $productId, $quantity, $product['price']]);

        // Termék készlet frissítése
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$quantity, $productId]);

        header('Location: success.php');
        exit();
    } else {
        echo "Nincs elég készlet.";
    }
}
?>
