<?php
session_start();
require_once "includes/db.php";
if (isset($_SESSION["user_id"])) { header("Location: index.php"); exit; }

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $username === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 4) {
        $error = "Please enter valid details. Password must be at least 4 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or email already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION["user_id"] = $pdo->lastInsertId();
            $_SESSION["username"] = $username;
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register - JerseyHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/main.css"></head>
<body>
<div class="upper-nav text-center py-2">JerseyHub</div>
<div class="container py-5" style="max-width:600px">
<div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Account</span></div><h2>Create Account</h2></div>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="border p-4">
<div class="mb-3"><label class="form-label">Full Name</label><input name="name" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
<div class="mb-4"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<button class="btn btn-danger">Register</button>
<a href="login.php" class="btn btn-outline-secondary">Login</a>
</form></div></body></html>