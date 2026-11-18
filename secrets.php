<?php
// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
// Retrieve the Stripe secret key from environment variables
return $stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?? $_ENV['STRIPE_SECRET_KEY'];