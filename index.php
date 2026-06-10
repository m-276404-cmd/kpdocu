<?php 
session_start(); 
if(!isset($_SESSION['login_id'])){
    header('location:login.php');
    exit();
}
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
include 'header.php'; 
?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><?php echo ucwords(str_replace('_', ' ', $page)); ?></h2>
        </div>
        
        <?php 
$allowed_pages = ['home', 'profile', 'document_list', 'new_document', 'edit_document', 'view_document', 'user_list', 'new_user', 'edit_user'];        if(in_array($page, $allowed_pages) && file_exists($page . '.php')) {
            include $page . '.php';
        } else {
            include 'home.php';
        }
        ?>
    </div>
</div>

<footer class="footer">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> KP Documentation Services Management System. All rights reserved. | Developed by Bella </p>
</footer>

<?php include 'footer.php'; ?>