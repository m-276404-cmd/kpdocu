<?php
session_start();
// Only allow admin to view email logs
if(!isset($_SESSION['login_type']) || $_SESSION['login_type'] != 1){
    die("Access denied. Admin only.");
}

$log_file = __DIR__ . '/email_log.txt';

echo "<h2>Email Reset Link Log</h2>";

if(file_exists($log_file)){
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($log_file));
    echo "</pre>";
} else {
    echo "No email log file found yet. Request a password reset to create it.";
}

echo "<br><a href='index.php?page=home'>Back to Dashboard</a>";
?>