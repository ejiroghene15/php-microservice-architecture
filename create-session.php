<?php

use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

require_once './vendor/autoload.php';
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

if (!file_exists(DB_NAME)) {
    touch(DB_NAME);
}

$stripeSecretKey = require_once './secrets.php';

$stripe = new StripeClient([
    "api_key" => $stripeSecretKey,
    "stripe_version" => "2026-10-28.endive"
]);

$payload = json_decode(file_get_contents('php://input'), true);

// * Generate order id in the format - 'ORD-2026-0042'
$order_id = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

$line_items = array_map(function ($item) {
    return [
        'price_data' => [
            'product_data' => [
                'name' => $item['title'],
                'description' => $item['description'],
            ],
            'currency' => 'usd',
            'unit_amount' => $item['price'] * 100,
        ],
        'quantity' => $item['quantity'],
        'metadata' => ['image' => $item['thumbnail']]
    ];
}, $payload['items']);

// * Open a file based db
// Line items - This is the product data
//[
//    'price_data' => [
//        'product_data' => [
//            'name' => 'T-shirt',
//            'description' => 'This is a t-shirt',
//        ],
//        'currency' => 'usd',
//        'unit_amount' => 1000,
//    ],
//    'quantity' => 1,
//]

# Save to DB with payment status of pending and return OrderID
# Append OrderID to success url endpoint
# Webhook with update the order payment status to paid on the backend

try {
    $checkout_session = $stripe->checkout->sessions->create([
        'ui_mode' => 'form', // form, embedded_page,
        'line_items' => $line_items,
        'client_reference_id' => $order_id,
        'metadata' => [
            'order_id' => $order_id
        ],
        'mode' => 'payment',
        'return_url' => FRONTEND_PATH . "/order-completed?order_id={$order_id}&session_id={CHECKOUT_SESSION_ID}",
        'integration_identifier' => 'embedded_web_0001' //| for ui_mode = form
    ]);

    $orders = [];

    // * Read existing data from the database
    $contents = file_get_contents(DB_NAME);
    if ($contents) {
        $orders = json_decode($contents, true);
    }

    $orders[$order_id] = [
        'order_id' => $order_id,
        'items' => $line_items,
        'payment_method' => 'stripe',
        'payment_status' => 'pending',
        'payment_session_id' => $checkout_session->id,
        'date' => date('Y-m-d H:i:s'),
    ];

    // * SAVE TO DB
    file_put_contents(DB_NAME, json_encode($orders));

    echo json_encode(['success' => true, 'clientSecret' => $checkout_session->client_secret]);

} catch (ApiErrorException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    http_response_code(500);
}

