<?php
session_start();
require_once "../includes/db.php";
if (!isset($_SESSION["admin_id"])) { header("Location: ../login.php"); exit; }

$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("SELECT image FROM products WHERE product_id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product) {
    if ($product["image"] && file_exists("../uploads/products/" . $product["image"])) {
        unlink("../uploads/products/" . $product["image"]);
    }
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
}
header("Location: products.php?msg=Jersey deleted successfully");
exit;
?>