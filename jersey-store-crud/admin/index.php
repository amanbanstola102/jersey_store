<?php
session_start();
require_once "../includes/db.php";
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}
$count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - JerseyHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<div class="upper-nav text-center py-2">Admin Dashboard</div>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php">JERSEYHUB</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span>Welcome <?= htmlspecialchars($_SESSION["admin_username"]) ?></span>
            <a href="../logout.php" class="btn btn-dark">Logout</a>
        </div>
    </div>
</nav>

<div class="control py-5">
    <div class="container">
        <div class="categ-header">
            <div class="sub-title"><span class="shape"></span><span class="title">Admin Dashboard</span></div>
            <h2>Manage Details Of Jersey Store</h2>
        </div>
        <div class="row align-items-center">
            <div class="col-md-3 mb-4">
                <div class="border p-4 text-center">
                    <h5>Total Jerseys</h5>
                    <div class="display-5"><?= $count ?></div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-flex flex-wrap gap-2">
                    <a href="product_add.php" class="btn btn-outline-danger">Insert Jerseys</a>
                    <a href="products.php" class="btn btn-outline-danger">View Jerseys</a>
                    <a href="orders.php" class="btn btn-outline-dark">Orders</a>
                    <a href="reviews.php" class="btn btn-outline-dark">Reviews</a>
                    <a href="../products.php" class="btn btn-outline-dark">View Store</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>