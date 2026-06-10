<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <button class="btn btn-link d-lg-none me-3" id="sidebarToggle">
            <i class="fas fa-bars fs-4 text-secondary"></i>
        </button>
        
     <a class="navbar-brand" href="index.php?page=home">
    <img src="assets/images/logo.png" alt="KP Logo" style="height: 45px; width: auto; margin-right: 8px;">
    <span style="font-weight: 600;">KP Documentation Services Management System</span>
</a>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle text-dark text-decoration-none" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fs-4"></i>
                    <span class="ms-2 d-none d-md-inline"><?php echo ucfirst($_SESSION['login_firstname'] ?? 'User'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li>
                        <a class="dropdown-item" href="index.php?page=profile">
                            <i class="fas fa-user me-2"></i> My Account
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="ajax.php?action=logout">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
    $('#sidebarToggle').click(function() {
        $('.sidebar').toggleClass('open');
    });
</script>