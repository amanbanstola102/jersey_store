<?php
session_start();
require_once "includes/db.php";
$category = trim($_GET['category'] ?? '');
$allowedCategories = ['Club Jerseys','National Teams','Retro Jerseys','Goalkeeper','Training Kits'];
if ($category !== '' && !in_array($category, $allowedCategories, true)) $category = '';
if ($category !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category=? ORDER BY product_id DESC");
    $stmt->execute([$category]);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll();
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>JerseyHub - Jerseys</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="assets/css/main.css"></head><body>
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
<section class="products all-products"><div class="container"><div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Jerseys</span></div><h2><?= $category ? htmlspecialchars($category) : 'All Jerseys' ?></h2><div class="category-filter-links"><a href="products.php" class="<?= $category===''?'active':'' ?>">All</a><?php foreach ($allowedCategories as $cat): ?><a href="products.php?category=<?= urlencode($cat) ?>" class="<?= $category===$cat?'active':'' ?>"><?= htmlspecialchars($cat) ?></a><?php endforeach; ?></div></div>
<?php if (!$products): ?><div class="border p-5 text-center">No jerseys found in this category.</div><?php else: ?><div class="row g-4"><?php foreach ($products as $product): ?><div class="col-md-6 col-lg-3"><div class="one-card"><a href="product.php?id=<?= $product["product_id"] ?>" class="text-dark product-link"><div class="photo"><?php if ($product['image']): ?><img src="uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"><?php else: ?><div class="image-placeholder">JERSEY</div><?php endif; ?></div><div class="content"><h5><?= htmlspecialchars($product['name']) ?></h5><div class="desc"><span>Rs. <?= number_format($product['price'], 2) ?></span><span><?= htmlspecialchars($product['category']) ?></span></div></div></a></div></div><?php endforeach; ?></div><?php endif; ?></div></section></body></html>
