<?php
ini_set('display_errors', 1);

// 1. Allow requests from any origin (replace * with a specific domain for production)
header("Access-Control-Allow-Origin: *");

// 2. Specify allowed HTTP methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// 3. Specify allowed HTTP headers
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

include_once 'consts.php';
