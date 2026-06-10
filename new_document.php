<?php
// ============================================
// INCREASE FILE UPLOAD LIMIT TO 100 FILES
// ============================================
ini_set('max_file_uploads', '100');
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '500M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');

include 'db_connect.php';

$is_edit = false;
$doc = array();
if(isset($_GET['id']) && !empty($_GET['id'])){
    $is_edit = true;
    $qry = $conn->query("SELECT * FROM documents WHERE id = " . (int)$_GET['id']);
    if($qry && $qry->num_rows > 0){
        $doc = $qry->fetch_assoc();
    }
}
?>

<style>
    .form-step { display: none; }
    .form-step.active { display: block; }
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 15px;
        flex-wrap: wrap;
    }
    .step-item {
        text-align: center;
        flex: 1;
        min-width: 80px;
        cursor: pointer;
    }
    .step-number {
        width: 40px;
        height: 40px;
        background: #ccc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
    }
    .step-item.active .step-number {
        background: #2563eb;
        color: white;
    }
    .step-item.completed .step-number {
        background: #10b981;
        color: white;
    }
    .step-label {
        font-size: 11px;
        color: #666;
    }
    .step-item.active .step-label {
        color: #2563eb;
        font-weight: bold;
    }
    .nav-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #ddd;
    }
    .section-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        margin-bottom: 25px;
        overflow: hidden;
    }
    .section-header {
        background: #f3f4f6;
        padding: 12px 20px;
        font-weight: bold;
        border-bottom: 1px solid #ddd;
    }
    .section-body {
        padding: 20px;
    }
    .review-table {
        width: 100%;
        border-collapse: collapse;
    }
    .review-table td, .review-table th {
        padding: 10px;
        border: 1px solid #ddd;
    }
    .review-table td:first-child {
        background: #f9fafb;
        font-weight: 600;
        width: 35%;
    }
    .file-item {
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 8px;
        border-left: 3px solid #1a56db;
    }

/* Upload Document Layout */
.upload-category {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 15px;
    background: #f9fafb;
}

.upload-category .fw-bold {
    display: block;
    margin-bottom: 10px;
    color: #374151;
    font-size: 14px;
}

.file-list-container {
    min-height: 40px;
    margin-top: 8px;
}

.selected-file-item {
    background: white;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.selected-file-item .file-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.selected-file-item .file-name {
    font-size: 13px;
    color: #374151;
}

.selected-file-item .file-size {
    font-size: 11px;
    color: #6c757d;
    margin-left: 8px;
}

.selected-file-item .remove-file-btn {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 14px;
}

.selected-file-item .remove-file-btn:hover {
    color: #a71d2a;
}

.add-file-btn {
    font-size: 13px;
}

/* Make both remove buttons red */
.remove-existing-file-btn {
    background: none;
    border: none;
    color: #dc3545 !important;
    cursor: pointer;
    font-size: 14px;
}

.remove-existing-file-btn:hover {
    color: #a71d2a !important;
}

/* Owner Card Styles */
.owner-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
}

.owner-card h6 {
    color: #2563eb;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #dee2e6;
}

