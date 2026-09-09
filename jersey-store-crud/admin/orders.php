<?php
session_start();
require_once "../includes/db.php";
if (!isset($_SESSION["admin_id"])) { header("Location: ../login.php"); exit; }
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["order_id"] ?? 0);
    $status = $_POST["status"] ?? "Pending";
    $allowed = ["Pending","Processing","Shipped","Delivered","Cancelled"];
    if (in_array($status, $allowed, true)) { $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE order_id=?"); $stmt->execute([$status,$id]); }
    header("Location: orders.php"); exit;
}
$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON u.user_id=o.user_id ORDER BY o.order_id DESC")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Orders - JerseyHub Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/main.css"></head><body><div class="upper-nav text-center py-2">JerseyHub Admin Panel</div><div class="container py-5"><div class="categ-header"><div class="sub-title"><span class="shape"></span><span class="title">Orders</span></div><h2>Customer Orders</h2></div><a href="index.php" class="btn btn-outline-dark mb-3">Dashboard</a><div class="table-responsive"><table class="table table-bordered align-middle"><thead class="table-dark"><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Address</th><th>Status</th><th>Update</th></tr></thead><tbody><?php foreach ($orders as $o): ?><tr><td>#<?= $o["order_id"] ?><br><small><?= htmlspecialchars($o["created_at"]) ?></small></td><td><?= htmlspecialchars($o["username"]) ?></td><td>Rs. <?= number_format($o["total_amount"],2) ?></td><td><?= htmlspecialchars($o["payment_method"]) ?><br><span class="badge <?= $o['payment_status']==='Paid'?'text-bg-success':($o['payment_status']==='Pending'?'text-bg-warning':'text-bg-danger') ?>"><?= htmlspecialchars($o["payment_status"]) ?></span></td><td><?= htmlspecialchars($o["delivery_address"]) ?></td><td><?= htmlspecialchars($o["status"]) ?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="order_id" value="<?= $o["order_id"] ?>"><select name="status" class="form-select form-select-sm"><?php foreach(["Pending","Processing","Shipped","Delivered","Cancelled"] as $s): ?><option <?= $o["status"]===$s?"selected":"" ?>><?= $s ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-dark">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></body></html>
