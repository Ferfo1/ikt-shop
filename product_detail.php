<?php
require 'db.php';

// Termék adatainak lekérése az URL-ből kapott ID alapján
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "A termék nem található.";
        exit;
    }
} else {
    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Részletes termék</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }

        .product-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            cursor: zoom-in;
            transition: transform 0.2s ease;
        }

        .product-image:hover {
            transform: scale(1.1);
        }

        .product-details {
            padding: 20px;
        }

        .product-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-description {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .product-price {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="product-name"><?= htmlspecialchars($product['name']) ?></h1>
                    <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
                    <p class="product-price">Ár: <?= htmlspecialchars($product['price']) ?> Ft</p>
                    <form method="POST" action="products.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">Kosárba</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