</style>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $is_edit ? 'Edit Document' : 'New Document'; ?></h5>
    </div>
    <div class="card-body">
        <!-- Step Indicators -->
        <div class="step-indicator">
            <div class="step-item" id="step1-indicator" onclick="goToStep(1)">
                <div class="step-number">1</div>
                <div class="step-label">Business</div>
            </div>
            <div class="step-item" id="step2-indicator" onclick="goToStep(2)">
                <div class="step-number">2</div>
                <div class="step-label">Owner</div>
            </div>
            <div class="step-item" id="step3-indicator" onclick="goToStep(3)">
                <div class="step-number">3</div>
                <div class="step-label">Family</div>
            </div>
            <div class="step-item" id="step4-indicator" onclick="goToStep(4)">
                <div class="step-number">4</div>
                <div class="step-label">Financial</div>
            </div>
            <div class="step-item" id="step5-indicator" onclick="goToStep(5)">
                <div class="step-number">5</div>
                <div class="step-label">Documents</div>
            </div>
            <div class="step-item" id="step6-indicator" onclick="goToStep(6)">
                <div class="step-number">6</div>
                <div class="step-label">Review</div>
            </div>
        </div>
        
        <form id="documentForm" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $is_edit ? $doc['id'] : ''; ?>">
            
            <!-- STEP 1: Business Information -->
            <div class="form-step" id="step1">
                <div class="section-card">
                    <div class="section-header"><i class="fas fa-building"></i> Business Information</div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Company / Business Name <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($doc['title'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>SSM Registration No.</label>
                                <input type="text" name="business_reg_no" class="form-control" value="<?php echo htmlspecialchars($doc['business_reg_no'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Business Type</label>
                                <select name="business_type" id="business_type" class="form-control">
                                    <option value="sole" <?php echo ((isset($doc['business_type']) && $doc['business_type'] == 'sole') ? 'selected' : ''); ?>>Sole Proprietorship</option>
                                    <option value="partnership" <?php echo ((isset($doc['business_type']) && $doc['business_type'] == 'partnership') ? 'selected' : ''); ?>>Partnership</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Business Address</label>
                                <textarea name="business_address" class="form-control" rows="2"><?php echo htmlspecialchars($doc['business_address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- STEP 2: Owner Details - MULTIPLE OWNERS -->
            <div class="form-step" id="step2">
                <div class="section-card">
                    <div class="section-header"><i class="fas fa-user"></i> Owner Details</div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Number of Owners</label>
                                <input type="number" name="owner_count" id="owner_count" class="form-control" value="<?php echo $doc['owner_count'] ?? 1; ?>" min="1" max="10">
                                <small class="text-muted">For Partnership, you can add up to 10 owners</small>
                            </div>
                        </div>
                        <div id="owners-container" class="mt-3"></div>
                    </div>
                </div>
            </div>
            
            <!-- STEP 3: Family Details -->
            <div class="form-step" id="step3">
                <div class="section-card">
                    <div class="section-header"><i class="fas fa-heart"></i> Family Details</div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Marital Status</label>
                                <select name="marital_status" class="form-control">
                                    <option value="single" <?php echo ((isset($doc['marital_status']) && $doc['marital_status'] == 'single') ? 'selected' : ''); ?>>Single</option>
                                    <option value="married" <?php echo ((isset($doc['marital_status']) && $doc['marital_status'] == 'married') ? 'selected' : ''); ?>>Married</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Marriage Date</label>
                                <input type="date" name="marriage_date" class="form-control" value="<?php echo $doc['marriage_date'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label>Number of Spouse</label>
                            <input type="number" name="spouse_count" id="spouse_count" class="form-control" value="<?php echo $doc['spouse_count'] ?? 0; ?>" min="0">
                        </div>
                        <div id="spouse-container" class="mt-2"></div>
                        
                        <div class="mt-3">
                            <label>Number of Children</label>
                            <input type="number" name="child_count" id="child_count" class="form-control" value="<?php echo $doc['child_count'] ?? 0; ?>" min="0">
                        </div>
                        <div id="children-container" class="mt-2"></div>
                    </div>
                </div>
            </div>
            
            <!-- STEP 4: Financial Info -->
            <div class="form-step" id="step4">
                <div class="section-card">
                    <div class="section-header"><i class="fas fa-chart-line"></i> Financial Information</div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Total Sales (RM)</label>
                                <input type="text" name="total_sales" class="form-control" value="<?php echo htmlspecialchars($doc['total_sales'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Net Profit (RM)</label>
                                <input type="text" name="net_profit" class="form-control" value="<?php echo htmlspecialchars($doc['net_profit'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Capital (RM)</label>
                                <input type="text" name="capital" class="form-control" value="<?php echo htmlspecialchars($doc['capital'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Assets (RM)</label>
                                <input type="text" name="assets" class="form-control" value="<?php echo htmlspecialchars($doc['assets'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
<!-- STEP 5: Upload Documents -->
<div class="form-step" id="step5">
    <div class="section-card">
        <div class="section-header"><i class="fas fa-cloud-upload-alt"></i> Upload Documents</div>
        <div class="section-body">
            
            <!-- 1. SSM Certificate -->
            <div class="upload-category mb-4">
                <label class="fw-bold">1. SSM Certificate</label>
                <div id="ssm-file-list" class="file-list-container"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-file-btn mt-2" data-category="ssm">
                    <i class="fas fa-plus"></i> Add File
                </button>
                <input type="file" name="ssm_files[]" class="d-none file-input" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                <small class="text-muted d-block mt-1">Supported: JPG, PNG, PDF, DOC, DOCX (Max 50MB each)</small>
            </div>
            
            <!-- 2. Tax Documents -->
            <div class="upload-category mb-4">
                <label class="fw-bold">2. Tax Documents</label>
                <div id="tax-file-list" class="file-list-container"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-file-btn mt-2" data-category="tax">
                    <i class="fas fa-plus"></i> Add File
                </button>
                <input type="file" name="tax_files[]" class="d-none file-input" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                <small class="text-muted d-block mt-1">Supported: JPG, PNG, PDF, DOC, DOCX (Max 50MB each)</small>
            </div>
            
            <!-- 3. Bank Statements -->
            <div class="upload-category mb-4">
                <label class="fw-bold">3. Bank Statements</label>
                <div id="bank-file-list" class="file-list-container"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-file-btn mt-2" data-category="bank">
                    <i class="fas fa-plus"></i> Add File
                </button>
                <input type="file" name="bank_files[]" class="d-none file-input" multiple accept=".jpg,.jpeg,.png,.pdf">
                <small class="text-muted d-block mt-1">Supported: JPG, PNG, PDF (Max 50MB each)</small>
            </div>
            
            <!-- 4. Financial Statements -->
            <div class="upload-category mb-4">
                <label class="fw-bold">4. Financial Statements</label>
                <div id="financial-file-list" class="file-list-container"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-file-btn mt-2" data-category="financial">
                    <i class="fas fa-plus"></i> Add File
                </button>
                <input type="file" name="financial_files[]" class="d-none file-input" multiple accept=".jpg,.jpeg,.png,.pdf">
                <small class="text-muted d-block mt-1">Supported: JPG, PNG, PDF (Max 50MB each)</small>
            </div>
            
            <!-- 5. Other Documents -->
            <div class="upload-category mb-4">
                <label class="fw-bold">5. Other Documents</label>
                <div id="other-file-list" class="file-list-container"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-file-btn mt-2" data-category="other">
                    <i class="fas fa-plus"></i> Add File
                </button>
                <input type="file" name="other_files[]" class="d-none file-input" multiple accept=".jpg,.jpeg,.png,.pdf">
                <small class="text-muted d-block mt-1">Supported: JPG, PNG, PDF (Max 50MB each)</small>
            </div>
            
        </div>
    </div>
</div>
            
            <!-- STEP 6: Review -->
            <div class="form-step" id="step6">
                <div class="section-card">
                    <div class="section-header"><i class="fas fa-check-circle"></i> Review & Submit</div>
                    <div class="section-body">
                        <div id="review-content" class="mb-3"></div>
                        
                        <div class="card mt-3" style="background:#f0fdf4; border-color:#22c55e;">
                            <div class="card-header" style="background:#22c55e; color:white;">
                                <i class="fas fa-money-bill-wave"></i> Payment Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Payment Received (RM)</label>
                                        <input type="text" name="payment_received" id="payment_received" class="form-control" placeholder="0.00" value="<?php echo htmlspecialchars($doc['payment_received'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Total Amount (RM)</label>
                                        <input type="text" name="payment_total" id="payment_total" class="form-control" placeholder="0.00" value="<?php echo htmlspecialchars($doc['payment_total'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Deposit (RM)</label>
                                        <input type="text" name="payment_deposit" id="payment_deposit" class="form-control" placeholder="0.00" value="<?php echo htmlspecialchars($doc['payment_deposit'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Balance (RM)</label>
                                        <input type="text" name="payment_balance" id="payment_balance" class="form-control" placeholder="0.00" value="<?php echo htmlspecialchars($doc['payment_balance'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="pending" <?php echo ((isset($doc['status']) && $doc['status'] == 'pending') ? 'selected' : ''); ?>>Pending</option>
                                <option value="completed" <?php echo ((isset($doc['status']) && $doc['status'] == 'completed') ? 'selected' : ''); ?>>Completed</option>
                                <option value="incomplete" <?php echo ((isset($doc['status']) && $doc['status'] == 'incomplete') ? 'selected' : ''); ?>>Incomplete</option>
                            </select>
                        </div>
                        <div class="mt-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($doc['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="nav-buttons">
                <button type="button" class="btn btn-secondary" id="prevBtn">← Back</button>
                <button type="button" class="btn btn-primary" id="nextBtn">Next →</button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display:none">✓ Submit</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentStep = 1;
const totalSteps = 6;

// Store existing spouse and children data from database (for edit mode)
let existingSpouses = [];
let existingChildren = [];
let existingOwners = [];

<?php if($is_edit && !empty($doc['spouse_data'])): ?>
    existingSpouses = <?php echo $doc['spouse_data']; ?>;
<?php endif; ?>

<?php if($is_edit && !empty($doc['children_data'])): ?>
    existingChildren = <?php echo $doc['children_data']; ?>;
<?php endif; ?>

<?php if($is_edit && !empty($doc['owners_data'])): ?>
    existingOwners = <?php echo $doc['owners_data']; ?>;
<?php endif; ?>

// Store files for each category
let ssmFiles = [];
let taxFiles = [];
let bankFiles = [];
let financialFiles = [];
let otherFiles = [];

// Store owners data
let ownersData = [];

$(document).ready(function() {
    showStep(1);
    
    // Helper function to escape HTML
    function escapeHtml(str) {
        if(!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function updateFileListDisplay(category, $fileListContainer) {
        let files = [];
        switch(category) {
            case 'ssm': files = ssmFiles; break;
            case 'tax': files = taxFiles; break;
            case 'bank': files = bankFiles; break;
            case 'financial': files = financialFiles; break;
            case 'other': files = otherFiles; break;
        }
        
        $fileListContainer.empty();
        for(let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileHtml = `
                <div class="selected-file-item" data-file-index="${i}">
                    <div class="file-info">
                        <i class="fas fa-file-alt text-secondary"></i>
                        <span class="file-name">${escapeHtml(file.name)}</span>
                        <span class="file-size">(${formatFileSize(file.size)})</span>
                    </div>
                    <button type="button" class="remove-file-btn" title="Remove" data-category="${category}" data-index="${i}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            $fileListContainer.append(fileHtml);
        }
    }
    
    // Handle Add File button clicks
    $('.add-file-btn').on('click', function() {
        const category = $(this).data('category');
        const $fileInput = $(this).siblings('.file-input');
        $fileInput.click();
        
        $fileInput.off('change').on('change', function() {
            const files = Array.from(this.files);
            
            switch(category) {
                case 'ssm':
                    ssmFiles = ssmFiles.concat(files);
                    updateFileListDisplay('ssm', $('#ssm-file-list'));
                    break;
                case 'tax':
                    taxFiles = taxFiles.concat(files);
                    updateFileListDisplay('tax', $('#tax-file-list'));
                    break;
                case 'bank':
                    bankFiles = bankFiles.concat(files);
                    updateFileListDisplay('bank', $('#bank-file-list'));
                    break;
                case 'financial':
                    financialFiles = financialFiles.concat(files);
                    updateFileListDisplay('financial', $('#financial-file-list'));
                    break;
                case 'other':
                    otherFiles = otherFiles.concat(files);
                    updateFileListDisplay('other', $('#other-file-list'));
                    break;
            }
            this.value = '';
        });
    });
    
    // Remove file handler
    $(document).on('click', '.remove-file-btn', function() {
        const category = $(this).data('category');
        const index = parseInt($(this).data('index'));
        
        switch(category) {
            case 'ssm':
                ssmFiles.splice(index, 1);
                updateFileListDisplay('ssm', $('#ssm-file-list'));
                break;
            case 'tax':
                taxFiles.splice(index, 1);
                updateFileListDisplay('tax', $('#tax-file-list'));
                break;
            case 'bank':
                bankFiles.splice(index, 1);
                updateFileListDisplay('bank', $('#bank-file-list'));
                break;
            case 'financial':
                financialFiles.splice(index, 1);
                updateFileListDisplay('financial', $('#financial-file-list'));
                break;
            case 'other':
                otherFiles.splice(index, 1);
                updateFileListDisplay('other', $('#other-file-list'));
                break;
        }
    });
    
    // ============================================
    // OWNERS FIELDS FUNCTIONS
    // ============================================
    function initOwnersFields() {
        let count = parseInt($('#owner_count').val()) || 1;
        let html = '';
        
        for(let i = 1; i <= count; i++) {
            let ownerName = '';
            let ownerIc = '';
            let ownerEmail = '';
            let ownerPhone = '';
            
            if(existingOwners[i-1]) {
                ownerName = existingOwners[i-1].name || '';
                ownerIc = existingOwners[i-1].ic || '';
                ownerEmail = existingOwners[i-1].email || '';
                ownerPhone = existingOwners[i-1].phone || '';
            }
            
            html += `
                <div class="owner-card">
                    <h6>Owner ${i}</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="owner_name_${i}" class="form-control owner-name" value="${escapeHtml(ownerName)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>IC Number <span class="text-danger">*</span></label>
                            <input type="text" name="owner_ic_${i}" class="form-control owner-ic" value="${escapeHtml(ownerIc)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="owner_email_${i}" class="form-control owner-email" value="${escapeHtml(ownerEmail)}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" name="owner_phone_${i}" class="form-control owner-phone" value="${escapeHtml(ownerPhone)}">
                        </div>
                    </div>
                </div>
            `;
        }
        
        $('#owners-container').html(html);
        
        // Store initial owners data
        updateOwnersData();
    }
    
    function updateOwnersData() {
        let count = parseInt($('#owner_count').val()) || 1;
        ownersData = [];
        
        for(let i = 1; i <= count; i++) {
            let name = $(`input[name="owner_name_${i}"]`).val();
            let ic = $(`input[name="owner_ic_${i}"]`).val();
            let email = $(`input[name="owner_email_${i}"]`).val();
            let phone = $(`input[name="owner_phone_${i}"]`).val();
            
            if(name) {
                ownersData.push({
                    name: name,
                    ic: ic || '',
                    email: email || '',
                    phone: phone || ''
                });
            }
        }
    }
    
    // Owner count change handler
    $('#owner_count').on('change keyup', function() {
        initOwnersFields();
    });
    
    // Update owners data when inputs change
    $(document).on('change keyup', '.owner-name, .owner-ic, .owner-email, .owner-phone', function() {
        updateOwnersData();
    });
    
    // ============================================
    // LOAD EXISTING FILES WHEN EDITING
    // ============================================
    <?php if($is_edit): ?>
        
        // Load Owners data
        <?php if(!empty($doc['owners_data'])): ?>
            var ownersExisting = <?php echo $doc['owners_data']; ?>;
            if(ownersExisting.length > 0) {
                $('#owner_count').val(ownersExisting.length);
                initOwnersFields();
            }
        <?php endif; ?>
        
        // Load SSM Files
        <?php if(!empty($doc['ssm_files'])): ?>
            var ssmExisting = <?php echo $doc['ssm_files']; ?>;
            if(ssmExisting.length > 0) {
                for(var i = 0; i < ssmExisting.length; i++) {
                    var fileData = ssmExisting[i];
                    var displayName = fileData;
                    var actualFileName = fileData;
                    
                    if(typeof fileData === 'object' && fileData !== null) {
                        displayName = fileData.original;
                        actualFileName = fileData.generated;
                    }
                    
                    var fileHtml = `
                        <div class="selected-file-item existing-file" data-file-name="${actualFileName}">
                            <div class="file-info">
                                <i class="fas fa-file-alt text-secondary"></i>
                                <span class="file-name">${escapeHtml(displayName)}</span>
                                <span class="file-size">(Existing file)</span>
                            </div>
                            <button type="button" class="remove-existing-file-btn" title="Remove" data-filename="${actualFileName}" data-category="ssm">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#ssm-file-list').append(fileHtml);
                }
            }
        <?php endif; ?>
        
        // Load Tax Files
        <?php if(!empty($doc['tax_files'])): ?>
            var taxExisting = <?php echo $doc['tax_files']; ?>;
            if(taxExisting.length > 0) {
                for(var i = 0; i < taxExisting.length; i++) {
                    var fileData = taxExisting[i];
                    var displayName = fileData;
                    var actualFileName = fileData;
                    
                    if(typeof fileData === 'object' && fileData !== null) {
                        displayName = fileData.original;
                        actualFileName = fileData.generated;
                    }
                    
                    var fileHtml = `
                        <div class="selected-file-item existing-file" data-file-name="${actualFileName}">
                            <div class="file-info">
                                <i class="fas fa-file-alt text-secondary"></i>
                                <span class="file-name">${escapeHtml(displayName)}</span>
                                <span class="file-size">(Existing file)</span>
                            </div>
                            <button type="button" class="remove-existing-file-btn" title="Remove" data-filename="${actualFileName}" data-category="tax">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#tax-file-list').append(fileHtml);
                }
            }
        <?php endif; ?>
        
        // Load Bank Files
        <?php if(!empty($doc['bank_files'])): ?>
            var bankExisting = <?php echo $doc['bank_files']; ?>;
            if(bankExisting.length > 0) {
                for(var i = 0; i < bankExisting.length; i++) {
                    var fileData = bankExisting[i];
                    var displayName = fileData;
                    var actualFileName = fileData;
                    
                    if(typeof fileData === 'object' && fileData !== null) {
                        displayName = fileData.original;
                        actualFileName = fileData.generated;
                    }
                    
                    var fileHtml = `
                        <div class="selected-file-item existing-file" data-file-name="${actualFileName}">
                            <div class="file-info">
                                <i class="fas fa-file-alt text-secondary"></i>
                                <span class="file-name">${escapeHtml(displayName)}</span>
                                <span class="file-size">(Existing file)</span>
                            </div>
                            <button type="button" class="remove-existing-file-btn" title="Remove" data-filename="${actualFileName}" data-category="bank">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#bank-file-list').append(fileHtml);
                }
            }
        <?php endif; ?>
        
        // Load Financial Files
        <?php if(!empty($doc['financial_files'])): ?>
            var financialExisting = <?php echo $doc['financial_files']; ?>;
            if(financialExisting.length > 0) {
                for(var i = 0; i < financialExisting.length; i++) {
                    var fileData = financialExisting[i];
                    var displayName = fileData;
                    var actualFileName = fileData;
                    
                    if(typeof fileData === 'object' && fileData !== null) {
                        displayName = fileData.original;
                        actualFileName = fileData.generated;
                    }
                    
                    var fileHtml = `
                        <div class="selected-file-item existing-file" data-file-name="${actualFileName}">
                            <div class="file-info">
                                <i class="fas fa-file-alt text-secondary"></i>
                                <span class="file-name">${escapeHtml(displayName)}</span>
                                <span class="file-size">(Existing file)</span>
                            </div>
                            <button type="button" class="remove-existing-file-btn" title="Remove" data-filename="${actualFileName}" data-category="financial">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#financial-file-list').append(fileHtml);
                }
            }
        <?php endif; ?>
        
        // Load Other Files
        <?php if(!empty($doc['other_files'])): ?>
            var otherExisting = <?php echo $doc['other_files']; ?>;
            if(otherExisting.length > 0) {
                for(var i = 0; i < otherExisting.length; i++) {
                    var fileData = otherExisting[i];
                    var displayName = fileData;
                    var actualFileName = fileData;
                    
                    if(typeof fileData === 'object' && fileData !== null) {
                        displayName = fileData.original;
                        actualFileName = fileData.generated;
                    }
                    
                    var fileHtml = `
                        <div class="selected-file-item existing-file" data-file-name="${actualFileName}">
                            <div class="file-info">
                                <i class="fas fa-file-alt text-secondary"></i>
                                <span class="file-name">${escapeHtml(displayName)}</span>
                                <span class="file-size">(Existing file)</span>
                            </div>
                            <button type="button" class="remove-existing-file-btn" title="Remove" data-filename="${actualFileName}" data-category="other">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#other-file-list').append(fileHtml);
                }
            }
        <?php endif; ?>
        
        // Handle removing existing files (mark for deletion)
        $(document).on('click', '.remove-existing-file-btn', function() {
            var fileName = $(this).data('filename');
            var category = $(this).data('category');
            
            var $hiddenInput = $('<input>', {
                type: 'hidden',
                name: 'delete_files_' + category + '[]',
                value: fileName
            });
            $(this).closest('.upload-category').append($hiddenInput);
            $(this).closest('.selected-file-item').remove();
            
            Swal.fire({
                icon: 'info',
                title: 'File will be deleted',
                text: 'This file will be removed when you save the document.',
                timer: 2000,
                showConfirmButton: false
            });
        });
        
    <?php endif; ?>
    
    // Initialize owners fields
    initOwnersFields();
    
    // Function to generate spouse fields with existing data
    function initSpouseFields() {
        let count = parseInt($('#spouse_count').val()) || 0;
        let html = '';
        for(let i = 1; i <= count; i++) {
            let spouseName = '';
            let spouseIc = '';
            
            if(existingSpouses[i-1]) {
                spouseName = existingSpouses[i-1].name || '';
                spouseIc = existingSpouses[i-1].ic || '';
            }
            
            html += '<div class="card mb-2 p-3">';
            html += '<h6>Spouse ' + i + '</h6>';
            html += '<div class="row">';
            html += '<div class="col-md-6"><label>Full Name</label><input type="text" name="spouse_name_' + i + '" class="form-control" value="' + escapeHtml(spouseName) + '"></div>';
            html += '<div class="col-md-6"><label>IC Number</label><input type="text" name="spouse_ic_' + i + '" class="form-control" value="' + escapeHtml(spouseIc) + '"></div>';
            html += '</div>';
            html += '</div>';
        }
        $('#spouse-container').html(html);
    }
    
    function initChildrenFields() {
        let count = parseInt($('#child_count').val()) || 0;
        let html = '';
        for(let i = 1; i <= count; i++) {
            let childName = '';
            let childIc = '';
            
            if(existingChildren[i-1]) {
                childName = existingChildren[i-1].name || '';
                childIc = existingChildren[i-1].ic || '';
            }
            
            html += '<div class="card mb-2 p-3">';
            html += '<h6>Child ' + i + '</h6>';
            html += '<div class="row">';
            html += '<div class="col-md-6"><label>Full Name</label><input type="text" name="child_name_' + i + '" class="form-control" value="' + escapeHtml(childName) + '"></div>';
            html += '<div class="col-md-6"><label>IC Number</label><input type="text" name="child_ic_' + i + '" class="form-control" value="' + escapeHtml(childIc) + '"></div>';
            html += '</div>';
            html += '</div>';
        }
        $('#children-container').html(html);
    }
    
    if(existingSpouses.length > 0) {
        $('#spouse_count').val(existingSpouses.length);
    }
    initSpouseFields();
    
    if(existingChildren.length > 0) {
        $('#child_count').val(existingChildren.length);
    }
    initChildrenFields();
    
    $('#spouse_count').on('change keyup', function() {
        initSpouseFields();
    });
    
    $('#child_count').on('change keyup', function() {
        initChildrenFields();
    });
    
    $('#nextBtn').click(function() {
        if(validateStep(currentStep)) {
            if(currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });
    
    $('#prevBtn').click(function() {
        if(currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
});

function showStep(step) {
    $('.form-step').removeClass('active');
    $('#step' + step).addClass('active');
    
    for(let i = 1; i <= totalSteps; i++) {
        if(i < step) {
            $('#step' + i + '-indicator').addClass('completed').removeClass('active');
        } else if(i == step) {
            $('#step' + i + '-indicator').addClass('active').removeClass('completed');
        } else {
            $('#step' + i + '-indicator').removeClass('active completed');
        }
    }
    
    if(step == 1) {
        $('#prevBtn').hide();
    } else {
        $('#prevBtn').show();
    }
    
    if(step == totalSteps) {
        $('#nextBtn').hide();
        $('#submitBtn').show();
        generateReview();
    } else {
        $('#nextBtn').show();
        $('#submitBtn').hide();
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToStep(step) {
    if(step < currentStep) {
        currentStep = step;
        showStep(currentStep);
    } else if(step > currentStep) {
        if(validateStep(currentStep)) {
            currentStep = step;
            showStep(currentStep);
        }
    }
}

function validateStep(step) {
    if(step == 1) {
        if(!$('input[name="title"]').val()) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter company name' });
            return false;
        }
    }
    if(step == 2) {
        let ownerCount = parseInt($('#owner_count').val()) || 1;
        let isValid = true;
        
        for(let i = 1; i <= ownerCount; i++) {
            let name = $(`input[name="owner_name_${i}"]`).val();
            let ic = $(`input[name="owner_ic_${i}"]`).val();
            
            if(!name || !ic) {
                Swal.fire({ icon: 'warning', title: 'Required', text: `Please enter Name and IC for Owner ${i}` });
                isValid = false;
                break;
            }
        }
        return isValid;
    }
    return true;
}

function generateReview() {
    const title = $('input[name="title"]').val() || '-';
    const businessRegNo = $('input[name="business_reg_no"]').val() || '-';
    const businessType = $('select[name="business_type"] option:selected').text() || '-';
    const businessAddress = $('textarea[name="business_address"]').val() || '-';
    const maritalStatus = $('select[name="marital_status"] option:selected').text() || '-';
    const marriageDate = $('input[name="marriage_date"]').val() || '-';
    const totalSales = $('input[name="total_sales"]').val() || '-';
    const netProfit = $('input[name="net_profit"]').val() || '-';
    const capital = $('input[name="capital"]').val() || '-';
    const assets = $('input[name="assets"]').val() || '-';
    const status = $('select[name="status"] option:selected').text() || '-';
    const description = $('textarea[name="description"]').val() || '-';
    const spouseCount = $('#spouse_count').val() || '0';
    const childCount = $('#child_count').val() || '0';
    const paymentReceived = $('#payment_received').val() || '0';
    const paymentTotal = $('#payment_total').val() || '0';
    const paymentDeposit = $('#payment_deposit').val() || '0';
    const paymentBalance = $('#payment_balance').val() || '0';
    
    // Get owners list
    let ownerCount = parseInt($('#owner_count').val()) || 1;
    let ownersHtml = '';
    for(let i = 1; i <= ownerCount; i++) {
        let name = $(`input[name="owner_name_${i}"]`).val();
        let ic = $(`input[name="owner_ic_${i}"]`).val();
        let email = $(`input[name="owner_email_${i}"]`).val();
        let phone = $(`input[name="owner_phone_${i}"]`).val();
        if(name) {
            ownersHtml += `<strong>${escapeHtmlForReview(name)}</strong> (IC: ${escapeHtmlForReview(ic) || '-'})<br>`;
            if(email) ownersHtml += `<small>Email: ${escapeHtmlForReview(email)}</small><br>`;
            if(phone) ownersHtml += `<small>Phone: ${escapeHtmlForReview(phone)}</small><br>`;
            if(i < ownerCount) ownersHtml += '<hr class="my-1">';
        }
    }
    
    let spouseList = '';
    for(let i = 1; i <= spouseCount; i++) {
        let name = $('input[name="spouse_name_' + i + '"]').val();
        let ic = $('input[name="spouse_ic_' + i + '"]').val();
        if(name) spouseList += `<strong>${escapeHtmlForReview(name)}</strong> (IC: ${escapeHtmlForReview(ic) || '-'})<br>`;
    }
    
    let childList = '';
    for(let i = 1; i <= childCount; i++) {
        let name = $('input[name="child_name_' + i + '"]').val();
        let ic = $('input[name="child_ic_' + i + '"]').val();
        if(name) childList += `<strong>${escapeHtmlForReview(name)}</strong> (IC: ${escapeHtmlForReview(ic) || '-'})<br>`;
    }
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold text-primary">Business Information</h6>
                <table class="table table-bordered table-sm">
                    <tr><th width="40%">Company Name</th><td>${escapeHtmlForReview(title)}</th></tr>
                    <tr><th>SSM Registration No.</th><td>${escapeHtmlForReview(businessRegNo)}</th></tr>
                    <tr><th>Business Type</th><td>${escapeHtmlForReview(businessType)}</th></tr>
                    <tr><th>Business Address</th><td>${escapeHtmlForReview(businessAddress)}</th></tr>
                </table>
                
                <h6 class="fw-bold text-primary mt-3">Owner Details</h6>
                <table class="table table-bordered table-sm">
                    <tr><th width="40%">Number of Owners</th><td>${ownerCount}</th></tr>
                    <tr><th>Owner(s) Details</th><td>${ownersHtml || '-'}</th></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-primary">Family Details</h6>
                <table class="table table-bordered table-sm">
                    <tr><th width="40%">Marital Status</th><td>${escapeHtmlForReview(maritalStatus)}</th></tr>
                    <tr><th>Marriage Date</th><td>${escapeHtmlForReview(marriageDate)}</th></tr>
                    <tr><th>Number of Spouse</th><td>${escapeHtmlForReview(spouseCount)}</th></tr>
                    <tr><th>Spouse Details</th><td>${spouseList || '-'}</th></tr>
                    <tr><th>Number of Children</th><td>${escapeHtmlForReview(childCount)}</th></tr>
                    <tr><th>Children Details</th><td>${childList || '-'}</th></tr>
                </table>
                
                <h6 class="fw-bold text-primary mt-3">Financial Information</h6>
                <table class="table table-bordered table-sm">
                    <tr><th width="40%">Total Sales (RM)</th><td>${escapeHtmlForReview(totalSales)}</th></tr>
                    <tr><th>Net Profit (RM)</th><td>${escapeHtmlForReview(netProfit)}</th></tr>
                    <tr><th>Capital (RM)</th><td>${escapeHtmlForReview(capital)}</th></tr>
                    <tr><th>Assets (RM)</th><td>${escapeHtmlForReview(assets)}</th></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <h6 class="fw-bold text-success">Payment Information</h6>
                <table class="table table-bordered table-sm">
                    <tr><th width="25%">Payment Received</th><td>RM ${escapeHtmlForReview(paymentReceived)}</th></tr>
                    <tr><th>Total Amount</th><td>RM ${escapeHtmlForReview(paymentTotal)}</th></tr>
                    <tr><th>Deposit</th><td>RM ${escapeHtmlForReview(paymentDeposit)}</th></td>
                    <tr><th>Balance</th><td>RM ${escapeHtmlForReview(paymentBalance)}</th></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <h6 class="fw-bold text-primary">Additional Information</h6>
                <table class="table table-bordered table-sm">
                    <tr><th width="25%">Status</th><td>${escapeHtmlForReview(status)}</th></tr>
                    <tr><th>Description</th><td>${escapeHtmlForReview(description)}</th></tr>
                </table>
            </div>
        </div>
    `;
    
    $('#review-content').html(html);
}

function escapeHtmlForReview(str) {
    if(!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

// Form submit - with owners data
$('#documentForm').submit(function(e) {
    e.preventDefault();
    
    let formData = new FormData();
    
    // Add all regular form fields
    formData.append('id', $('input[name="id"]').val());
    formData.append('title', $('input[name="title"]').val());
    formData.append('business_reg_no', $('input[name="business_reg_no"]').val());
    formData.append('business_type', $('select[name="business_type"]').val());
    formData.append('business_address', $('textarea[name="business_address"]').val());
    formData.append('marital_status', $('select[name="marital_status"]').val());
    formData.append('marriage_date', $('input[name="marriage_date"]').val());
    formData.append('spouse_count', $('#spouse_count').val());
    formData.append('child_count', $('#child_count').val());
    formData.append('total_sales', $('input[name="total_sales"]').val());
    formData.append('net_profit', $('input[name="net_profit"]').val());
    formData.append('capital', $('input[name="capital"]').val());
    formData.append('assets', $('input[name="assets"]').val());
    formData.append('status', $('select[name="status"]').val());
    formData.append('description', $('textarea[name="description"]').val());
    formData.append('payment_received', $('#payment_received').val());
    formData.append('payment_total', $('#payment_total').val());
    formData.append('payment_deposit', $('#payment_deposit').val());
    formData.append('payment_balance', $('#payment_balance').val());
    formData.append('owner_count', $('#owner_count').val());
    
    // Add owners data
    let ownerCount = parseInt($('#owner_count').val()) || 1;
    let owners = [];
    for(let i = 1; i <= ownerCount; i++) {
        let name = $(`input[name="owner_name_${i}"]`).val();
        let ic = $(`input[name="owner_ic_${i}"]`).val();
        let email = $(`input[name="owner_email_${i}"]`).val();
        let phone = $(`input[name="owner_phone_${i}"]`).val();
        if(name) {
            owners.push({ name: name, ic: ic || '', email: email || '', phone: phone || '' });
        }
    }
    formData.append('owners_data', JSON.stringify(owners));
    
    // Add spouse and children data
    let spouseCount = $('#spouse_count').val();
    let spouses = [];
    for(let i = 1; i <= spouseCount; i++) {
        let name = $('input[name="spouse_name_' + i + '"]').val();
        let ic = $('input[name="spouse_ic_' + i + '"]').val();
        if(name) {
            spouses.push({ name: name, ic: ic || '' });
        }
    }
    
    let childCount = $('#child_count').val();
    let children = [];
    for(let i = 1; i <= childCount; i++) {
        let name = $('input[name="child_name_' + i + '"]').val();
        let ic = $('input[name="child_ic_' + i + '"]').val();
        if(name) {
            children.push({ name: name, ic: ic || '' });
        }
    }
    
    formData.append('spouse_data', JSON.stringify(spouses));
    formData.append('children_data', JSON.stringify(children));
    
    // Add the actual FILE objects
    for(let i = 0; i < ssmFiles.length; i++) {
        formData.append('ssm_files[]', ssmFiles[i]);
    }
    for(let i = 0; i < taxFiles.length; i++) {
        formData.append('tax_files[]', taxFiles[i]);
    }
    for(let i = 0; i < bankFiles.length; i++) {
        formData.append('bank_files[]', bankFiles[i]);
    }
    for(let i = 0; i < financialFiles.length; i++) {
        formData.append('financial_files[]', financialFiles[i]);
    }
    for(let i = 0; i < otherFiles.length; i++) {
        formData.append('other_files[]', otherFiles[i]);
    }
    
    let $btn = $('#submitBtn');
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: 'ajax.php?action=save_document_multipage',
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(resp) {
            if(resp == '1') {
                Swal.fire({ icon: 'success', title: 'Success!', text: 'Document saved!', timer: 1500, showConfirmButton: false })
                    .then(() => { window.location.href = 'index.php?page=document_list'; });
            } else {
                Swal.fire({ icon: 'error', title: 'Error!', text: resp });
                $btn.html('Submit').prop('disabled', false);
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error!', text: 'AJAX Error: ' + xhr.status });
            $btn.html('Submit').prop('disabled', false);
        }
    });
});
</script>