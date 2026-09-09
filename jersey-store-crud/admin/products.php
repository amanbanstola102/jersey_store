<?php
session_start();
require_once "../includes/db.php";
if (!isset($_SESSION["admin_id"])) { header("Location: ../login.php"); exit; }

$products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll();
$msg = $_GET["msg"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Jerseys - JerseyHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<div class="upper-nav text-center py-2">JerseyHub Admin Panel</div>
<nav class="navbar navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">JERSEYHUB</a>
        <div><a href="index.php" class="btn btn-outline-dark me-2">Dashboard</a><a href="logout.php" class="btn btn-dark">Logout</a></div>
    </div>
</nav>
<div class="container py-5">
    <div class="categ-header">
        <div class="sub-title"><span class="shape"></span><span class="title">Jerseys</span></div>
        <h2>All Jerseys</h2>
    </div>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="mb-3"><a href="product_add.php" class="btn btn-danger">Insert Jersey</a></div>
    <div class="table-responsive">
    <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
            <tr><th>ID</th><th>Name</th><th>Image</th><th>Category</th><th>Price</th><th>Size</th><th>Stock</th><th>Edit</th><th>Delete</th></tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product["product_id"] ?></td>
                <td><?= htmlspecialchars($product["name"]) ?></td>
                <td>
                    <?php if ($product["image"]): ?>
                        <img src="../uploads/products/<?= htmlspecialchars($product["image"]) ?>" width="70" height="70" style="object-fit:contain" class="img-thumbnail">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($product["category"]) ?></td>
                <td>Rs. <?= number_format($product["price"], 2) ?></td>
                <td><?= htmlspecialchars($product["size"]) ?></td>
                <td><?= $product["stock"] ?></td>
                <td><a href="product_edit.php?id=<?= $product["product_id"] ?>" class="btn btn-sm btn-outline-dark">Edit</a></td>
                <td><a href="product_delete.php?id=<?= $product["product_id"] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this jersey?')">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>