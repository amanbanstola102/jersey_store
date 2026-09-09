<?php
session_start();
require_once "includes/db.php";
$products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC LIMIT 8")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JerseyHub - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<div class="upper-nav text-center py-2">
    Summer Jersey Sale - Up to 30% Off
</div>

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

<section class="landing">
    <div class="container">
        <div class="row g-0">
            <div class="col-lg-3 tabs-categ">
                <ul class="pt-4">
                    <li><a href="products.php">Football Jerseys <span>></span></a></li>
                    <li><a href="products.php?category=Club+Jerseys">Club Jerseys <span>></span></a></li>
                    <li><a href="products.php?category=National+Teams">National Teams <span>></span></a></li>
                    <li><a href="products.php?category=Retro+Jerseys">Retro Jerseys <span>></span></a></li>
                    <li><a href="products.php?category=Goalkeeper">Goalkeeper <span>></span></a></li>
                    <li><a href="products.php?category=Training+Kits">Training Kits <span>></span></a></li>
                </ul>
            </div>
            <div class="col-lg-9">
                <div class="hero-cover">
                    <span class="hero-small">NEW SEASON COLLECTION</span>
                    <span class="hero-title">Official Jerseys</span>
                    <span class="hero-desc">Find your<br>team's colors.</span>
                    <a href="products.php">Shop now &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="category" id="categories">
    <div class="container">
        <div class="categ-header">
            <div class="sub-title"><span class="shape"></span><span class="title">Categories</span></div>
            <h2>Browse By Category</h2>
        </div>
        <div class="cards">
            <a class="category-card" href="products.php?category=Club+Jerseys"><div class="category-symbol">FC</div><span>Club Jerseys</span></a>
            <a class="category-card" href="products.php?category=National+Teams"><div class="category-symbol">NT</div><span>National Teams</span></a>
            <a class="category-card" href="products.php?category=Retro+Jerseys"><div class="category-symbol">R</div><span>Retro Jerseys</span></a>
            <a class="category-card" href="products.php?category=Goalkeeper"><div class="category-symbol">GK</div><span>Goalkeeper</span></a>
            <a class="category-card" href="products.php?category=Training+Kits"><div class="category-symbol">TR</div><span>Training Kits</span></a>
        </div>
    </div>
</section>

<section class="adver">
    <div class="container">
        <div class="ad-cover">
            <span class="ad-title">JERSEY COLLECTION</span>
            <span class="ad-desc">Represent your<br>team in style.</span>
            <a href="products.php" class="ad-button">View Jerseys</a>
        </div>
    </div>
</section>

<section class="products" id="products">
    <div class="container">
        <div class="categ-header">
            <div class="sub-title"><span class="shape"></span><span class="title">Our Jerseys</span></div>
            <h2>Explore Our Jerseys</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
            <div class="col-md-6 col-lg-3">
                <div class="one-card">
                    <div class="photo">
                        <?php if ($product['image']): ?>
                            <img src="uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div class="image-placeholder">JERSEY</div>
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <h5><?= htmlspecialchars($product['name']) ?></h5>
                        <div class="desc"><span>Rs. <?= number_format($product['price'], 2) ?></span><span><?= htmlspecialchars($product['category']) ?></span></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="view">
            <a href="products.php">View All Jerseys</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container text-center py-4">JerseyHub &copy; <?= date("Y") ?>. All rights reserved.</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>