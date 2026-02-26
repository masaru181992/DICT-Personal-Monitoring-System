<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$is_edit = isset($_GET['edit']) && (int)$_GET['edit'] > 0;
$target_id = $is_edit ? (int)$_GET['edit'] : 0;
$target = null;
$categories = [];

try {
    // Fetch all categories for the dropdown
    $categoryStmt = $pdo->query("SELECT * FROM ipcr_categories ORDER BY name");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If in edit mode, fetch the target details
    if ($is_edit && $target_id > 0) {
        $targetStmt = $pdo->prepare("
            SELECT t.*, c.name as category_name 
            FROM ipcr_targets t
            JOIN ipcr_categories c ON t.category_id = c.id
            WHERE t.id = ? AND t.user_id = ?
        ");
        $targetStmt->execute([$target_id, $user_id]);
        $target = $targetStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$target) {
            $_SESSION['error_message'] = 'Target not found or access denied';
            header('Location: ipcr_target_status.php');
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Error in ipcr_target_form.php: " . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while loading the form';
    header('Location: ipcr_target_status.php');
    exit();
}
?>

<!-- Add/Edit Target Modal -->
<div class="modal fade" id="ipcrTargetModal" tabindex="-1" aria-labelledby="ipcrTargetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ipcrTargetModalLabel">
                    <?= $is_edit ? 'Edit IPCR Target' : 'Add New IPCR Target' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ipcrTargetForm" action="<?= $is_edit ? 'update_ipcr_target.php' : 'add_ipcr_target.php' ?>" method="POST">
                <div class="modal-body">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="target_id" value="<?= htmlspecialchars($target['id']) ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="<?= htmlspecialchars($target['title'] ?? '')" <?= !$is_edit ? '' : 'readonly' ?>>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"><?= 
                            htmlspecialchars($target['description'] ?? '') 
                        ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"
                                        <?= (isset($target['category_id']) && $target['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="target_date" class="form-label">Target Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="target_date" name="target_date" required
                                   value="<?= isset($target['target_date']) ? date('Y-m-d', strtotime($target['target_date'])) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="target_quantity" class="form-label">Target Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="target_quantity" name="target_quantity" min="0.01" step="0.01" required
                                   value="<?= isset($target['target_quantity']) ? htmlspecialchars($target['target_quantity']) : '1' ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="quantity_accomplished" class="form-label">Quantity of Actual Accomplishment</label>
                            <input type="number" class="form-control" id="quantity_accomplished" name="quantity_accomplished" min="0" step="0.01"
                                   value="<?= isset($target['quantity_accomplished']) ? htmlspecialchars($target['quantity_accomplished']) : '0' ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" 
                                   value="<?= htmlspecialchars($target['unit'] ?? 'unit(s)') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Not Started" <?= (isset($target['status']) && $target['status'] === 'Not Started') ? 'selected' : '' ?>>Not Started</option>
                                <option value="In Progress" <?= (isset($target['status']) && $target['status'] === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= (isset($target['status']) && $target['status'] === 'Completed') ? 'selected' : '' ?>>Completed</option>
                                <option value="On Hold" <?= (isset($target['status']) && $target['status'] === 'On Hold') ? 'selected' : '' ?>>On Hold</option>
                                <option value="Cancelled" <?= (isset($target['status']) && $target['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="Low" <?= (isset($target['priority']) && $target['priority'] === 'Low') ? 'selected' : '' ?>>Low</option>
                                <option value="Medium" <?= !isset($target['priority']) || (isset($target['priority']) && $target['priority'] === 'Medium') ? 'selected' : '' ?>>Medium</option>
                                <option value="High" <?= (isset($target['priority']) && $target['priority'] === 'High') ? 'selected' : '' ?>>High</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">
                        <span class="d-none spinner-border spinner-border-sm" id="saveSpinner" role="status" aria-hidden="true"></span>
                        <span id="saveBtnText"><?= $is_edit ? 'Update' : 'Save' ?> Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#target_date').attr('min', today);
    
    // Auto-update status based on progress
    function updateStatus() {
        const target = parseFloat($('#target_quantity').val()) || 0;
        const accomplished = parseFloat($('#quantity_accomplished').val()) || 0;
        
        if (target > 0) {
            const progress = Math.min(100, Math.round((accomplished / target) * 100));
            
            if (progress >= 100) {
                $('#status').val('Completed');
            } else if (progress > 0) {
                if ($('#status').val() === 'Not Started') {
                    $('#status').val('In Progress');
                }
            }
        }
    }
    
    // Handle quantity changes
    $('#target_quantity, #quantity_accomplished').on('input', updateStatus);
    
    // Save changes button click handler
    $('#saveChangesBtn').on('click', function() {
        const form = $('#ipcrTargetForm');
        const submitBtn = $(this);
        const spinner = submitBtn.find('#saveSpinner');
        const btnText = submitBtn.find('#saveBtnText');
        const originalBtnText = btnText.text();
        
        // Validate form
        if (!form[0].checkValidity()) {
            form.addClass('was-validated');
            return;
        }
        
        // Show loading state
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Saving...');
        
        // Submit form via AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast('Success', response.message || 'Changes saved successfully', 'success');
                    
                    // Close modal after a short delay
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('ipcrTargetModal'));
                        if (modal) modal.hide();
                        
                        // Reload the page or update the table
                        if (typeof window.reloadIPCRData === 'function') {
                            window.reloadIPCRData();
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    showToast('Error', response.message || 'Failed to save changes', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showToast('Error', 'An error occurred while saving. Please try again.', 'error');
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text(originalBtnText);
            }
        });
    });
    
    // Show toast notification
    function showToast(title, message, type = 'info') {
        const toastContainer = $('#toastContainer');
        if (toastContainer.length === 0) {
            $('body').append('<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1100;"></div>');
        }
        
        const toastId = 'toast-' + Date.now();
        const toast = $(`
            <div id="${toastId}" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);
        
        $('#toastContainer').append(toast);
        
        // Auto-remove toast after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
    
    // Make functions available globally
    window.showToast = showToast;
});
</script>
