<?php
include 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier_type (identifier, type),
    INDEX idx_attempt_time (attempt_time)
);";
if($conn->query($sql)){
    echo 'rate_limits table created successfully\n';
} else {
    echo 'Error creating rate_limits table: ' . $conn->error . '\n';
}

$sql2 = "CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    event_type VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at)
);";
if($conn->query($sql2)){
    echo 'security_logs table created successfully\n';
} else {
    echo 'Error creating security_logs table: ' . $conn->error . '\n';
}

$sql3 = "CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_session_token (session_token),
    INDEX idx_last_activity (last_activity)
);";
if($conn->query($sql3)){
    echo 'user_sessions table created successfully\n';
} else {
    echo 'Error creating user_sessions table: ' . $conn->error . '\n';
}

echo 'Database setup complete.\n';
?>