<?php
include 'db_connect.php';

// Get document ID from URL
$doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($doc_id <= 0){
    echo '<div class="alert alert-danger">Invalid document ID</div>';
    return;
}

// Fetch document
$qry = $conn->query("SELECT * FROM documents WHERE id = $doc_id");

if(!$qry || $qry->num_rows == 0){
    echo '<div class="alert alert-danger">Document not found</div>';
    return;
}

$doc = $qry->fetch_assoc();

// Get creator info
$creator_name = 'System';
if(isset($doc['user_id']) && $doc['user_id'] > 0){
    $user_qry = $conn->query("SELECT firstname, lastname FROM users WHERE id = {$doc['user_id']}");
    if($user_qry && $user_qry->num_rows > 0){
        $creator = $user_qry->fetch_assoc();
        $creator_name = $creator['firstname'] . ' ' . $creator['lastname'];
    }
}
?>

<style>
    .file-item {
        padding: 10px 12px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.2s;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .file-item:hover {
        background: #e9ecef;
    }
    .file-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 200px;
    }
    .file-name-display {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        background: white;
        padding: 4px 10px;
        border-radius: 20px;
        color: #2c3e50;
        word-break: break-all;
    }
    .btn-view-doc {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 5px 12px;
        border-radius: 20px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #0d6efd;
        font-weight: 500;
    }
    .btn-view-doc:hover {
        background: #0d6efd;
        color: white;
    }
    .btn-view-doc:hover i {
        color: white;
    }
    .btn-back {
        margin-bottom: 15px;
    }
    .category-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-top: 15px;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 2px solid #dee2e6;
    }
    .badge-count {
        background: #6c757d;
        color: white;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.7rem;
    }
    
    /* Full width files section at bottom */
    .files-full-width {
        margin-top: 20px;
        width: 100%;
    }
    
    /* Make all cards equal height */
    .card {
        height: 100%;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid">
    <!-- Back and Edit Buttons -->
    <div class="btn-back">
        <a href="index.php?page=document_list" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Document List
        </a>
        <a href="index.php?page=edit_document&id=<?php echo $doc_id; ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> Edit Document
        </a>
    </div>

    <!-- Document Header -->
    <div class="alert alert-info mb-3">
        <h5 class="mb-1"><?php echo htmlspecialchars($doc['title']); ?></h5>
        <small>Created by: <?php echo htmlspecialchars($creator_name); ?> | Date: <?php echo date('d M Y', strtotime($doc['date_created'])); ?></small>
    </div>
    
    <!-- ROW 1: BUSINESS INFO | OWNER DETAILS -->
    <div class="row">
        <!-- BUSINESS INFORMATION -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-building"></i> Business Information
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="35%">Company Name</th>
                            <td><?php echo htmlspecialchars($doc['title'] ?? '-'); ?></th>
                        </tr>
                        <tr>
                            <th>SSM Registration No.</th>
                            <td><?php echo htmlspecialchars($doc['business_reg_no'] ?? '-'); ?></th>
                        </tr>
                        <tr>
                            <th>Business Type</th>
                            <td><?php echo ucfirst(htmlspecialchars($doc['business_type'] ?? '-')); ?></th>
                        </tr>
                        <tr>
                            <th>Business Address</th>
                            <td><?php echo nl2br(htmlspecialchars($doc['business_address'] ?? '-')); ?></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- OWNER DETAILS -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-user"></i> Owner Details
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <?php 
                $owner_count = $doc['owner_count'] ?? 1;
                $owners_data = !empty($doc['owners_data']) ? json_decode($doc['owners_data'], true) : [];
                
                // For backward compatibility - if no owners_data, use old single owner fields
                if(empty($owners_data) && !empty($doc['owner_name'])) {
                    $owners_data = [[
                        'name' => $doc['owner_name'] ?? '-',
                        'ic' => $doc['owner_ic'] ?? '-',
                        'email' => $doc['owner_email'] ?? '-',
                        'phone' => $doc['owner_phone'] ?? '-'
                    ]];
                    $owner_count = 1;
                }
                ?>
                <tr>
                    <th width="35%">Number of Owners</th>
                    <td><?php echo $owner_count; ?></td>
                </tr>
                <tr>
                    <th>Owner(s) Details</th>
                    <td>
                        <?php if(!empty($owners_data) && is_array($owners_data)): ?>
                            <?php foreach($owners_data as $index => $owner): ?>
                                <div class="mb-2 <?php echo $index > 0 ? 'mt-3 pt-2 border-top' : ''; ?>">
                                    <strong><?php echo htmlspecialchars($owner['name'] ?? '-'); ?></strong><br>
                                    <small>IC: <?php echo htmlspecialchars($owner['ic'] ?? '-'); ?></small><br>
                                    <?php if(!empty($owner['email'])): ?>
                                        <small>Email: <?php echo htmlspecialchars($owner['email']); ?></small><br>
                                    <?php endif; ?>
                                    <?php if(!empty($owner['phone'])): ?>
                                        <small>Phone: <?php echo htmlspecialchars($owner['phone']); ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
    </div>
    
    <!-- ROW 2: FAMILY DETAILS | FINANCIAL INFO -->
    <div class="row">
        <!-- FAMILY DETAILS -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-heart"></i> Family Details
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <?php if(!empty($doc['spouse_data'])): 
                            $spouses = json_decode($doc['spouse_data'], true);
                        ?>
                        <tr>
                            <th width="35%">Spouse</th>
                            <td>
                                <?php foreach($spouses as $spouse): ?>
                                    <strong><?php echo htmlspecialchars($spouse['name'] ?? '-'); ?></strong><br>
                                    <small>IC: <?php echo htmlspecialchars($spouse['ic'] ?? '-'); ?></small><br>
                                <?php endforeach; ?>
                            </th>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <th>Spouse</th>
                            <td>-</th>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if(!empty($doc['children_data'])): 
                            $children = json_decode($doc['children_data'], true);
                        ?>
                        <tr>
                            <th>Children</th>
                            <td>
                                <?php foreach($children as $child): ?>
                                    <strong><?php echo htmlspecialchars($child['name'] ?? '-'); ?></strong><br>
                                    <small>IC: <?php echo htmlspecialchars($child['ic'] ?? '-'); ?></small><br>
                                <?php endforeach; ?>
                            </th>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <th>Children</th>
                            <td>-</th>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- FINANCIAL INFORMATION -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-line"></i> Financial Information
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="35%">Total Sales (RM)</th>
                            <td><?php echo !empty($doc['total_sales']) ? number_format($doc['total_sales'], 2) : '-'; ?></th>
                        </tr>
                        <tr>
                            <th>Net Profit (RM)</th>
                            <td><?php echo !empty($doc['net_profit']) ? number_format($doc['net_profit'], 2) : '-'; ?></th>
                        </tr>
                        <tr>
                            <th>Capital (RM)</th>
                            <td><?php echo !empty($doc['capital']) ? number_format($doc['capital'], 2) : '-'; ?></th>
                        </tr>
                        <tr>
                            <th>Assets (RM)</th>
                            <td><?php echo !empty($doc['assets']) ? number_format($doc['assets'], 2) : '-'; ?></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ROW 3: ADDITIONAL INFO | PAYMENT INFO -->
    <div class="row">
        <!-- ADDITIONAL INFORMATION -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle"></i> Additional Information
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="35%">Status</th>
                            <td>
                                <?php
                                $status_class = 'secondary';
                                if($doc['status'] == 'completed') $status_class = 'success';
                                elseif($doc['status'] == 'pending') $status_class = 'warning';
                                elseif($doc['status'] == 'incomplete') $status_class = 'danger';
                                ?>
                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($doc['status'] ?? 'Pending'); ?></span>
                            </th>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td><?php echo nl2br(htmlspecialchars($doc['description'] ?? '-')); ?></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- PAYMENT INFORMATION -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-money-bill"></i> Payment Information
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="40%">Payment Received</th>
                            <td>RM <?php echo !empty($doc['payment_received']) ? number_format($doc['payment_received'], 2) : '0.00'; ?></th>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td>RM <?php echo !empty($doc['payment_total']) ? number_format($doc['payment_total'], 2) : '0.00'; ?></th>
                        </tr>
                        <tr>
                            <th>Deposit</th>
                            <td>RM <?php echo !empty($doc['payment_deposit']) ? number_format($doc['payment_deposit'], 2) : '0.00'; ?></th>
                        </tr>
                        <tr>
                            <th>Balance</th>
                            <td>RM <?php echo !empty($doc['payment_balance']) ? number_format($doc['payment_balance'], 2) : '0.00'; ?></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ATTACHED FILES SECTION - FULL WIDTH AT BOTTOM -->
    <div class="files-full-width">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-paperclip"></i> Attached Files
            </div>
            <div class="card-body">
                <?php
                $file_fields = ['ssm_files', 'tax_files', 'bank_files', 'financial_files', 'other_files'];
                $file_labels = ['SSM Certificate', 'Tax Documents', 'Bank Statements', 'Financial Statements', 'Other Documents'];
                $file_icons = ['fa-certificate', 'fa-file-invoice-dollar', 'fa-university', 'fa-chart-pie', 'fa-folder-open'];
                $has_files = false;
                
                for($i = 0; $i < count($file_fields); $i++){
                    $field = $file_fields[$i];
                    $label = $file_labels[$i];
                    $icon = $file_icons[$i];
                    $files = [];
                    
                    if(isset($doc[$field]) && !empty($doc[$field]) && $doc[$field] != '[]' && $doc[$field] != 'null'){
                        $files = json_decode($doc[$field], true);
                        if(is_array($files) && count($files) > 0){
                            $has_files = true;
                            ?>
                            <div class="category-header">
                                <h6 class="mt-2 mb-2">
                                    <i class="fas <?php echo $icon; ?>"></i> <?php echo $label; ?>
                                </h6>
                                <span class="badge-count"><?php echo count($files); ?> files</span>
                            </div>
                            <?php
                            foreach($files as $file){
                                if(!empty($file)){
                                    // Handle both old format (string) and new format (array with original/generated)
                                    if(is_array($file)) {
                                        $display_name = $file['original'];
                                        $file_path = $file['generated'];
                                    } else {
                                        // For old files, clean the filename for display
                                        $display_name = preg_replace('/^(ssm|tax|bank|financial|other)_\d+_\d+_/', '', $file);
                                        $display_name = preg_replace('/^\d+_/', '', $display_name);
                                        $file_path = $file;
                                    }
                                    ?>
                                    <div class="file-item">
                                        <div class="file-info">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                            <span class="file-name-display">
                                                <?php echo htmlspecialchars($display_name); ?>
                                            </span>
                                        </div>
                                        <button class="btn-view-doc" onclick="viewDocument('<?php echo htmlspecialchars($file_path); ?>')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
                
                if(!$has_files){
                    echo '<p class="text-muted text-center">No files attached</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
function viewDocument(filename) {
    const fileExt = filename.split('.').pop().toLowerCase();
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);
    const isPDF = fileExt === 'pdf';
    const isDoc = ['doc', 'docx'].includes(fileExt);
    const isXls = ['xls', 'xlsx'].includes(fileExt);
    
    let content = '';
    let displayFilename = filename.split('/').pop();
    
    if (isImage) {
        content = `<img src="assets/uploads/${filename}" style="max-width: 100%; max-height: 80vh; object-fit: contain;">`;
    } else if (isPDF) {
        content = `<iframe src="assets/uploads/${filename}" style="width: 100%; height: 80vh; border: none;"></iframe>`;
    } else if (isDoc || isXls) {
        const fileUrl = `assets/uploads/${filename}`;
        const encodedUrl = encodeURIComponent(window.location.origin + '/' + fileUrl);
        content = `
            <iframe src="https://docs.google.com/gview?url=${encodedUrl}&embedded=true" 
                    style="width: 100%; height: 80vh; border: none;">
            </iframe>
            <div class="mt-2 text-center">
                <small>If preview doesn't load, <a href="${fileUrl}" target="_blank">click here to open</a></small>
            </div>
        `;
    } else {
        content = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-file-alt" style="font-size: 64px; color: #1a56db;"></i>
                <h4 class="mt-3">${displayFilename}</h4>
                <p class="text-muted">Preview not available for this file type</p>
                <a href="assets/uploads/${filename}" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>
        `;
    }
    
    Swal.fire({
        title: displayFilename,
        html: content,
        showCloseButton: true,
        showConfirmButton: false,
        width: '90%',
        customClass: {
            popup: 'preview-swal'
        }
    });
}
</script>

<style>
.preview-swal {
    max-width: 90%;
    padding: 0;
}
.swal2-html-container {
    margin: 0;
    padding: 0;
}
.file-name-display {
    word-break: break-all;
    max-width: 300px;
}
@media (max-width: 768px) {
    .file-item {
        flex-direction: column;
        align-items: flex-start;
    }
    .file-name-display {
        max-width: 100%;
    }
}
</style>