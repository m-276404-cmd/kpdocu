<?php include 'db_connect.php' ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">User List</h5>
        <?php if($_SESSION['login_type'] == 1): ?>
        <a href="index.php?page=new_user" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add User
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table class="table table-bordered datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $qry = $conn->query("SELECT * FROM users ORDER BY firstname ASC");
                $i = 1;
                while($row = $qry->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <?php if($row['type'] == 1): ?>
                            <span class="badge bg-primary">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Employee</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                           <a href="javascript:void(0)" 
   onclick="uni_modal('View User: <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>', 'view_user.php?id=<?php echo $row['id']; ?>')" 
   class="btn btn-sm btn-outline-info" title="View">
    <i class="fas fa-eye"></i>
</a>
                            <a href="index.php?page=edit_user&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if($_SESSION['login_type'] == 1 && $row['id'] != $_SESSION['login_id']): ?>
                            <button onclick="deleteUser(<?php echo $row['id']; ?>)" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax.php?action=delete_user',
                type: 'POST',
                data: {id: id},
                success: function(resp) {
                    if(resp == 1) {
                        Swal.fire('Deleted!', 'User has been deleted.', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', 'Failed to delete user.', 'error');
                    }
                }
            });
        }
    });
}

$(document).ready(function() {
    $('.datatable').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search..."
        }
    });
});
</script>