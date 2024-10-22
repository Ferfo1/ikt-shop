<?php
try {
    $pdo = new PDO('mysql:host=192.168.8.165:6033;dbname=minishop', 'szirony', 'szirony');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis kapcsolat nem sikerült: " . $e->getMessage()); 
}
?>
