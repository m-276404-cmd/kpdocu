<?php include 'db_connect.php' ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Document List</h5>
        <a href="index.php?page=new_document" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Document
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Company Name</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $where = ($_SESSION['login_type'] != 1) ? "WHERE d.user_id = {$_SESSION['login_id']}" : "";
                $qry = $conn->query("SELECT d.*, CONCAT(u.firstname, ' ', u.lastname) as created_by 
                                    FROM documents d 
                                    LEFT JOIN users u ON d.user_id = u.id 
                                    $where 
                                    ORDER BY d.id DESC");
                $i = 1;
                while($row = $qry->fetch_assoc()):
                    $status_class = '';
                    $status_text = '';
                    if($row['status'] == 'completed') {
                        $status_class = 'success';
                        $status_text = 'Completed';
                    } elseif($row['status'] == 'pending') {
                        $status_class = 'warning';
                        $status_text = 'Pending';
                    } else {
                        $status_class = 'danger';
                        $status_text = 'Incomplete';
                    }
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td>
                        <span class="badge bg-<?php echo $status_class; ?>">
                            <?php echo $status_text; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($row['created_by'] ?? 'System'); ?></td>
                    <td><?php echo date('d M Y h:i A', strtotime($row['date_created'])); ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="index.php?page=edit_document&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="viewDocument(<?php echo $row['id']; ?>)" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteDocument(<?php echo $row['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteDocument(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax.php?action=delete_file',
                type: 'POST',
                data: {id: id},
                success: function(resp) {
                    if(resp == 1) {
                        Swal.fire('Deleted!', 'Document has been deleted.', 'success');
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        Swal.fire('Error!', 'Failed to delete document.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'AJAX error occurred.', 'error');
                }
            });
        }
    });
}

function viewDocument(id) {
    window.location.href = 'index.php?page=view_document&id=' + id;
}

$(document).ready(function() {
    if($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });
    }
});
</script>