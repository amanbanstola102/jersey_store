<?php
session_start();
require_once "includes/db.php";
$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die("Jersey not found.");

$stmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON u.user_id=r.user_id WHERE r.product_id=? ORDER BY r.review_id DESC");
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($product["name"]) ?> - JerseyHub</title>
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
<div class="row g-5">
<div class="col-md-6"><div class="photo" style="height:500px">
<?php if ($product["image"]): ?><img src="uploads/products/<?= htmlspecialchars($product["image"]) ?>" alt="<?= htmlspecialchars($product["name"]) ?>"><?php else: ?><div class="image-placeholder">JERSEY</div><?php endif; ?>
</div></div>
<div class="col-md-6">
<div class="sub-title"><span class="shape"></span><span class="title"><?= htmlspecialchars($product["category"]) ?></span></div>
<h1 class="mt-3"><?= htmlspecialchars($product["name"]) ?></h1>
<h3 class="text-danger">Rs. <?= number_format($product["price"],2) ?></h3>
<p class="text-secondary mt-3"><?= nl2br(htmlspecialchars($product["description"])) ?></p>
<p>Size: <?= htmlspecialchars($product["size"]) ?> | Stock: <?= $product["stock"] ?></p>
<?php if ($product["stock"] > 0): ?>
<form action="add_to_cart.php" method="post" class="d-flex gap-2">
<input type="hidden" name="product_id" value="<?= $product["product_id"] ?>">
<input type="number" name="quantity" min="1" max="<?= $product["stock"] ?>" value="1" class="form-control" style="max-width:110px">
<button class="btn btn-danger">Add to Cart</button>
</form>
<?php else: ?><div class="alert alert-secondary">Out of stock.</div><?php endif; ?>
</div></div>

<section class="mt-5">
<div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Customer Reviews</span></div><h2>Reviews</h2></div>
<?php if (isset($_SESSION["user_id"])): ?><a href="review_add.php?product_id=<?= $id ?>" class="btn btn-outline-danger mb-4">Write a Review</a><?php else: ?><p><a href="login.php">Login</a> to write a review.</p><?php endif; ?>
<?php if (!$reviews): ?><div class="border p-4">No reviews yet.</div><?php endif; ?>
<?php foreach ($reviews as $review): ?>
<div class="border p-4 mb-3"><div class="d-flex justify-content-between"><strong><?= htmlspecialchars($review["username"]) ?></strong><span><?= str_repeat("*", (int)$review["rating"]) ?></span></div><p class="mt-2 mb-1"><?= nl2br(htmlspecialchars($review["comment"])) ?></p></div>
<?php endforeach; ?>
</section>
</div></body></html>