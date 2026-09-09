<?php
session_start();
require_once "includes/db.php";
if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];

if (isset($_GET["remove"])) {
    $id = (int)$_GET["remove"];
    unset($_SESSION["cart"][$id]);
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach (($_POST["qty"] ?? []) as $id => $qty) {
        $id = (int)$id; $qty = (int)$qty;
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
        $stmt->execute([$id]);
        $stock = $stmt->fetchColumn();
        if ($stock === false || $qty <= 0) unset($_SESSION["cart"][$id]);
        else $_SESSION["cart"][$id] = min($qty, (int)$stock);
    }
}
header("Location: cart.php");
exit;
?>