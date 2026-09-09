<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
$cart = $_SESSION["cart"];
$products = [];
$total = 0;

if ($cart) {
    $ids = array_keys($cart);
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        $qty = (int)$cart[$p["product_id"]];
        $total += $p["price"] * $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cart - JerseyHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/main.css"></head>
<body>
<div class="upper-nav text-center py-2">Summer Jersey Sale - Up to 30% Off</div>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">JERSEYHUB</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Jerseys</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#categories">Categories</a></li>
            </ul>
            <div class="d-flex gap-2 align-items-center">
                <a class="btn btn-outline-dark" href="cart.php">Cart</a>
                <?php if (isset($_SESSION["admin_id"])): ?>
                    <details class="profile-dropdown">
                        <summary class="profile-trigger">
                            <span class="profile-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.8-3.4 3.1-5.2 7-5.2s6.2 1.8 7 5.2"></path></svg></span>
                            <span><?= htmlspecialchars($_SESSION["admin_username"]) ?></span>
                        </summary>
                        <div class="profile-menu">
                            <a href="admin/index.php">Admin Dashboard</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </details>
                <?php elseif (isset($_SESSION["user_id"])): ?>
                    <details class="profile-dropdown">
                        <summary class="profile-trigger">
                            <span class="profile-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.8-3.4 3.1-5.2 7-5.2s6.2 1.8 7 5.2"></path></svg></span>
                            <span><?= htmlspecialchars($_SESSION["username"]) ?></span>
                        </summary>
                        <div class="profile-menu">
                            <a href="orders.php">My Orders</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </details>
                <?php else: ?>
                    <a class="btn btn-outline-secondary" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container py-5">
<div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Shopping Cart</span></div><h2>Your Cart</h2></div>
<?php if (!$products): ?>
<div class="border p-5 text-center"><h5>Your cart is empty.</h5><a href="products.php" class="btn btn-danger mt-3">Browse Jerseys</a></div>
<?php else: ?>
<form action="cart_update.php" method="post">
<div class="table-responsive"><table class="table table-bordered align-middle">
<thead class="table-dark"><tr><th>Jersey</th><th>Price</th><th width="130">Quantity</th><th>Subtotal</th><th>Remove</th></tr></thead>
<tbody>
<?php foreach ($products as $p): $qty=(int)$cart[$p["product_id"]]; ?>
<tr>
<td><?= htmlspecialchars($p["name"]) ?></td><td>Rs. <?= number_format($p["price"],2) ?></td>
<td><input type="number" min="1" max="<?= max(1,$p["stock"]) ?>" name="qty[<?= $p["product_id"] ?>]" value="<?= $qty ?>" class="form-control"></td>
<td>Rs. <?= number_format($p["price"]*$qty,2) ?></td>
<td><a href="cart_update.php?remove=<?= $p["product_id"] ?>" class="btn btn-sm btn-outline-danger">Remove</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
<button class="btn btn-outline-dark">Update Cart</button>
<div class="fs-5 fw-bold">Total: Rs. <?= number_format($total,2) ?></div>
<a href="checkout.php" class="btn btn-danger">Checkout</a>
</div>
</form>
<?php endif; ?>
</div></body></html>