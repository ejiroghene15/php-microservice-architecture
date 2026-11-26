<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit();
}

$contents = @file_get_contents(DB_NAME);
if ($contents === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to read orders']);
    exit();
}

$orders = trim($contents) === '' ? [] : json_decode($contents, true);
if (!is_array($orders)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Invalid orders data']);
    exit();
}

$result = [];
foreach ($orders as $order) {
    $itemCount = 0;
    $totalCents = 0;
    foreach ($order['items'] as &$item) {
        $itemCount += $item['quantity'];
        $totalCents += $item['price_data']['unit_amount'] * $item['quantity'];
        $item = [
            'name' => $item['price_data']['product_data']['name'],
//            'description' => $item['price_data']['product_data']['description'],
            'price' => $item['price_data']['unit_amount'],
            'quantity' => $item['quantity'],
            'image' => $item['metadata']['image'] ?? '',
        ];
    }

    $result[] = [
        'order_id' => $order['order_id'],
        'customer' => $order['customer'] ?? null,
        'number_of_items' => $itemCount,
        'date' => date('F j, Y', strtotime($order['date'])),
        'amount' => $totalCents / 100,
        'payment_status' => $order['payment_status'],
        'items' => $order['items']
    ];
}

echo json_encode(['success' => true, 'orders' => $result]);
