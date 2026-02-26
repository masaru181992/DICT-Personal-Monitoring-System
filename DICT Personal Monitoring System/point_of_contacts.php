<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$page_title = 'Point of Contacts';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'save_contact':
                    $data = [
                        'type' => $_POST['type'],
                        'title' => trim($_POST['title']),
                        'description' => trim($_POST['description']),
                        'phone' => trim($_POST['phone']),
                        'email' => trim($_POST['email']),
                        'officer_name' => trim($_POST['officer_name']),
                        'officer_position' => trim($_POST['officer_position']),
                        'officer_phone' => trim($_POST['officer_phone']),
                        'alt_focal_name' => trim($_POST['alt_focal_name']),
                        'alt_focal_position' => trim($_POST['alt_focal_position']),
                        'alt_focal_phone' => trim($_POST['alt_focal_phone'])
                    ];
                    
                    // Basic validation
                    if (empty($data['title']) || empty($data['type'])) {
                        throw new Exception('Title and type are required');
                    }
                    
                    if (isset($_POST['id']) && !empty($_POST['id'])) {
                        // Update existing contact
                        $stmt = $pdo->prepare("UPDATE point_of_contacts SET 
                            type = :type,
                            title = :title,
                            description = :description,
                            phone = :phone,
                            email = :email,
                            officer_name = :officer_name,
                            officer_position = :officer_position,
                            officer_phone = :officer_phone,
                            alt_focal_name = :alt_focal_name,
                            alt_focal_position = :alt_focal_position,
                            alt_focal_phone = :alt_focal_phone,
                            updated_at = NOW()
                            WHERE id = :id");
                        
                        $data['id'] = $_POST['id'];
                        $stmt->execute($data);
                        $response['message'] = 'Contact updated successfully';
                    } else {
                        // Insert new contact
                        $stmt = $pdo->prepare("INSERT INTO point_of_contacts 
                            (type, title, description, phone, email, 
                            officer_name, officer_position, officer_phone, 
                            alt_focal_name, alt_focal_position, alt_focal_phone) 
                            VALUES 
                            (:type, :title, :description, :phone, :email, 
                            :officer_name, :officer_position, :officer_phone, 
                            :alt_focal_name, :alt_focal_position, :alt_focal_phone)");
                        
                        $stmt->execute($data);
                        $response['message'] = 'Contact added successfully';
                    }
                    
                    $response['success'] = true;
                    break;
                    
                case 'delete_contact':
                    if (empty($_POST['id'])) {
                        throw new Exception('Invalid contact ID');
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM point_of_contacts WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    
                    $response = [
                        'success' => true,
                        'message' => 'Contact deleted successfully'
                    ];
                    break;
            }
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // For non-AJAX requests, redirect with a message
    $_SESSION['message'] = $response['message'];
    $_SESSION['message_type'] = $response['success'] ? 'success' : 'danger';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all contacts grouped by type
$contacts = [
    'provincial' => [],
    'municipal' => [],
    'nga' => [],
    'ngo' => []
];

try {
    $stmt = $pdo->query("SELECT * FROM point_of_contacts ORDER BY title ASC");
    $all_contacts = $stmt->fetchAll();
    
    foreach ($all_contacts as $contact) {
        if (isset($contacts[$contact['type']])) {
            $contacts[$contact['type']][] = $contact;
        }
    }
} catch (Exception $e) {
    // Log error or handle it appropriately
    error_log('Database error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Personal Monitoring System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .copy-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            transition: color 0.2s;
        }
        .copy-btn:hover {
            color: #0d6efd;
        }
        .input-group-copy {
            position: relative;
        }
        .form-control-with-copy {
            padding-right: 35px;
        }
        .contact-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
            height: 100%;
        }
        
        .contact-card .card-body {
            padding: 1.5rem;
        }
        
        /* Match sidebar title color */
        .card-title, 
        .fw-bold {
            color: var(--accent-color, #64ffda);
        }
        
        .card-text.text-muted,
        .text-muted.small {
            color: #ffffff !important;
            opacity: 0.8;
        }
        
        .contact-icon {
            color: var(--accent-color, #64ffda);
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .contact-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--accent-color, #64ffda);
        }
        .nav-pills .nav-link.active {
            background-color: var(--accent-color, #64ffda);
            color: white;
        }
        .nav-pills .nav-link {
            color: var(--accent-color, #64ffda);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Point of Contacts</h4>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="#" class="btn btn-custom me-2" id="addContactBtn" data-bs-toggle="modal" data-bs-target="#editContactModal">
                                <i class="bi bi-person-plus"></i> Add Contact
                            </a>
                            <a href="export_contacts.php" class="btn btn-custom">
                                <i class="bi bi-download"></i> Export to Excel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-pills mb-4" id="contactsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="provincial-tab" data-bs-toggle="pill" data-bs-target="#provincial" type="button" role="tab">
                            <i class="bi bi-building"></i> Provincial LGU
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="municipal-tab" data-bs-toggle="pill" data-bs-target="#municipal" type="button" role="tab">
                            <i class="bi bi-building"></i> Municipal LGU
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nga-tab" data-bs-toggle="pill" data-bs-target="#nga" type="button" role="tab">
                            <i class="bi bi-bank"></i> NGA
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ngo-tab" data-bs-toggle="pill" data-bs-target="#ngo" type="button" role="tab">
                            <i class="bi bi-people"></i> NGO
                        </button>
                    </li>
                </ul>

                <!-- Display messages if any -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Tab Content -->
                <div class="tab-content" id="contactsTabContent">
                    <?php 
                    $tabs = [
                        'provincial' => 'Provincial LGU',
                        'municipal' => 'Municipal LGU',
                        'nga' => 'National Government Agencies',
                        'ngo' => 'NGO/Private Sector'
                    ];
                    
                    $first = true;
                    foreach ($tabs as $type => $label): 
                        $active = $first ? 'show active' : '';
                        $first = false;
                    ?>
                    <div class="tab-pane fade <?php echo $active; ?>" id="<?php echo $type; ?>" role="tabpanel" aria-labelledby="<?php echo $type; ?>-tab">
                        <div class="row g-4">
                            <?php if (empty($contacts[$type])): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">No contacts found. Click 'Add New Contact' to add one.</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($contacts[$type] as $contact): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card contact-card h-100">
                                            <div class="card-body text-center position-relative">
                                                <button class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2 edit-contact" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editContactModal"
                                                        data-id="<?php echo htmlspecialchars($contact['id']); ?>"
                                                        data-type="<?php echo htmlspecialchars($contact['type']); ?>"
                                                        data-title="<?php echo htmlspecialchars($contact['title']); ?>"
                                                        data-description="<?php echo htmlspecialchars($contact['description']); ?>"
                                                        data-phone="<?php echo htmlspecialchars($contact['phone']); ?>"
                                                        data-email="<?php echo htmlspecialchars($contact['email']); ?>"
                                                        data-officer-name="<?php echo htmlspecialchars($contact['officer_name']); ?>"
                                                        data-officer-position="<?php echo htmlspecialchars($contact['officer_position']); ?>"
                                                        data-officer-phone="<?php echo htmlspecialchars($contact['officer_phone']); ?>"
                                                        data-alt-focal-name="<?php echo htmlspecialchars($contact['alt_focal_name']); ?>"
                                                        data-alt-focal-position="<?php echo htmlspecialchars($contact['alt_focal_position']); ?>"
                                                        data-alt-focal-phone="<?php echo htmlspecialchars($contact['alt_focal_phone']); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <!-- Main Contact Info -->
                                                <div class="contact-icon mb-3">
                                                    <i class="bi bi-person-badge"></i>
                                                </div>
                                                <h5 class="card-title"><?php echo htmlspecialchars($contact['title']); ?></h5>
                                                <?php if (!empty($contact['description'])): ?>
                                                    <p class="card-text text-muted"><?php echo htmlspecialchars($contact['description']); ?></p>
                                                <?php endif; ?>
                                                
                                                <!-- Contact Information -->
                                                <div class="mt-3 border-top pt-2">
                                                    <h6 class="fw-bold mb-2"><i class="bi bi-telephone"></i> Contact Information</h6>
                                                    <?php if (!empty($contact['phone'])): ?>
                                                        <p class="mb-1"><i class="bi bi-telephone"></i> Office: <?php echo htmlspecialchars($contact['phone']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($contact['email'])): ?>
                                                        <p class="mb-1"><i class="bi bi-envelope"></i> Email: <?php echo htmlspecialchars($contact['email']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Officer-in-Charge -->
                                                <?php if (!empty($contact['officer_name'])): ?>
                                                    <div class="mt-3 border-top pt-2">
                                                        <h6 class="fw-bold mb-2"><i class="bi bi-person-fill"></i> Officer-in-Charge</h6>
                                                        <p class="mb-1"><strong><?php echo htmlspecialchars($contact['officer_name']); ?></strong></p>
                                                        <?php if (!empty($contact['officer_position'])): ?>
                                                            <p class="mb-1 text-muted small"><?php echo htmlspecialchars($contact['officer_position']); ?></p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($contact['officer_phone'])): ?>
                                                            <p class="mb-0"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($contact['officer_phone']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Alternative Focal -->
                                                <?php if (!empty($contact['alt_focal_name'])): ?>
                                                    <div class="mt-3 border-top pt-2">
                                                        <h6 class="fw-bold mb-2"><i class="bi bi-person-plus-fill"></i> Alternative Focal</h6>
                                                        <p class="mb-1"><strong><?php echo htmlspecialchars($contact['alt_focal_name']); ?></strong></p>
                                                        <?php if (!empty($contact['alt_focal_position'])): ?>
                                                            <p class="mb-1 text-muted small"><?php echo htmlspecialchars($contact['alt_focal_position']); ?></p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($contact['alt_focal_phone'])): ?>
                                                            <p class="mb-0"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($contact['alt_focal_phone']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Last Updated -->
                                                <div class="mt-3 border-top pt-2 text-muted small">
                                                    <p class="mb-0">
                                                        <i class="bi bi-clock"></i> Last updated: 
                                                        <?php 
                                                        $date = new DateTime($contact['updated_at']);
                                                        echo $date->format('F j, Y g:i A'); 
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    </div>
                </div>
            </div>

                        </div>
                    </div>

                    <!-- Bootstrap Bundle with Popper -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                    <!-- jQuery -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <!-- Custom Scripts -->
                    <!-- Edit Contact Modal -->
                    <div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editContactModalLabel">Edit Contact</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="contactForm">
                                    <input type="hidden" name="action" id="formAction" value="save_contact">
                                    <input type="hidden" name="id" id="contactId">
                                    
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="contactType" class="form-label">Contact Type</label>
                                                <select class="form-select" id="contactType" name="type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="provincial">Provincial LGU</option>
                                                    <option value="municipal">Municipal LGU</option>
                                                    <option value="nga">National Government Agency</option>
                                                    <option value="ngo">NGO/Private Sector</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="contactTitle" class="form-label">Title</label>
                                                    <div class="input-group-copy">
                                                        <input type="text" class="form-control form-control-with-copy" id="contactTitle" name="title" required>
                                                        <button type="button" class="copy-btn" title="Copy to clipboard">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <label for="contactDescription" class="form-label">Description</label>
                                                <div class="input-group-copy">
                                                    <input type="text" class="form-control form-control-with-copy" id="contactDescription" name="description">
                                                    <button type="button" class="copy-btn" title="Copy to clipboard">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="contactPhone" class="form-label">Phone</label>
                                                <div class="input-group-copy">
                                                    <input type="tel" class="form-control form-control-with-copy" id="contactPhone" name="phone">
                                                    <button type="button" class="copy-btn" title="Copy to clipboard">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="contactEmail" class="form-label">Email</label>
                                                <div class="input-group-copy">
                                                    <input type="email" class="form-control form-control-with-copy" id="contactEmail" name="email">
                                                    <button type="button" class="copy-btn" title="Copy to clipboard">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="border p-3 mb-3 rounded">
                                            <h6 class="mb-3"><i class="bi bi-person-fill"></i> Officer-in-Charge</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="officerName" class="form-label">Name</label>
                                                    <div class="input-group-copy">
                                                        <input type="text" class="form-control form-control-with-copy" id="officerName" name="officer_name">
                                                        <button type="button" class="copy-btn" title="Copy to clipboard">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="officerPosition" class="form-label">Position</label>
                                                    <div class="input-group-copy">
                                                        <input type="text" class="form-control form-control-with-copy" id="officerPosition" name="officer_position">
                                                        <button type="button" class="copy-btn" title="Copy to clipboard">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="officerPhone" class="form-label">Phone</label>
                                                    <div class="input-group-copy">
                                                        <input type="tel" class="form-control form-control-with-copy" id="officerPhone" name="officer_phone">
                                                        <button type="button" class="copy-btn" title="Copy to clipboard">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="border p-3 rounded">
                                            <h6 class="mb-3"><i class="bi bi-person-plus-fill"></i> Alternative Focal</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="altFocalName" class="form-label">Name</label>
                                                    <div class="input-group-copy">
                                                        <input type="text" class="form-control form-control-with-copy" id="altFocalName" name="alt_focal_name">
                                                        <button type="button" class="copy-btn" title="Copy to clipboard">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="altFocalPosition" class="form-label">Position</label>
                                                    <div class="input-group-copy">
                                                        <input type="text" class="form-control form-control-with-copy" id="altFocalPosition" name="alt_focal_position">
                                                        <button type="button" class="copy-btn" title="Copy to clipboard">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="altFocalPhone" class="form-label">Phone</label>
                                                    <div class="input-group-copy">
                                                        <input type="tel" class="form-control form-control-with-copy" id="altFocalPhone" name="alt_focal_phone">
                                                        <button type="button" class="copy-btn" title="Copy to clipboard">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div id="deleteButtonContainer" class="me-auto">
                                            <!-- Delete button will be shown only when editing -->
                                        </div>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        // Function to show alert message
                        function showAlert(type, message) {
                            // Remove any existing alerts
                            const existingAlert = document.querySelector('.alert-dismissible');
                            if (existingAlert) {
                                existingAlert.remove();
                            }
                            
                            const alertHtml = `
                                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                    ${message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            
                            // Insert alert after the page header
                            const header = document.querySelector('main .d-flex.justify-content-between');
                            if (header) {
                                header.insertAdjacentHTML('afterend', alertHtml);
                            }
                            
                            // Auto-hide after 5 seconds
                            setTimeout(() => {
                                const alert = document.querySelector('.alert-dismissible');
                                if (alert) {
                                    const bsAlert = new bootstrap.Alert(alert);
                                    bsAlert.close();
                                }
                            }, 5000);
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            // Initialize tooltips
                            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                            
                            // Add copy functionality to all copy buttons
                            document.addEventListener('click', function(e) {
                                if (e.target.closest('.copy-btn') || e.target.classList.contains('bi-clipboard')) {
                                    const copyBtn = e.target.closest('.copy-btn') || e.target.closest('button');
                                    const input = copyBtn.closest('.input-group-copy').querySelector('input, textarea, select');
                                    
                                    if (input) {
                                        input.select();
                                        document.execCommand('copy');
                                        
                                        // Change icon to checkmark temporarily
                                        const icon = copyBtn.querySelector('i');
                                        if (icon) {
                                            const originalClass = icon.className;
                                            icon.className = 'bi bi-check2';
                                            copyBtn.title = 'Copied!';
                                            
                                            // Reset after 2 seconds
                                            setTimeout(() => {
                                                icon.className = originalClass;
                                                copyBtn.title = 'Copy to clipboard';
                                            }, 2000);
                                        }
                                    }
                                }
                            });
                            
                            // Get modal and form elements
                            const editContactModal = document.getElementById('editContactModal');
                            const contactForm = document.getElementById('contactForm');
                            const deleteButtonContainer = document.getElementById('deleteButtonContainer');
                            
                            // Handle modal show event
                            if (editContactModal) {
                                editContactModal.addEventListener('show.bs.modal', function (event) {
                                    const button = event.relatedTarget;
                                    const isNewContact = button.id === 'addContactBtn';
                                    
                                    // Reset form
                                    contactForm.reset();
                                    document.getElementById('formAction').value = 'save_contact';
                                    document.getElementById('contactId').value = '';
                                    
                                    // Show/hide delete button
                                    if (isNewContact) {
                                        deleteButtonContainer.innerHTML = '';
                                        document.getElementById('editContactModalLabel').textContent = 'Add New Contact';
                                    } else {
                                        // Create delete button if it doesn't exist
                                        if (!document.getElementById('deleteContactBtn')) {
                                            const deleteBtn = document.createElement('button');
                                            deleteBtn.type = 'button';
                                            deleteBtn.className = 'btn btn-danger';
                                            deleteBtn.id = 'deleteContactBtn';
                                            deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Delete';
                                            deleteBtn.onclick = function() {
                                                if (confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
                                                    const contactId = document.getElementById('contactId').value;
                                                    deleteContact(contactId);
                                                }
                                            };
                                            deleteButtonContainer.innerHTML = '';
                                            deleteButtonContainer.appendChild(deleteBtn);
                                        }
                                        
                                        // Set form values from data attributes
                                        const contactId = button.getAttribute('data-id');
                                        if (contactId) document.getElementById('contactId').value = contactId;
                                        
                                        const type = button.getAttribute('data-type');
                                        if (type) document.getElementById('contactType').value = type;
                                        
                                        const title = button.getAttribute('data-title');
                                        if (title) document.getElementById('contactTitle').value = title;
                                        
                                        const description = button.getAttribute('data-description');
                                        if (description) document.getElementById('contactDescription').value = description;
                                        
                                        const phone = button.getAttribute('data-phone');
                                        if (phone) document.getElementById('contactPhone').value = phone;
                                        
                                        const email = button.getAttribute('data-email');
                                        if (email) document.getElementById('contactEmail').value = email;
                                        
                                        // Officer fields
                                        const officerName = button.getAttribute('data-officer-name');
                                        if (officerName) document.getElementById('officerName').value = officerName;
                                        
                                        const officerPosition = button.getAttribute('data-officer-position');
                                        if (officerPosition) document.getElementById('officerPosition').value = officerPosition;
                                        
                                        const officerPhone = button.getAttribute('data-officer-phone');
                                        if (officerPhone) document.getElementById('officerPhone').value = officerPhone;
                                        
                                        // Alternative focal fields
                                        const altFocalName = button.getAttribute('data-alt-focal-name');
                                        if (altFocalName) document.getElementById('altFocalName').value = altFocalName;
                                        
                                        const altFocalPosition = button.getAttribute('data-alt-focal-position');
                                        if (altFocalPosition) document.getElementById('altFocalPosition').value = altFocalPosition;
                                        
                                        const altFocalPhone = button.getAttribute('data-alt-focal-phone');
                                        if (altFocalPhone) document.getElementById('altFocalPhone').value = altFocalPhone;
                                        
                                        document.getElementById('editContactModalLabel').textContent = 'Edit Contact';
                                    }
                                    
                                    // Set default type if coming from tab
                                    const activeTab = document.querySelector('.nav-pills .nav-link.active');
                                    if (activeTab && !document.getElementById('contactType').value) {
                                        const tabType = activeTab.getAttribute('id').replace('-tab', '');
                                        if (tabType) {
                                            document.getElementById('contactType').value = tabType;
                                        }
                                    }
                                    
                                    // Store the edit button for later use
                                    editContactModal._editButton = button;
                                });
                            }
                            
                            // Handle form submission
                            if (contactForm) {
                                contactForm.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    
                                    // Show loading state
                                    const submitBtn = contactForm.querySelector('button[type="submit"]');
                                    const originalBtnText = submitBtn.innerHTML;
                                    submitBtn.disabled = true;
                                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                                    
                                    // Get form data
                                    const formData = new FormData(contactForm);
                                    
                                    // Submit via AJAX
                                    fetch(window.location.href, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            // Show success message
                                            showAlert('success', data.message);
                                            
                                            // Close modal and reload the page to show updated data
                                            const modal = bootstrap.Modal.getInstance(editContactModal);
                                            modal.hide();
                                            
                                            // Small delay to show success message before reload
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 1000);
                                        } else {
                                            // Show error message
                                            showAlert('danger', data.message || 'An error occurred while saving the contact');
                                            submitBtn.disabled = false;
                                            submitBtn.innerHTML = originalBtnText;
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showAlert('danger', 'An error occurred while saving the contact');
                                        submitBtn.disabled = false;
                                        submitBtn.innerHTML = originalBtnText;
                                    });
                                });
                            }
                            
                            // Function to delete a contact
                            function deleteContact(contactId) {
                                if (!contactId) return;
                                
                                if (confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
                                    const formData = new FormData();
                                    formData.append('action', 'delete_contact');
                                    formData.append('id', contactId);
                                    
                                    // Show loading state
                                    const deleteBtn = document.getElementById('deleteContactBtn');
                                    const originalBtnText = deleteBtn.innerHTML;
                                    deleteBtn.disabled = true;
                                    deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
                                    
                                    fetch(window.location.href, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            // Show success message
                                            showAlert('success', data.message);
                                            
                                            // Close modal and reload the page to show updated data
                                            const modal = bootstrap.Modal.getInstance(editContactModal);
                                            modal.hide();
                                            
                                            // Small delay to show success message before reload
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 1000);
                                        } else {
                                            showAlert('danger', data.message || 'Failed to delete contact');
                                            deleteBtn.disabled = false;
                                            deleteBtn.innerHTML = originalBtnText;
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showAlert('danger', 'An error occurred while deleting the contact');
                                        deleteBtn.disabled = false;
                                        deleteBtn.innerHTML = originalBtnText;
                                    });
                                }
                            }
                        });
                    </script>
</body>
</html>
