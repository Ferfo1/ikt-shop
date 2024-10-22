<?php
// config.php importálása
require 'db.php';
 

$stmt = $pdo->prepare("
    SELECT 
        orders.id AS order_id, 
        users.username AS user_name, 
        orders.total, 
        orders.address, 
        orders.payment_method, 
        orders.delivery_method, 
        GROUP_CONCAT(products.name) AS product_names
    FROM orders
    JOIN users ON orders.user_id = users.id
    JOIN order_details ON orders.id = order_details.order_id
    JOIN products ON order_details.product_id = products.id
    GROUP BY orders.id
    ORDER BY orders.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Termékek listázása
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Termék hozzáadása képpel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Kép feltöltése
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imagePath = 'uploads/' . $imageName;

        // Kép mentése
        move_uploaded_file($imageTmpPath, $imagePath);
    } else {
        $imagePath = null;
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $imagePath]);
    header("Location: admin.php");
    exit;
}

// Termék törlése
if (isset($_GET['delete_product'])) {
    $productId = $_GET['delete_product'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    header("Location: admin.php");
    exit;
}

// Termék módosítása
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $productId = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $productId]);
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Felület</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Rendelések és Termékek Kezelése</h1>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Rendelés ID</th>
                    <th>Felhasználó neve</th>
                    <th>Összeg (Ft)</th>
                    <th>Cím</th>
                    <th>Fizetési mód</th>
                    <th>Szállítási mód</th>
                    <th>Rendelt termékek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($order['user_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($order['total'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($order['address'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($order['delivery_method'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($order['product_names'] ?? 'N/A') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Termékek listázása -->
        <h2>Termékek Listája</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Termék neve</th>
                    <th>Leírás</th>
                    <th>Ár (Ft)</th>
                    <th>Kép</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['id']) ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= htmlspecialchars($product['price']) ?></td>
                    <td>
                        <?php if ($product['image']): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image" width="50">
                        <?php else: ?>
                            Nincs kép
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="#editProductModal<?= $product['id'] ?>" class="btn btn-warning" data-bs-toggle="modal">Módosítás</a>
                        <a href="?delete_product=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Biztosan törölni szeretnéd ezt a terméket?');">Törlés</a>
                    </td>
                </tr>

                <!-- Termék módosítása Modal -->
                <div class="modal fade" id="editProductModal<?= $product['id'] ?>" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editProductModalLabel">Termék Módosítása</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="admin.php" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Termék Neve</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Leírás</label>
                                        <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Ár</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Kép feltöltése (ha változtatni szeretnéd)</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($product['image_path']) ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                                    <button type="submit" name="edit_product" class="btn btn-primary">Módosítás</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Új Termék Hozzáadása -->
        <h2>Új Termék Hozzáadása</h2>
        <form method="POST" action="admin.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Termék Neve</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Leírás</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Ár</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Kép Feltöltése</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Termék Hozzáadása</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
