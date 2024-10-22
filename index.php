<?php
require 'db.php';

// Véletlenszerű termékek lekérdezése
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY RAND() LIMIT 3");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kosár inicializálása
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
 
// Kosár hozzáadása
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $_SESSION['cart'][] = $product;
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal - Mini Webshop</title>
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
        <h1 class="my-4">Üdvözöljük a Mini Webshopban!</h1>
        <!-- A termékek és kosár megjelenítése itt -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
