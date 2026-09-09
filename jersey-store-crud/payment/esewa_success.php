<?php
session_start();
require_once __DIR__ . '/helpers.php';

$data = $_GET['data'] ?? '';
$decoded = base64_decode($data, true);
$response = $decoded !== false ? json_decode($decoded, true) : null;
$ok = is_array($response);
$error = '';
$orderId = 0;

if (!$ok) {
    $error = 'Invalid eSewa response.';
} else {
    $signedNames = (string)($response['signed_field_names'] ?? '');
    $expected = eSewa_signature($response, $signedNames, ESEWA_SECRET_KEY);
    if (!$signedNames || !isset($response['signature']) || !hash_equals($expected, (string)$response['signature'])) {
        $ok = false;
        $error = 'eSewa response signature could not be verified.';
    }
}

if ($ok) {
    $uuid = (string)($response['transaction_uuid'] ?? '');
    if (preg_match('/^ORDER-(\d+)-/', $uuid, $m)) $orderId = (int)$m[1];
    $stmt = $pdo->prepare('SELECT p.*, o.total_amount, o.user_id FROM payments p JOIN orders o ON o.order_id=p.order_id WHERE p.order_id=? AND p.gateway="eSewa"');
    $stmt->execute([$orderId]);
    $payment = $stmt->fetch();
    if (!$payment) {
        $ok = false;
        $error = 'Payment record was not found.';
    } elseif ((float)$payment['amount'] !== (float)$response['total_amount'] || (string)$payment['gateway_reference'] !== $uuid || (string)$response['product_code'] !== ESEWA_PRODUCT_CODE) {
        $ok = false;
        $error = 'eSewa payment details do not match the order.';
    }

    if ($ok) {
        $query = http_build_query([
            'product_code' => ESEWA_PRODUCT_CODE,
            'total_amount' => number_format((float)$payment['amount'], 2, '.', ''),
            'transaction_uuid' => $uuid,
        ]);
        $ch = curl_init(esewa_status_url() . '?' . $query);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30]);
        $body = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        $statusData = $body ? json_decode($body, true) : null;
        if ($curlError || !is_array($statusData)) {
            $ok = false;
            $error = 'Could not verify the eSewa transaction status.';
        } elseif (($statusData['status'] ?? '') !== 'COMPLETE') {
            $ok = false;
            $error = 'eSewa payment is not complete. Current status: ' . htmlspecialchars((string)($statusData['status'] ?? 'UNKNOWN'));
            mark_payment($orderId, 'Failed', $response['transaction_code'] ?? null, $statusData['ref_id'] ?? null, $body);
        } else {
            mark_payment($orderId, 'Paid', $response['transaction_code'] ?? null, $statusData['ref_id'] ?? null, $body);
            $success = true;
        }
    }
}

if (!empty($success) && !empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT order_id FROM orders WHERE order_id=? AND user_id=?');
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    if (!$stmt->fetch()) { $success = false; $error = 'Order access could not be verified.'; }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>eSewa Payment Result - JerseyHub</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/main.css"></head>
<body><div class="upper-nav text-center py-2">JerseyHub Payment</div><div class="container py-5" style="max-width:700px"><div class="border p-5 text-center">
<?php if (!empty($success)): ?><h2 class="text-success">Payment Successful</h2><p>Order #<?= $orderId ?> has been paid successfully.</p><?php else: ?><h2 class="text-danger">Payment Not Confirmed</h2><p><?= htmlspecialchars($error) ?></p><?php endif; ?>
<a href="../orders.php" class="btn btn-dark">View My Orders</a></div></div></body></html>
