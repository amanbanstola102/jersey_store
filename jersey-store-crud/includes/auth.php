<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function require_customer() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php?redirect=" . urlencode($_SERVER["REQUEST_URI"]));
        exit;
    }
}
?>