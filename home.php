<?php include 'db_connect.php'; ?>

<div class="row g-4">
    <?php if($_SESSION['login_type'] == 1): ?>
    <!-- ADMIN DASHBOARD - Shows all documents -->
    <div class="col-md-6 col-xl-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Documents</h6>
                        <h2 class="mb-0">
                            <?php 
                            $cnt = $conn->query("SELECT COUNT(*) as cnt FROM documents")->fetch_assoc();
                            echo $cnt['cnt']; 
                            ?>
                        </h2>
                    </div>
                    <i class="fas fa-folder-open fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Users</h6>
                        <h2 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt']; ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Pending Documents</h6>
                        <h2 class="mb-0">
                            <?php 
                            $pending = $conn->query("SELECT COUNT(*) as cnt FROM documents WHERE status = 'pending'")->fetch_assoc();
                            echo $pending['cnt']; 
                            ?>
                        </h2>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Completed</h6>
                        <h2 class="mb-0">
                            <?php 
                            $completed = $conn->query("SELECT COUNT(*) as cnt FROM documents WHERE status = 'completed'")->fetch_assoc();
                            echo $completed['cnt']; 
                            ?>
                        </h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- EMPLOYEE DASHBOARD - Shows ONLY their own documents -->
    <?php 
    $user_id = $_SESSION['login_id'];
    
    // Get employee's document counts
    $total = $conn->query("SELECT COUNT(*) as cnt FROM documents WHERE user_id = $user_id")->fetch_assoc();
    $pending = $conn->query("SELECT COUNT(*) as cnt FROM documents WHERE user_id = $user_id AND status = 'pending'")->fetch_assoc();
    $completed = $conn->query("SELECT COUNT(*) as cnt FROM documents WHERE user_id = $user_id AND status = 'completed'")->fetch_assoc();
    $incomplete = $conn->query("SELECT COUNT(*) as cnt FROM documents WHERE user_id = $user_id AND status = 'incomplete'")->fetch_assoc();
    ?>
    
    <div class="col-md-6 col-xl-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">My Documents</h6>
                        <h2 class="mb-0"><?php echo $total['cnt']; ?></h2>
                    </div>
                    <i class="fas fa-folder-open fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Pending</h6>
                        <h2 class="mb-0"><?php echo $pending['cnt']; ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Completed</h6>
                        <h2 class="mb-0"><?php echo $completed['cnt']; ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Incomplete</h6>
                        <h2 class="mb-0"><?php echo $incomplete['cnt']; ?></h2>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Welcome, <?php echo $_SESSION['login_firstname']; ?>!</h5>
            </div>
            <div class="card-body">
                <p>This is your KP Documentation Management System dashboard. Use the sidebar to navigate.</p>
                <hr>
                <h6>Quick Actions:</h6>
                <div class="d-flex gap-3 mt-3">
                    <a href="index.php?page=new_document" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Document
                    </a>
                    <a href="index.php?page=document_list" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View Documents
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>