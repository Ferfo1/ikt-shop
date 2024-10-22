<?php
session_start();
 
// Kosár kiürítése, ha a felhasználó úgy dönt
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

// Termék eltávolítása a kosárból
if (isset($_POST['remove_item'])) {
    $productId = $_POST['product_id'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $productId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Indexek újrasorrendezése
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosár - Mini Webshop</title>
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
        <h1 class="my-4">Kosár</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <p>A kosara üres. <a href="index.php" class="btn btn-primary">Vásárlás folytatása</a></p>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($_SESSION['cart'] as $cartItem): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($cartItem['name']) ?> - <?= htmlspecialchars($cartItem['price']) ?> Ft
                        <form method="POST" action="cart.php" class="mb-0">
                            <input type="hidden" name="product_id" value="<?= $cartItem['id'] ?>">
                            <button type="submit" name="remove_item" class="btn btn-danger btn-sm">Eltávolítás</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h3>Összesen: <?= array_sum(array_column($_SESSION['cart'], 'price')) ?> Ft</h3>
            <form method="POST" action="cart.php">
                <button type="submit" name="clear_cart" class="btn btn-warning">Kosár kiürítése</button>
            </form>
            <a href="checkout.php" class="btn btn-success mt-2">Pénztár</a>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
