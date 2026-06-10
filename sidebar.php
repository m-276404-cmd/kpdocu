<div class="sidebar">
    <div class="sidebar-menu">
        <div class="sidebar-item <?php echo ($page == 'home') ? 'active' : ''; ?>">
            <a href="index.php?page=home">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <?php if($_SESSION['login_type'] == 1): ?>
        <div class="sidebar-item <?php echo ($page == 'user_list' || $page == 'new_user' || $page == 'edit_user') ? 'active' : ''; ?>">
            <a href="index.php?page=user_list">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="sidebar-item <?php echo ($page == 'document_list' || $page == 'new_document' || $page == 'edit_document') ? 'active' : ''; ?>">
            <a href="index.php?page=document_list">
                <i class="fas fa-folder-open"></i>
                <span>Documents</span>
            </a>
        </div>
        
       
    </div>
</div>

<script>
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home'; ?>';
</script>