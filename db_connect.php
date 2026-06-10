<?php
// For LOCAL (XAMPP)
if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'kpdocu';
} 
// For RENDER (Online)
else {
    // These will be set from Render environment variables
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '';
    $db   = getenv('DB_NAME') ?: 'kpdocu';
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>