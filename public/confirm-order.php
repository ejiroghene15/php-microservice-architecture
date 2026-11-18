<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit();
}

// * READ DB FILE
$contents = file_get_contents(DB_NAME);

// * read orders from db
$orders = json_decode($contents, true);

$orderId = $_GET['orderId'];

// * FIND ORDER IN DB
$findOrder = $orders[$orderId];

if (!$findOrder) {
    http_response_code(404);
    exit();
}

// * SUM THE TOTAL AMOUNT OF THE ORDER
$totalAmount = 0;
foreach ($findOrder['items'] as $item) {
    // Divide the unit amount by 1000 to get the real amount value
    $totalAmount += ($item['price_data']['unit_amount'] / 100) * $item['quantity'];
}

// * UPDATE PAYMENT STATUS TO processing
if ($findOrder['payment_status'] === 'pending') {
    $findOrder['payment_status'] = 'processing';
}

// * SAVE TO DB
$orders[$orderId] = $findOrder;
file_put_contents(DB_NAME, json_encode($orders));

// * Rewrite items array to store every deep nested details into one level
foreach ($findOrder['items'] as &$item) {
    $item = [
        'name' => $item['price_data']['product_data']['name'],
        'description' => $item['price_data']['product_data']['description'],
        'price' => $item['price_data']['unit_amount'],
        'quantity' => $item['quantity'],
        'image' => $item['metadata']['image'] ?? '',
    ];
}

// * RETURN SUCCESS RESPONSE
echo json_encode([
    'success' => true,
    'items' => $findOrder['items'],
    'orderDate' => date('F dS, Y', strtotime($findOrder['date'])),
    'totalAmount' => $totalAmount
]);