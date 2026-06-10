<?php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_password($password) {
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    return preg_match($pattern, $password);
}

function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

function check_rate_limit($conn, $identifier, $type = 'password_reset') {
    $max_attempts = 5;
    $lockout_time = 900;
    
    $stmt = $conn->prepare("DELETE FROM rate_limits WHERE attempt_time < DATE_SUB(NOW(), INTERVAL ? SECOND)");
    $stmt->bind_param("i", $lockout_time);
    $stmt->execute();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM rate_limits WHERE identifier = ? AND type = ?");
    $stmt->bind_param("ss", $identifier, $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['attempts'] >= $max_attempts) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO rate_limits (identifier, type, attempt_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $identifier, $type);
    $stmt->execute();
    
    return true;
}

function log_security_event($conn, $user_id, $event_type, $details = '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $stmt = $conn->prepare("INSERT INTO security_logs (user_id, event_type, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issss", $user_id, $event_type, $details, $ip, $user_agent);
    $stmt->execute();
}

function send_reset_email($email, $reset_link) {
    $log_file = __DIR__ . '/email_log.txt';
    $log_entry = date('Y-m-d H:i:s') . " - TO: $email - LINK: $reset_link\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    return true;
}
?>