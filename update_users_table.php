<?php
include 'db_connect.php';

$sql = "ALTER TABLE users 
ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS reset_expiry DATETIME NULL,
ADD COLUMN IF NOT EXISTS last_password_change DATETIME NULL,
ADD COLUMN IF NOT EXISTS account_locked TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS failed_login_attempts INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS lockout_until DATETIME NULL;";

if($conn->query($sql)){
    echo 'Users table updated successfully\n';
} else {
    echo 'Error updating users table: ' . $conn->error . '\n';
}
?>