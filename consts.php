<?php
const DB_NAME = 'db.json';

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    define("FRONTEND_PATH", 'http://localhost:4200/angular-store');
} else {
    define("FRONTEND_PATH", 'https://ejiroghene15.github.io/angular-store');
}
