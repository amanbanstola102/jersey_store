<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_customer();
require_once __DIR__ . '/helpers.php';

$orderId = (int)($_GET['order_id'] ?? 0);
$order = get_user_order($orderId, (int)$_SESSION['user_id']);
if (!$order || $order['payment_method'] !== 'eSewa') {
    header('Location: ../orders.php');
    exit;
}

$amount = number_format((float)$order['total_amount'], 2, '.', '');
$transactionUuid = 'ORDER-' . $orderId . '-' . date('YmdHis');
$signedFieldNames = 'total_amount,transaction_uuid,product_code';
$signature = eSewa_signature([
    'total_amount' => $amount,
    'transaction_uuid' => $transactionUuid,
    'product_code' => ESEWA_PRODUCT_CODE,
], $signedFieldNames, ESEWA_SECRET_KEY);

$stmt = $pdo->prepare('UPDATE payments SET gateway_reference=?, raw_response=? WHERE order_id=?');
$stmt->execute([$transactionUuid, json_encode(['transaction_uuid'=>$transactionUuid]), $orderId]);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>eSewa Payment - JerseyHub</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/main.css"></head>
<body><div class="upper-nav text-center py-2">JerseyHub Payment</div>
<div class="container py-5" style="max-width:700px"><div class="border p-4 text-center"><h2>Continue to eSewa</h2><p class="text-secondary">Order #<?= $orderId ?> &mdash; Rs. <?= number_format((float)$order['total_amount'],2) ?></p>
<form action="<?= htmlspecialchars(esewa_form_url()) ?>" method="post">
<input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
<input type="hidden" name="tax_amount" value="0">
<input type="hidden" name="total_amount" value="<?= htmlspecialchars($amount) ?>">
<input type="hidden" name="transaction_uuid" value="<?= htmlspecialchars($transactionUuid) ?>">
<input type="hidden" name="product_code" value="<?= htmlspecialchars(ESEWA_PRODUCT_CODE) ?>">
<input type="hidden" name="product_service_charge" value="0">
<input type="hidden" name="product_delivery_charge" value="0">
<input type="hidden" name="success_url" value="<?= htmlspecialchars(site_url('payment/esewa_success.php')) ?>">
<input type="hidden" name="failure_url" value="<?= htmlspecialchars(site_url('payment/esewa_failure.php')) ?>">
<input type="hidden" name="signed_field_names" value="<?= htmlspecialchars($signedFieldNames) ?>">
<input type="hidden" name="signature" value="<?= htmlspecialchars($signature) ?>">
<button class="btn btn-danger btn-lg">Pay with eSewa</button>
<a href="../orders.php" class="btn btn-outline-secondary btn-lg">Cancel</a>
</form></div></div></body></html>
