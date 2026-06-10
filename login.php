<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="assets/images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | KP Documentation Services Management System</title>
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
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
            font-size: 60px;
            color: #667eea;
        }
        .login-logo h2 {
            font-weight: bold;
            margin-top: 15px;
            color: #333;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            width: 100%;
            color: white;
            font-size: 16px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102,126,234,0.4);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
    <img src="assets/images/logo.png" alt="KP Docu" style="width: 100px; height: auto; margin-bottom: 20px;">
            <h2>KPDocu</h2>
            <p class="text-muted">KP Documentation Services Management System</p>
        </div>
        
        <div id="msg"></div>
        
        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-login" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            <div class="text-center mt-3">
                <a href="request_reset.php" class="text-decoration-none">Forgot Password?</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        var $btn = $('#loginBtn');
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Logging in...').prop('disabled', true);
        
        $.ajax({
            url: 'ajax.php?action=login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'text',
            success: function(resp) {
                console.log('Login response:', resp);
                if(resp == '1') {
                    window.location.href = 'index.php?page=home';
                } else if(resp == '4') {
                    $('#msg').html('<div class="alert alert-danger">Account locked. Too many failed attempts. Try again later.</div>');
                    $btn.html(originalText).prop('disabled', false);
                } else {
                    $('#msg').html('<div class="alert alert-danger">Invalid email or password.</div>');
                    $btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.log('AJAX Error:', xhr.responseText);
                $('#msg').html('<div class="alert alert-danger">Connection error. Please try again.</div>');
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });
    </script>
</body>
</html>