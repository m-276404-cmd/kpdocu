<?php
include 'db_connect.php';

$email = "admin@kpdocu.com";
$new_password = "admin123";
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = '$hashed' WHERE email = '$email'";

if($conn->query($sql)) {
    echo "<p style='color:green'>✓ Password updated for $email</p>";
    echo "<p>New Password: admin123</p>";
} else {
    echo "<p style='color:red'>Error: " . $conn->error . "</p>";
}

// Also update test user
$email2 = "test@kpdocu.com";
$sql2 = "UPDATE users SET password = '$hashed' WHERE email = '$email2'";
$conn->query($sql2);

echo "<p>You can now login with:</p>";
echo "<ul>";
echo "<li>Email: admin@kpdocu.com | Password: admin123</li>";
echo "<li>Email: test@kpdocu.com | Password: admin123</li>";
echo "</ul>";
echo "<a href='login.php'>Go to Login</a>";
?>