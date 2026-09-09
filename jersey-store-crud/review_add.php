<?php
session_start();
require_once "includes/db.php";
require_once "includes/auth.php";
require_customer();

$productId = (int)($_GET["product_id"] ?? $_POST["product_id"] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) die("Jersey not found.");

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = (int)($_POST["rating"] ?? 0);
    $comment = trim($_POST["comment"] ?? "");
    if ($rating < 1 || $rating > 5 || $comment === "") {
        $error = "Please select a rating and write a review.";
    } else {
        $stmt = $pdo->prepare("SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION["user_id"], $productId]);
        if ($stmt->fetch()) {
            $error = "You have already reviewed this jersey.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION["user_id"], $productId, $rating, $comment]);
            header("Location: product.php?id=" . $productId);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Review - JerseyHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/main.css"></head>
<body>
<div class="upper-nav text-center py-2">JerseyHub</div>
<div class="container py-5" style="max-width:650px">
<div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Reviews</span></div><h2>Review <?= htmlspecialchars($product["name"]) ?></h2></div>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="border p-4">
<input type="hidden" name="product_id" value="<?= $productId ?>">
<div class="mb-3"><label class="form-label">Rating</label><select name="rating" class="form-select" required><option value="">Choose rating</option><option value="5">5 - Excellent</option><option value="4">4 - Very Good</option><option value="3">3 - Good</option><option value="2">2 - Fair</option><option value="1">1 - Poor</option></select></div>
<div class="mb-4"><label class="form-label">Review</label><textarea name="comment" class="form-control" rows="5" required></textarea></div>
<button class="btn btn-danger">Submit Review</button>
<a href="product.php?id=<?= $productId ?>" class="btn btn-outline-secondary">Cancel</a>
</form></div></body></html>