    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function showMessage(type, message) {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Success!' : 'Error!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        }
        
        function confirmDelete(url, id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url + '?id=' + id;
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
    <script>
// Global modal function for viewing details
function uni_modal(title, url) {
    // Create modal HTML if it doesn't exist
    if($('#uni_modal').length === 0) {
        $('body').append(`
            <div class="modal fade" id="uni_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }
    
    // Set title and load content
    $('#uni_modal .modal-title').html(title);
    $('#uni_modal .modal-body').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>');
    $('#uni_modal').modal('show');
    
    // Load the URL content
    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            $('#uni_modal .modal-body').html(response);
        },
        error: function() {
            $('#uni_modal .modal-body').html('<div class="alert alert-danger">Error loading content</div>');
        }
    });
}
</script>
</body>
</html>