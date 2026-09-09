<?php
session_start();
require_once "../includes/db.php";
if (!isset($_SESSION["admin_id"])) { header("Location: ../login.php"); exit; }

if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE review_id=?");
    $stmt->execute([$id]);
    header("Location: reviews.php");
    exit;
}
$reviews = $pdo->query("SELECT r.*, u.username, p.name AS product_name FROM reviews r JOIN users u ON u.user_id=r.user_id JOIN products p ON p.product_id=r.product_id ORDER BY r.review_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reviews - JerseyHub Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/main.css"></head>
<body>
<div class="upper-nav text-center py-2">JerseyHub Admin Panel</div>
<div class="container py-5">
<div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Reviews</span></div><h2>Customer Reviews</h2></div>
<a href="index.php" class="btn btn-outline-dark mb-3">Dashboard</a>
<div class="table-responsive"><table class="table table-bordered align-middle">
<thead class="table-dark"><tr><th>Customer</th><th>Jersey</th><th>Rating</th><th>Review</th><th>Delete</th></tr></thead>
<tbody><?php foreach ($reviews as $r): ?><tr>
<td><?= htmlspecialchars($r["username"]) ?></td><td><?= htmlspecialchars($r["product_name"]) ?></td><td><?= $r["rating"] ?>/5</td><td><?= nl2br(htmlspecialchars($r["comment"])) ?></td>
<td><a class="btn btn-sm btn-outline-danger" href="reviews.php?delete=<?= $r["review_id"] ?>" onclick="return confirm('Delete this review?')">Delete</a></td>
</tr><?php endforeach; ?></tbody></table></div>
</div></body></html>