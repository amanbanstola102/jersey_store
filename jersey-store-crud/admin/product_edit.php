<?php
session_start();
require_once "../includes/db.php";
if (!isset($_SESSION["admin_id"])) { header("Location: ../login.php"); exit; }

$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { die("Jersey not found."); }

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $size = trim($_POST["size"] ?? "");
    $stock = (int)($_POST["stock"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    $imageName = $product["image"];

    if ($name === "" || $category === "" || $price <= 0) {
        $error = "Please fill in the required fields.";
    } else {
        if (!empty($_FILES["image"]["name"])) {
            $allowed = ["jpg", "jpeg", "png", "webp"];
            $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                $error = "Only JPG, JPEG, PNG and WEBP images are allowed.";
            } elseif ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                $error = "Image upload failed.";
            } else {
                $imageName = time() . "_" . preg_replace("/[^A-Za-z0-9._-]/", "_", basename($_FILES["image"]["name"]));
                move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/products/" . $imageName);
                if ($product["image"] && file_exists("../uploads/products/" . $product["image"])) {
                    unlink("../uploads/products/" . $product["image"]);
                }
            }
        }
        if ($error === "") {
            $stmt = $pdo->prepare("UPDATE products SET name=?, category=?, price=?, size=?, stock=?, description=?, image=? WHERE product_id=?");
            $stmt->execute([$name, $category, $price, $size, $stock, $description, $imageName, $id]);
            header("Location: products.php?msg=Jersey updated successfully");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Jersey - JerseyHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<div class="upper-nav text-center py-2">JerseyHub Admin Panel</div>
<div class="container py-5" style="max-width:800px;">
    <div class="categ-header">
        <div class="sub-title"><span class="shape"></span><span class="title">Jerseys</span></div>
        <h2>Edit Jersey</h2>
    </div>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="border p-4">
        <div class="mb-3"><label class="form-label">Jersey Name</label><input name="name" class="form-control" value="<?= htmlspecialchars($product["name"]) ?>" required></div>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Category</label><input name="category" class="form-control" value="<?= htmlspecialchars($product["category"]) ?>" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Price</label><input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product["price"]) ?>" required></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Size</label><input name="size" class="form-control" value="<?= htmlspecialchars($product["size"]) ?>"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Stock</label><input type="number" name="stock" min="0" class="form-control" value="<?= htmlspecialchars($product["stock"]) ?>"></div>
        </div>
        <div class="mb-3"><label class="form-label">Replace Image</label><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp"></div>
        <div class="mb-4"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product["description"]) ?></textarea></div>
        <button class="btn btn-danger">Update Jersey</button>
        <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
</body>
</html>