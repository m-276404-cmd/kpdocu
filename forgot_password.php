<?php 
session_start();
require_once('./db_connect.php');
require_once('./functions.php');

// Get token and email from URL
$reset_token = isset($_GET['token']) ? $_GET['token'] : '';
$email = isset($_GET['email']) ? urldecode($_GET['email']) : '';

$valid_token = false;
$error_message = '';

if (!empty($reset_token) && !empty($email)) {
    // Check in database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("ss", $email, $reset_token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $valid_token = true;
        $user = $result->fetch_assoc();
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_token'] = $reset_token;
    } else {
        $error_message = "Invalid or expired reset link. Please request a new one.";
    }
    $stmt->close();
} else {
    $error_message = "No reset link provided. Please request a password reset.";
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Reset Password | KP Documentation Services Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            width: 100%;
            color: white;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
        .strength-weak { background-color: #dc3545; width: 33%; }
        .strength-medium { background-color: #ffc107; width: 66%; }
        .strength-strong { background-color: #28a745; width: 100%; }
    </style>
</head>
<body>
    <div class="card">
        <div class="text-center mb-4">
            <i class="fas fa-lock fa-3x" style="color: #667eea;"></i>
            <h2 class="mt-2">Reset Password</h2>
        </div>
        
        <?php if($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <div class="text-center mt-3">
                <a href="request_reset.php" class="btn btn-primary">Request New Reset Link</a>
                <a href="login.php" class="btn btn-secondary">Back to Login</a>
            </div>
        <?php elseif($valid_token): ?>
            <div id="msg-area"></div>
            <form id="reset-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($reset_token); ?>">
                
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly disabled>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password" required>
                    <div class="password-strength" id="password-strength"></div>
                    <small class="text-muted">Password must be at least 8 characters with uppercase, lowercase, and number</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" id="cpassword" name="cpassword" class="form-control" placeholder="Confirm new password" required>
                    <div id="password-match" class="small"></div>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check-circle"></i> Reset Password
                </button>
                
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    <?php if($valid_token): ?>
    $('#password').on('keyup', function() {
        var strength = 0;
        var pw = $(this).val();
        if (pw.length >= 8) strength++;
        if (pw.match(/[a-z]+/)) strength++;
        if (pw.match(/[A-Z]+/)) strength++;
        if (pw.match(/[0-9]+/)) strength++;
        
        var bar = $('#password-strength');
        bar.removeClass('strength-weak strength-medium strength-strong');
        if (pw.length > 0) {
            if (strength < 3) bar.addClass('strength-weak');
            else if (strength < 4) bar.addClass('strength-medium');
            else bar.addClass('strength-strong');
        }
    });

    $('#cpassword').on('keyup', function() {
        var pass = $('#password').val();
        var cpass = $(this).val();
        
        if (cpass.length > 0) {
            if (pass === cpass) {
                $('#password-match').html('<i class="fa fa-check-circle text-success"></i> Passwords match').css('color', 'green');
            } else {
                $('#password-match').html('<i class="fa fa-exclamation-circle text-danger"></i> Passwords do not match').css('color', 'red');
            }
        } else {
            $('#password-match').html('');
        }
    });

    $('#reset-form').submit(function(e) {
        e.preventDefault();
        
        var pw = $('#password').val();
        var cpw = $('#cpassword').val();
        
        if(!pw || !cpw) {
            $('#msg-area').html('<div class="alert alert-danger">All fields required</div>');
            return;
        }
        if(pw !== cpw) {
            $('#msg-area').html('<div class="alert alert-danger">Passwords do not match</div>');
            return;
        }
        if(pw.length < 8 || !pw.match(/[a-z]/) || !pw.match(/[A-Z]/) || !pw.match(/[0-9]/)) {
            $('#msg-area').html('<div class="alert alert-danger">Password too weak. Must be 8+ chars with uppercase, lowercase, and number</div>');
            return;
        }
        
        var $btn = $(this).find('button[type="submit"]');
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
        
        $.ajax({
            url: 'ajax.php?action=reset_password',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                if (resp.status === 'success') {
                    $('#msg-area').html('<div class="alert alert-success">' + resp.message + '</div>');
                    setTimeout(function() { 
                        window.location.href = 'login.php'; 
                    }, 3000);
                } else {
                    $('#msg-area').html('<div class="alert alert-danger">' + resp.message + '</div>');
                    $btn.html('<i class="fas fa-check-circle"></i> Reset Password').prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
                $('#msg-area').html('<div class="alert alert-danger">Connection error. Please try again.</div>');
                $btn.html('<i class="fas fa-check-circle"></i> Reset Password').prop('disabled', false);
            }
        });
    });
    <?php endif; ?>
    </script>
</body>
</html>