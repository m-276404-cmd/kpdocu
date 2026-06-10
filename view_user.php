<?php
include 'db_connect.php';

// User type array
$type_arr = array('', 'Admin', 'Employee');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo '<div class="alert alert-warning">Invalid user ID.</div>';
    return;
}

$qry = $conn->query("SELECT * FROM users WHERE id = {$id}");

if (!$qry) {
    die('Query error: ' . $conn->error);
}

if ($qry->num_rows <= 0) {
    echo '<div class="alert alert-warning">User not found.</div>';
    return;
}

$row = $qry->fetch_assoc();

// Set variables
$firstname = $row['firstname'] ?? '';
$lastname = $row['lastname'] ?? '';
$name = trim($firstname . ' ' . $lastname);
$email = $row['email'] ?? '';
$avatar = $row['avatar'] ?? '';
$address = $row['address'] ?? '';
$contact = $row['contact'] ?? '';
$type = isset($row['type']) ? (int)$row['type'] : 2;
$date_created = isset($row['date_created']) ? $row['date_created'] : '';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <?php if(empty($avatar) || !file_exists('assets/uploads/'.$avatar)): ?>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px;height:120px;font-size: 48px;">
                            <?php echo strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1)); ?>
                        </div>
                    <?php else: ?>
                        <img src="assets/uploads/<?php echo $avatar; ?>" class="rounded-circle" style="width: 120px;height:120px;object-fit:cover;">
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Full Name</th>
                            <td><?php echo ucwords(htmlspecialchars($name)); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($email); ?></td>
                        </tr>
                        <tr>
                            <th>Contact</th>
                            <td><?php echo !empty($contact) ? htmlspecialchars($contact) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th>User Type</th>
                            <td>
                                <?php if($type == 1): ?>
                                    <span class="badge bg-primary">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Employee</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo !empty($address) ? nl2br(htmlspecialchars($address)) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th>Date Created</th>
                            <td><?php echo !empty($date_created) ? date('d M Y, h:i A', strtotime($date_created)) : '-'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>