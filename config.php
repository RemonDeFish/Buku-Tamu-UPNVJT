<?php

session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'raymond');
define('DB_PASS', '24081010160');
define('DB_NAME', 'buku_tamu');

define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'Buku Tamu UPNVJT');

try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
