<?php
include('db_connect.php');
session_start();

if(!isset($_SESSION['login_id'])){
    header('location:login.php');
    exit();
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['login_id'];

// ONLY allow if editing own profile OR is admin
if($user_id != $_SESSION['login_id'] && $_SESSION['login_type'] != 1){
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
?>

<div class="container-fluid">
    <div id="msg-area"></div>
    
    <form id="userForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                </div>
                
                <div class="form-group mb-3">
                    <label>Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                </div>
                
                <div class="form-group mb-3">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group mb-3">
                    <label>Contact Number</label>
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group mb-3">
                    <label>New Password</label>
                    <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                    <small class="text-muted">Leave blank to keep current password</small>
                </div>
                
                <div class="form-group mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="cpass" id="cpass" class="form-control" autocomplete="new-password">
                    <span id="pass-match-msg" class="small"></span>
                </div>
                
                <?php if($_SESSION['login_type'] == 1): ?>
                <div class="form-group mb-3">
                    <label>User Type</label>
                    <select name="type" class="form-control">
                        <option value="2" <?php echo ($user['type'] == 2) ? 'selected' : ''; ?>>Employee</option>
                        <option value="1" <?php echo ($user['type'] == 1) ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-group mb-3">
                    <label>Avatar</label>
                    <input type="file" name="img" class="form-control" accept="image/*">
                    <?php if(!empty($user['avatar']) && file_exists('assets/uploads/'.$user['avatar'])): ?>
                        <div class="mt-2">
                            <img src="assets/uploads/<?php echo $user['avatar']; ?>" style="max-height: 60px;" class="img-thumbnail">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="form-group text-center mt-3">
            <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
            <button type="button" class="btn btn-secondary" onclick="location.href='index.php?page=user_list'">Cancel</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){



    // Password match validation
    $('#password, #cpass').on('keyup', function() {
        var pass = $('#password').val();
        var cpass = $('#cpass').val();
        
        if(pass != '' || cpass != '') {
            if(pass === cpass && pass != '') {
                $('#pass-match-msg').html('<i class="fa fa-check-circle text-success"></i> Passwords match');
            } else if(pass != cpass && cpass != '') {
                $('#pass-match-msg').html('<i class="fa fa-exclamation-circle text-danger"></i> Passwords do not match');
            } else {
                $('#pass-match-msg').html('');
            }
        } else {
            $('#pass-match-msg').html('');
        }
    });
    
    // Form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate passwords
        var pass = $('#password').val();
        var cpass = $('#cpass').val();
        
        if(pass != '' && pass !== cpass) {
            $('#msg-area').html('<div class="alert alert-danger">Passwords do not match!</div>');
            $('html, body').animate({scrollTop: 0}, 'slow');
            return false;
        }
        
        // Disable button and show loading
        var $btn = $('#saveBtn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'ajax.php?action=update_user',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'text',
            success: function(response) {
                response = response.trim();
                
                if(response == '1') {
                    $('#msg-area').html('<div class="alert alert-success">✓ User updated successfully! Redirecting...</div>');
                    $('html, body').animate({scrollTop: 0}, 'slow');
                    
                    setTimeout(function() {
                        window.location.href = 'index.php?page=user_list';
                    }, 1500);
                } else if(response == '2') {
                    $('#msg-area').html('<div class="alert alert-danger">Email already exists for another user!</div>');
                    $btn.prop('disabled', false).html('Save Changes');
                    $('html, body').animate({scrollTop: 0}, 'slow');
                } else {
                    $('#msg-area').html('<div class="alert alert-danger">Error: ' + response + '</div>');
                    $btn.prop('disabled', false).html('Save Changes');
                    $('html, body').animate({scrollTop: 0}, 'slow');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                $('#msg-area').html('<div class="alert alert-danger">AJAX Error: ' + error + '</div>');
                $btn.prop('disabled', false).html('Save Changes');
                $('html, body').animate({scrollTop: 0}, 'slow');
            }
        });
    });
});
</script>