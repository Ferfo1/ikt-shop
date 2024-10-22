<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $orderId = $_POST['order_id'];

    $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
    $stmt->execute([$orderId]);

    header('Location: admin.php');
    exit();
}
?> 
