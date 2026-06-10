<?php include 'db_connect.php'; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">New User</h5>
    </div>
    <div class="card-body">
        <form id="userForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" class="form-control" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" class="form-control" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <small class="text-muted">Minimum 8 characters</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="cpass" id="cpass" class="form-control" required>
                    <span id="pass_match_msg" class="small"></span>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control">
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Avatar (Optional)</label>
                    <input type="file" name="img" class="form-control" accept="image/*">
                </div>
                
                <?php if($_SESSION['login_type'] == 1): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label">User Type</label>
                    <select name="type" class="form-select">
                        <option value="2">Employee</option>
                        <option value="1">Admin</option>
                    </select>
                </div>
                <?php else: ?>
                    <input type="hidden" name="type" value="2">
                <?php endif; ?>
            </div>
            
            <div class="text-end">
                <a href="index.php?page=user_list" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">Create User</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Password match validation
    $('#password, #cpass').on('keyup', function() {
        var password = $('#password').val();
        var cpassword = $('#cpass').val();
        
        if (password != '' && cpassword != '') {
            if (password === cpassword) {
                $('#pass_match_msg').html('<i class="fas fa-check-circle text-success"></i> Passwords match').css('color', 'green');
            } else {
                $('#pass_match_msg').html('<i class="fas fa-exclamation-circle text-danger"></i> Passwords do not match').css('color', 'red');
            }
        } else {
            $('#pass_match_msg').html('');
        }
    });
    
    // Form submission
    $('#userForm').submit(function(e) {
        e.preventDefault();
        
        // Validate passwords match
        var password = $('#password').val();
        var cpassword = $('#cpass').val();
        
        if (password !== cpassword) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Passwords do not match!'
            });
            return false;
        }
        
        if (password.length < 8) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Password must be at least 8 characters!'
            });
            return false;
        }
        
        // Disable button and show loading
        var $btn = $('#submitBtn');
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Creating...').prop('disabled', true);
        
        // Use FormData for file upload support
        var formData = new FormData(this);
        
        $.ajax({
            url: 'ajax.php?action=save_user',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'text',
            success: function(resp) {
                console.log('Response:', resp);
                resp = resp.trim();
                
                if (resp == '1') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'User created successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'index.php?page=user_list';
                    });
                } else if (resp == '2') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Email already exists!'
                    });
                    $btn.html('Create User').prop('disabled', false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to create user. Error: ' + resp
                    });
                    $btn.html('Create User').prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'AJAX Error: ' + error
                });
                $btn.html('Create User').prop('disabled', false);
            }
        });
    });
});
</script>