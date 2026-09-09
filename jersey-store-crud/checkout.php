<?php
session_start();
require_once "includes/db.php";
require_once "includes/auth.php";
require_customer();

$cart = $_SESSION["cart"] ?? [];
if (!$cart) { header("Location: cart.php"); exit; }

$ids = array_map('intval', array_keys($cart));
$placeholders = implode(",", array_fill(0, count($ids), "?"));
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();
$total = 0;
foreach ($products as $p) $total += (float)$p["price"] * (int)$cart[$p["product_id"]];

$error = "";
$paymentMethod = $_POST['payment_method'] ?? 'COD';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $address = trim($_POST["address"] ?? "");
    $allowedMethods = ['COD', 'eSewa'];
    if ($address === "") {
        $error = "Delivery address is required.";
    } elseif (!in_array($paymentMethod, $allowedMethods, true)) {
        $error = "Please select a valid payment method.";
    } else {
        try {
            $pdo->beginTransaction();

            // Lock products and recalculate the total on the server before creating the order.
            $lockedProducts = [];
            $freshTotal = 0;
            foreach ($ids as $id) {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id=? FOR UPDATE");
                $stmt->execute([$id]);
                $p = $stmt->fetch();
                $qty = (int)($cart[$id] ?? 0);
                if (!$p || $qty < 1 || (int)$p['stock'] < $qty) {
                    throw new RuntimeException("One or more jerseys are out of stock.");
                }
                $lockedProducts[] = [$p, $qty];
                $freshTotal += (float)$p['price'] * $qty;
            }

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_address, payment_method, payment_status, status) VALUES (?, ?, ?, ?, 'Pending', 'Pending')");
            $stmt->execute([$_SESSION["user_id"], $freshTotal, $address, $paymentMethod]);
            $orderId = (int)$pdo->lastInsertId();

            foreach ($lockedProducts as [$p, $qty]) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $p["product_id"], $qty, $p["price"]]);
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?");
                $stmt->execute([$qty, $p["product_id"], $qty]);
                if ($stmt->rowCount() !== 1) throw new RuntimeException("Stock changed while placing the order.");
            }

            $stmt = $pdo->prepare("INSERT INTO payments (order_id, user_id, gateway, amount, status) VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->execute([$orderId, $_SESSION["user_id"], $paymentMethod, $freshTotal]);

            $pdo->commit();
            $_SESSION["cart"] = [];

            if ($paymentMethod === 'eSewa') {
                header("Location: payment/esewa_initiate.php?order_id=" . $orderId);
            } else {
                header("Location: orders.php?success=1");
            }
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $e->getMessage() ?: "Unable to place the order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Checkout - JerseyHub</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="assets/css/main.css"></head>
<body><div class="upper-nav text-center py-2">JerseyHub Checkout</div>
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
<div class="container py-5" style="max-width:950px"><div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Checkout</span></div><h2>Place Your Order</h2></div>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="row g-4"><div class="col-md-7"><form method="post" class="border p-4">
<label class="form-label">Delivery Address</label><textarea name="address" rows="5" class="form-control mb-4" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
<label class="form-label">Payment Method</label>
<div class="payment-options mb-4">
<label class="payment-option"><input type="radio" name="payment_method" value="COD" <?= $paymentMethod==='COD'?'checked':'' ?>> <span><strong>Cash on Delivery</strong><small>Pay when your jersey is delivered.</small></span></label>
<label class="payment-option"><input type="radio" name="payment_method" value="eSewa" <?= $paymentMethod==='eSewa'?'checked':'' ?>> <span><strong>eSewa</strong><small>Pay securely through eSewa.</small></span></label>
</div>
<button class="btn btn-danger">Continue to Payment</button> <a href="cart.php" class="btn btn-outline-secondary">Back to Cart</a>
</form></div>
<div class="col-md-5"><div class="border p-4"><h5>Order Summary</h5><?php foreach ($products as $p): ?><div class="d-flex justify-content-between border-bottom py-2"><span><?= htmlspecialchars($p["name"]) ?> x <?= (int)$cart[$p["product_id"]] ?></span><span>Rs. <?= number_format((float)$p["price"]*$cart[$p["product_id"]],2) ?></span></div><?php endforeach; ?><div class="d-flex justify-content-between mt-3 fw-bold"><span>Total</span><span>Rs. <?= number_format($total,2) ?></span></div></div></div></div></div></body></html>
