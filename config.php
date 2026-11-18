<?php
ini_set('display_errors', 1);

// 1. Allow requests from any origin (replace * with a specific domain for production)

if ($_SERVER['SERVER_NAME'] === 'localhost') {
    header("Access-Control-Allow-Origin: *");
} else {
    header("Access-Control-Allow-Origin: https://angular-store-phi.vercel.app");
}

// 2. Specify allowed HTTP methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// 3. Specify allowed HTTP headers
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


header('Content-Type: application/json');

include_once 'consts.php';
