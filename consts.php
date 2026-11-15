<?php
const DB_NAME = 'db.json';

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define("FRONTEND_PATH", 'http://localhost:4200');
} else {
    define("FRONTEND_PATH", 'https://ejiroghene15.github.io/angular-store');
}
