<?php
include 'db_connect.php';

// Only allow logged in users
if(!isset($_SESSION['login_id'])){
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['login_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-user-circle"></i> My Profile</h5>
    </div>
    <div class="card-body">
        <form id="profileForm">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>">
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                    <small class="text-muted">Leave blank to keep current password</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="cpass" id="cpass" class="form-control" autocomplete="new-password">
                    <span id="pass_match_msg" class="small"></span>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Avatar</label>
                    <input type="file" name="img" class="form-control" accept="image/*">
                    <?php if(!empty($user['avatar']) && file_exists('assets/uploads/'.$user['avatar'])): ?>
                        <div class="mt-2">
                            <img src="assets/uploads/<?php echo $user['avatar']; ?>" style="max-height: 60px;" class="img-thumbnail">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary" id="submitBtn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#password, #cpass').on('keyup', function() {
        var password = $('#password').val();
        var cpassword = $('#cpass').val();
        
        if (password != '' || cpassword != '') {
            if (password === cpassword && password != '') {
                $('#pass_match_msg').html('<i class="fas fa-check-circle text-success"></i> Passwords match');
            } else if (password != cpassword && cpassword != '') {
                $('#pass_match_msg').html('<i class="fas fa-exclamation-circle text-danger"></i> Passwords do not match');
            } else {
                $('#pass_match_msg').html('');
            }
        } else {
            $('#pass_match_msg').html('');
        }
    });
    
    $('#profileForm').submit(function(e) {
        e.preventDefault();
        
        var password = $('#password').val();
        var cpassword = $('#cpass').val();
        
        if (password !== cpassword) {
            Swal.fire({ icon: 'error', title: 'Error!', text: 'Passwords do not match!' });
            return false;
        }
        
        if (password.length > 0 && password.length < 8) {
            Swal.fire({ icon: 'error', title: 'Error!', text: 'Password must be at least 8 characters!' });
            return false;
        }
        
        var $btn = $('#submitBtn');
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'ajax.php?action=update_profile',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'text',
            success: function(resp) {
                resp = resp.trim();
                
                if (resp == '1') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Profile updated successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'index.php?page=home';
                    });
                } else if (resp == '2') {
                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Email already exists!' });
                    $btn.html('Save Changes').prop('disabled', false);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Failed to update profile!' });
                    $btn.html('Save Changes').prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire({ icon: 'error', title: 'Error!', text: 'AJAX Error occurred!' });
                $btn.html('Save Changes').prop('disabled', false);
            }
        });
    });
});
</script>