<?php
session_start();
require_once "includes/db.php";

$id = (int)($_POST["product_id"] ?? 0);
$qty = max(1, (int)($_POST["quantity"] ?? 1));

$stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product) {
    if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
    $current = (int)($_SESSION["cart"][$id] ?? 0);
    $_SESSION["cart"][$id] = min($current + $qty, (int)$product["stock"]);
}
header("Location: cart.php");
exit;
?>