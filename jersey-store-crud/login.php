<?php
session_start();
require_once "includes/db.php";

if (isset($_SESSION["admin_id"])) { header("Location: admin/index.php"); exit; }
if (isset($_SESSION["user_id"])) { header("Location: index.php"); exit; }

$error = "";
$redirect = $_GET["redirect"] ?? "index.php";
if (!is_string($redirect) || $redirect === "" || str_starts_with($redirect, "http://") || str_starts_with($redirect, "https://") || str_starts_with($redirect, "//")) {
    $redirect = "index.php";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $redirect = $_POST["redirect"] ?? "index.php";
    if (!is_string($redirect) || $redirect === "" || str_starts_with($redirect, "http://") || str_starts_with($redirect, "https://") || str_starts_with($redirect, "//")) {
        $redirect = "index.php";
    }

    // Admins use this exact same login form.
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin["password"])) {
        session_regenerate_id(true);
        unset($_SESSION["user_id"], $_SESSION["username"]);
        $_SESSION["admin_id"] = $admin["admin_id"];
        $_SESSION["admin_username"] = $admin["username"];
        header("Location: admin/index.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        unset($_SESSION["admin_id"], $_SESSION["admin_username"]);
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["username"] = $user["username"];
        header("Location: " . $redirect);
        exit;
    }
    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - JerseyHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/main.css"></head>
<body>
<div class="upper-nav text-center py-2">JerseyHub</div>
<div class="container py-5" style="max-width:520px">
<div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Account</span></div><h2>Login</h2></div>
<p class="text-secondary">Sign in to your JerseyHub account.</p>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="border p-4">
<input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
<div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
<div class="mb-4"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<button class="btn btn-dark">Login</button>
<a href="register.php" class="btn btn-outline-secondary">Create Account</a>
</form>
<p class="text-secondary small mt-3">Store administrators use this same login form.</p>
</div></body></html>