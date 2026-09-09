<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

function payment_json_request(string $url, array $payload, string $secretKey): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Key ' . $secretKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 30,
    ]);
    $body = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($body === false || $error) {
        throw new RuntimeException('Payment gateway connection failed: ' . $error);
    }
    $data = json_decode($body, true);
    if (!is_array($data)) {
        throw new RuntimeException('Invalid response from payment gateway.');
    }
    $data['_http_code'] = $httpCode;
    return $data;
}

function get_user_order(int $orderId, int $userId): ?array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_id = ? AND user_id = ?');
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();
    return $order ?: null;
}

function mark_payment(int $orderId, string $status, ?string $transactionId, ?string $gatewayReference, ?string $rawResponse = null): void {
    global $pdo;
    $paidAt = $status === 'Paid' ? date('Y-m-d H:i:s') : null;
    $stmt = $pdo->prepare('UPDATE payments SET status=?, transaction_id=?, gateway_reference=?, raw_response=?, paid_at=? WHERE order_id=?');
    $stmt->execute([$status, $transactionId, $gatewayReference, $rawResponse, $paidAt, $orderId]);

    $paymentStatus = $status === 'Paid' ? 'Paid' : ($status === 'Pending' ? 'Pending' : 'Failed');
    $orderStatus = $status === 'Paid' ? 'Processing' : 'Pending';
    $stmt = $pdo->prepare('UPDATE orders SET payment_status=?, status=? WHERE order_id=?');
    $stmt->execute([$paymentStatus, $orderStatus, $orderId]);
}

function eSewa_signature(array $fields, string $signedFieldNames, string $secret): string {
    $names = array_filter(array_map('trim', explode(',', $signedFieldNames)));
    $parts = [];
    foreach ($names as $name) {
        $parts[] = $name . '=' . ($fields[$name] ?? '');
    }
    return base64_encode(hash_hmac('sha256', implode(',', $parts), $secret, true));
}
