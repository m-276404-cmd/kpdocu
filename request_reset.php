<?php 
session_start();
require_once('./db_connect.php');
require_once('./functions.php');

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Forgot Password | KP Documentation Services Management System</title>
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
    </style>
</head>
<body>
    <div class="card">
        <div class="text-center mb-4">
            <i class="fas fa-key fa-3x" style="color: #667eea;"></i>
            <h2 class="mt-2">Forgot Password</h2>
            <p class="text-muted">Enter your email to receive reset link</p>
        </div>
        
        <div id="msg-area"></div>
        
        <form id="request-reset-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            
            <button type="submit" id="send-btn" class="btn-primary">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Back to Login</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $('#request-reset-form').submit(function(e) {
        e.preventDefault();
        
        var email = $('#email').val().trim();
        if(!email) {
            $('#msg-area').html('<div class="alert alert-danger">Please enter your email address.</div>');
            return;
        }
        
        var $btn = $('#send-btn');
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);
        
        $.ajax({
            url: 'ajax.php?action=request_reset',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#msg-area').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#email').val('');
                } else {
                    $('#msg-area').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
                $btn.html(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                console.log('AJAX Error:', xhr.responseText);
                $('#msg-area').html('<div class="alert alert-danger">Connection error. Please try again.</div>');
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });
    </script>
</body>
</html>