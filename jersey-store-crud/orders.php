<?php
session_start();
require_once "includes/db.php";
require_once "includes/auth.php";
require_customer();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC");
$stmt->execute([$_SESSION["user_id"]]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>My Orders - JerseyHub</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="assets/css/main.css"></head><body>
<div class="upper-nav text-center py-2">JerseyHub</div><nav class="navbar navbar-expand-lg navbar-light bg-light">
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
<div class="container py-5"><div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Orders</span></div><h2>My Orders</h2></div>
<?php if (isset($_GET["success"])): ?><div class="alert alert-success">Your order has been placed successfully.</div><?php endif; ?>
<?php if (!$orders): ?><div class="border p-5 text-center">You have not placed any orders yet.</div><?php endif; ?>
<?php foreach ($orders as $order): ?>
<div class="border p-4 mb-3"><div class="row g-3"><div class="col-md-2"><strong>Order #<?= $order["order_id"] ?></strong></div><div class="col-md-2">Rs. <?= number_format($order["total_amount"],2) ?></div><div class="col-md-2">Order: <span class="badge text-bg-secondary"><?= htmlspecialchars($order["status"]) ?></span></div><div class="col-md-2">Payment: <span class="badge <?= $order['payment_status']==='Paid'?'text-bg-success':($order['payment_status']==='Pending'?'text-bg-warning':'text-bg-danger') ?>"><?= htmlspecialchars($order["payment_status"]) ?></span></div><div class="col-md-2"><?= htmlspecialchars($order["payment_method"]) ?></div><div class="col-md-2"><?= htmlspecialchars($order["created_at"]) ?></div></div><div class="mt-3">Delivery: <?= htmlspecialchars($order["delivery_address"]) ?></div></div>
<?php endforeach; ?></div></body></html>
