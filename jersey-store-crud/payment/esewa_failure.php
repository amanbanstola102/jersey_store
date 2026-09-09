<?php
session_start();
require_once __DIR__ . '/helpers.php';
$data = $_GET['data'] ?? '';
$decoded = base64_decode($data, true);
$response = $decoded !== false ? json_decode($decoded, true) : null;
$orderId = 0;
if (is_array($response)) {
    $uuid = (string)($response['transaction_uuid'] ?? '');
    if (preg_match('/^ORDER-(\d+)-/', $uuid, $m)) $orderId = (int)$m[1];
    if ($orderId) mark_payment($orderId, 'Failed', $response['transaction_code'] ?? null, null, $decoded);
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>eSewa Payment Failed - JerseyHub</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/main.css"></head><body><div class="upper-nav text-center py-2">JerseyHub Payment</div><div class="container py-5" style="max-width:700px"><div class="border p-5 text-center"><h2 class="text-danger">Payment Failed or Cancelled</h2><p>Your order was created, but eSewa did not confirm the payment.</p><a href="../orders.php" class="btn btn-dark">View My Orders</a></div></div></body></html>
