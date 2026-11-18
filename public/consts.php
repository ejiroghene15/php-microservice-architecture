<?php
const DB_NAME = 'db.json';

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    define("FRONTEND_PATH", 'http://localhost:4200');
} else {
    define("FRONTEND_PATH", 'https://angular-store-phi.vercel.app');
}
