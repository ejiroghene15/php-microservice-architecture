<?php
// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve the Stripe secret key from environment variables
return $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];