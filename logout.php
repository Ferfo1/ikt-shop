<?php
session_start();

// Kijelentkezés: töröljük a sessiont
session_unset(); // Törli a session változókat
session_destroy(); // Megsemmisíti a sessiont

// Átirányítás a főoldalra
header('Location: index.php');
exit();
?>
