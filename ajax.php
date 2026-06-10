<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/lib/PHPMailer/Exception.php';
require_once __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/lib/PHPMailer/SMTP.php';

ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '500M');ob_start();
session_start();
include 'admin_class.php';
include 'db_connect.php';
include 'functions.php';

$crud = new Action();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'login':
        echo $crud->login();
        break;
    case 'logout':
        echo $crud->logout();
        break;
    case 'save_user':
    echo $crud->save_user();
    break;
    case 'update_user':
        echo $crud->update_user();
        break;
    case 'delete_user':
        echo $crud->delete_user();
        break;
    case 'save_document_multipage':
        echo $crud->save_document_multipage();
        break;
    case 'delete_file':
        echo $crud->delete_file();
        break;
    case 'upload_file':
        echo $crud->upload_file();
        break;
    case 'remove_file':
        echo $crud->remove_file();
        break;
    
    // ============ ADD THESE TWO CASES ============
    case 'request_reset':
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Valid email is required']);
            exit;
        }
        
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $stmt->bind_param("ssi", $token, $expiry, $user['id']);
            
            if($stmt->execute()) {
                $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $reset_link = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/forgot_password.php?email=" . urlencode($email) . "&token=" . $token;
                
              // ---------- SEND REAL EMAIL USING GMAIL ----------
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'm-276404@moe-dl.edu.my';     // CHANGE THIS - Your Gmail address
    $mail->Password   = 'hgxw gxou xdvy vhef';      // Your App Password (with spaces)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    // Recipient
    $mail->setFrom('m-276404@moe-dl.edu.my', 'KP Documentation Services Management System');  // CHANGE THIS
    $mail->addAddress($email);
    
    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request - KP Documentation Services Management System';
    $mail->Body    = '
        <h2>Password Reset Request</h2>
        <p>Click the link below to reset your password:</p>
        <p><a href="' . $reset_link . '">' . $reset_link . '</a></p>
        <p>This link will expire in 1 hour.</p>
        <p>If you did not request this, please ignore this email.</p>
    ';
    $mail->AltBody = "Reset your password at this link: $reset_link";
    
    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Reset link sent to your email! Please check your inbox or spam folder.']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Error: ' . $mail->ErrorInfo]);
}                exit;
            }
        }
        
        // Always return success for security (don't reveal if email exists)
        echo json_encode(['status' => 'success', 'message' => 'If your email exists, you will receive a reset link.']);
        break;
    
    case 'reset_password':
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']);
            exit;
        }
        
        $email = $_POST['email'];
        $reset_token = $_POST['reset_token'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        
        if (empty($email) || empty($reset_token) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }
        
        if ($password !== $cpassword) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
            exit;
        }
        
        if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters with uppercase, lowercase, and number']);
            exit;
        }
        
        // Verify token
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()");
        $stmt->bind_param("ss", $email, $reset_token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or expired reset link']);
            exit;
        }
        
        $user = $result->fetch_assoc();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL, last_password_change = NOW() WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user['id']);
        
        if($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully. Redirecting to login...']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password. Please try again.']);
        }
        break;
    
        case 'update_profile':
    echo $crud->update_profile();
    break;
    
    default:
        echo "0";
        break;
}
?>