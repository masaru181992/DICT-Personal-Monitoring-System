<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Set page title
$page_title = "Settings";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Personal Monitoring System - <?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>

        :root {
            --primary-bg: #0a192f;
            --secondary-bg: rgba(16, 32, 56, 0.9);
            --accent-color: #64ffda;
            --accent-secondary: #7928ca;
            --text-white: #ffffff;
            --border-color: rgba(100, 255, 218, 0.1);
        }
        
        body {
            background-color: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(100, 255, 218, 0.1) 0%, transparent 50%),
                radial-gradient(at 100% 0%, rgba(121, 40, 202, 0.1) 0%, transparent 50%);
            color: var(--text-white);
            font-family: 'Space Grotesk', sans-serif;
        }
        
        .form-control, .form-select {
            background: rgba(16, 32, 56, 0.9);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(16, 32, 56, 0.95);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(100, 255, 218, 0.2);
            color: var(--text-white);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select option {
            background: var(--primary-bg);
            color: var(--text-white);
        }
         /* Modal Styles */
         .modal-content {
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            color: var(--text-white);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
        }

        .modal-title {
            color: var(--text-white);
            font-weight: 600;
        }

        /* Sidebar Enhancement */
        .sidebar {
            min-height: 100vh;
            background: rgba(10, 25, 47, 0.95);
            border-right: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .sidebar h3 {
            color: var(--text-white);
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(100, 255, 218, 0.05);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(100, 255, 218, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 255, 218, 0.3);
        }

        .main-content {
            overflow-y: auto;
            height: 100vh;
            padding: 1.5rem;
            margin-left: 350px;
            max-width: calc(100% - 350px);
            box-sizing: border-box;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                max-width: 100%;
                padding: 1rem;
            }
        }

        /* Nav Item Styling */
        .nav-item {
            color: #a0aec0;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            opacity: 0.8;
        }

        .sidebar a:hover, .sidebar a.active {
            background: linear-gradient(135deg, rgba(100, 255, 218, 0.1), rgba(121, 40, 202, 0.1));
            color: var(--accent-color);
            transform: translateX(5px);
            opacity: 1;
        }

        .sidebar a i {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .sidebar a.active {
            background: linear-gradient(135deg, rgba(100, 255, 218, 0.2), rgba(121, 40, 202, 0.2));
            border-left: 4px solid var(--accent-color);
        }

        .main-content {
            padding: 30px;
            color: var(--text-white);
        }

        .card {
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            color: var(--text-white);
            margin: 0;
        }
        .btn-custom {
            background: linear-gradient(135deg, var(--accent-color) 0%, #4ad3b5 100%);
            color: var(--primary-bg);
            font-weight: 600;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(100, 255, 218, 0.4);
            color: var(--primary-bg);
        }
        
        /* Custom scrollbar */
        .table-responsive::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #4dffd1;
        }
        
        /* Fixed header */
        .table thead th {
            position: sticky;
            top: 0;
            background-color: var(--secondary-bg);
            z-index: 10;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Responsive table container */
        .table-container {
            max-height: calc(100vh - 250px);
            min-height: 300px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        @media (max-width: 992px) {
            .table-container {
                max-height: calc(100vh - 280px);
            }
            
            .table {
                width: 100%;
                margin-bottom: 1rem;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table thead,
            .table tbody,
            .table th,
            .table td,
            .table tr {
                display: block;
            }
            
            .table thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            
            .table tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
            }
            
            .table td {
                border: none;
                border-bottom: 1px solid var(--border-color);
                position: relative;
                padding-left: 50%;
                width: 100%;
                box-sizing: border-box;
            }
            
            .table td:before {
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: 600;
                color: var(--accent-color);
            }
            
            .table td:nth-of-type(1):before { content: 'Title'; }
            .table td:nth-of-type(2):before { content: 'Description'; }
            .table td:nth-of-type(3):before { content: 'Start Date'; }
            .table td:nth-of-type(4):before { content: 'End Date'; }
            .table td:nth-of-type(5):before { content: 'Status'; }
            .table td:nth-of-type(6):before { content: 'Actions'; }
        }

        .btn-custom i {
            font-size: 1.1rem;
        }

        /* Status Badge Colors */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
        }

        .status-not-started {
            color: #ffd700;
            border-color: rgba(255, 215, 0, 0.3);
        }

        .status-in-progress {
            color: var(--accent-color);
            border-color: rgba(100, 255, 218, 0.3);
        }

        .status-completed {
            color: #00ff00;
            border-color: rgba(0, 255, 0, 0.3);
        }

        .status-on-hold {
            color: #ff9900;
            border-color: rgba(255, 153, 0, 0.3);
        }

        /* Alert Styles */
        .alert {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--text-white);
            backdrop-filter: blur(10px);
            border-radius: 8px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
        }

        /* Card and Container Styles */
        .card {
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .table-responsive {
            background: var(--secondary-bg);
            border-radius: 16px;
            padding: 0;
        }

        /* Delete Button Style */
        .btn-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ff4d4d;
        }

        .btn-danger:hover {
            background: rgba(220, 53, 69, 0.3);
            color: #ff4d4d;
        }

        /* Modal Form Labels */
        .form-label {
            color: var(--text-white);
            margin-bottom: 8px;
        }

        /* Table Column Widths */
        .table th:nth-child(1), .table td:nth-child(1) { width: 18%; min-width: 150px; } /* Title */
        .table th:nth-child(2), .table td:nth-child(2) { width: 27%; min-width: 200px; } /* Description */
        .table th:nth-child(3), .table td:nth-child(3) { width: 13%; min-width: 120px; } /* Start Date */
        .table th:nth-child(4), .table td:nth-child(4) { width: 13%; min-width: 120px; } /* End Date */
        .table th:nth-child(5), .table td:nth-child(5) { width: 14%; min-width: 120px; } /* Status */
        .table th:nth-child(6), .table td:nth-child(6) { width: 15%; min-width: 100px; } /* Actions */
        
        .table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            vertical-align: middle;
        }
        
        .table td:nth-child(2) { /* Description column */
            max-width: 300px;
            white-space: normal;
            word-break: break-word;
        }
        
        /* Sorting Controls */
        .btn-sort {
            background: rgba(100, 255, 218, 0.1);
            border: 1px solid rgba(100, 255, 218, 0.2);
            color: #a0aec0;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-sort:hover, .btn-sort.active {
            background: rgba(100, 255, 218, 0.2);
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-sort i {
            font-size: 0.9em;
        }
        
        .sorting-controls {
            background: rgba(16, 32, 56, 0.5);
            padding: 10px 15px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .sort-link {
            color: var(--text-white);
            text-decoration: none;
            display: block;
        }
        
        .sort-link:hover {
            color: var(--accent-color);
        }
        .btn-db-action {
            min-width: 100%;
            margin: 0;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        @media (min-width: 768px) {
            .btn-db-action {
                min-width: 150px;
                margin: 0 5px;
            }
        }
        
        .btn-db-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .backup-card {
            background: rgba(16, 32, 56, 0.9);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        @media (min-width: 768px) {
            .backup-card {
                padding: 25px;
                margin: 20px 0;
            }
        }
        
        .backup-card h5 {
            color: var(--accent-color);
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            font-size: 1.25rem;
        }
        
        .backup-info {
            background: rgba(100, 255, 218, 0.1);
            border-left: 3px solid var(--accent-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
    </style>
</head>
<body>
    <!-- Success/Error Alert -->
    <div id="alert-container" class="position-fixed top-0 end-0 m-3" style="z-index: 1100; max-width: 400px; display: none;">
        <div class="alert alert-dismissible fade show" role="alert">
            <span id="alert-message"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <div class="container-fluid px-0">
        <div class="row g-0 min-vh-100">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main Content -->
            <div class="col-12 col-md-9 col-lg-10 ms-auto p-3 p-md-4" style="margin-top: 60px; overflow-y: auto; max-height: 100vh;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                        <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Settings</h4>
                            
                            <!-- Database Backup & Restore Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="backup-card">
                                        <h5><i class="bi bi-database-gear me-2"></i>Database Management</h5>
                                        
                                        <div class="backup-info mb-4">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            Create a backup of your database or restore from a previous backup file (.sql).
                                        </div>
                                        
                                        <div class="d-flex flex-wrap gap-2">
                                            <button id="backupBtn" class="btn btn-custom">
                                                <i class="bi bi-download me-2"></i>Backup Database
                                            </button>
                                            
                                            <button id="restoreBtn" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#restoreModal">
                                                <i class="bi bi-upload me-2"></i>Restore Database
                                            </button>
                                            
                                            <button id="downloadBlankBtn" class="btn btn-custom">
                                                <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Blank Database
                                            </button>
                                            
                                            <a id="downloadLink" href="#" class="btn btn-custom" style="display: none;">
                                                <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Backup
                                            </a>
                                        </div>
                                        
                                        <div id="backupStatus" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Restore Database Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreModalLabel">Restore Database</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Warning:</strong> This will overwrite all existing data in the database. Make sure you have a backup before proceeding.
                    </div>
                    
                    <form id="restoreForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="backupFile" class="form-label">Select SQL Backup File</label>
                            <input class="form-control" type="file" id="backupFile" name="backup_file" accept=".sql" required>
                            <div class="form-text">Only .sql files are allowed</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmRestoreBtn" class="btn btn-warning">Restore Database</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Show alert message
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alert-container');
            const alertMessage = document.getElementById('alert-message');
            const alertBox = alertContainer.querySelector('.alert');
            
            // Set message and alert type
            alertMessage.textContent = message;
            alertBox.className = `alert alert-${type} alert-dismissible fade show`;
            
            // Show the alert
            alertContainer.style.display = 'block';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alertBox);
                bsAlert.close();
            }, 5000);
        }
        
        // Handle backup button click
        document.getElementById('backupBtn').addEventListener('click', function() {
            const backupBtn = this;
            const originalText = backupBtn.innerHTML;
            
            // Show loading state
            backupBtn.disabled = true;
            backupBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating Backup...';
            
            // Clear any previous alerts
            const alertContainer = document.getElementById('alert-container');
            alertContainer.style.display = 'none';
            
            // Make AJAX request to backup database
            fetch('includes/db_backup_restore.php?action=backup', {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                const text = await response.text();
                
                // Log the raw response for debugging
                console.log('Raw response:', text);
                
                if (!response.ok) {
                    // If the response is not JSON, throw the raw text
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error(`Server returned ${response.status}: ${text}`);
                    }
                    // If it is JSON, try to parse the error message
                    try {
                        const errorData = JSON.parse(text);
                        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                    } catch (e) {
                        throw new Error(`HTTP error! status: ${response.status}. Response: ${text.substring(0, 200)}...`);
                    }
                }
                
                // Try to parse as JSON
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Invalid JSON response from server. See console for details.');
                }
            })
            .then(data => {
                if (data.status === 'success') {
                    // Show success message
                    showAlert('Backup created successfully!', 'success');
                    
                    // Show download link
                    const downloadLink = document.getElementById('downloadLink');
                    downloadLink.href = data.file;
                    downloadLink.download = data.file.split('/').pop();
                    downloadLink.style.display = 'inline-block';
                    
                    // Simulate click to start download after a short delay
                    setTimeout(() => {
                        downloadLink.click();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Failed to create backup');
                }
            })
            .catch(error => {
                console.error('Backup Error:', error);
                let errorMessage = 'An error occurred while creating the backup';
                
                if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Failed to connect to the server. Please check your connection.';
                } else if (error.message.includes('NetworkError')) {
                    errorMessage = 'Network error. Please check your internet connection.';
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                showAlert(errorMessage, 'danger');
            })
            .finally(() => {
                // Reset button state
                backupBtn.disabled = false;
                backupBtn.innerHTML = originalText;
            });
        });
        
        // Handle restore button click
        document.getElementById('confirmRestoreBtn').addEventListener('click', function() {
            const fileInput = document.getElementById('backupFile');
            const restoreBtn = this;
            const originalText = restoreBtn.innerHTML;
            
            if (!fileInput.files.length) {
                showAlert('Please select a backup file to restore', 'warning');
                return;
            }
            
            // Show confirmation dialog
            if (!confirm('WARNING: This will overwrite all existing data in the database. Are you sure you want to continue?')) {
                return;
            }
            
            // Show loading state
            restoreBtn.disabled = true;
            restoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Restoring...';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('backup_file', fileInput.files[0]);
            
            // Make AJAX request to restore database
            fetch('includes/db_backup_restore.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Database restored successfully! The page will now reload.', 'success');
                    // Reload the page after a short delay
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    throw new Error(data.message || 'Failed to restore database');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert(error.message || 'An error occurred while restoring the database', 'danger');
            })
            .finally(() => {
                // Reset button state
                restoreBtn.disabled = false;
                restoreBtn.innerHTML = originalText;
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('restoreModal'));
                if (modal) {
                    modal.hide();
                }
            });
        });
        
        // Reset file input when modal is hidden
        document.getElementById('restoreModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('restoreForm').reset();
        });
        
        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
            document.querySelectorAll('.live-time').forEach(el => {
                el.textContent = timeString;
            });
        }

        // Update clock immediately and then every minute
        updateClock();
        setInterval(updateClock, 60000);

        // Update date when needed (e.g., at midnight)
        function updateDate() {
            const now = new Date();
            const dateString = now.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
            document.querySelectorAll('.current-date').forEach(el => {
                el.textContent = dateString;
            });
        }

        // Update date on page load
        updateDate();
        
        // Handle download blank database button click
        document.getElementById('downloadBlankBtn').addEventListener('click', function() {
            const blankBtn = this;
            const originalText = blankBtn.innerHTML;
            
            // Show loading state
            blankBtn.disabled = true;
            blankBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating Blank Database...';
            
            // Clear any previous alerts
            const alertContainer = document.getElementById('alert-container');
            alertContainer.style.display = 'none';
            
            // Make AJAX request to create blank database
            fetch('includes/db_backup_restore.php?action=blank', {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                const text = await response.text();
                
                console.log('Blank database response:', text);
                
                if (response.ok && text.includes('-- Blank Database Structure')) {
                    // Create blob and download
                    const blob = new Blob([text], { type: 'application/sql' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'blank_database_' + new Date().toISOString().slice(0, 10) + '.sql';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    
                    showAlert('Blank database downloaded successfully!', 'success');
                } else {
                    throw new Error(text || 'Failed to create blank database');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error creating blank database: ' + error.message, 'danger');
            })
            .finally(() => {
                // Restore button state
                blankBtn.disabled = false;
                blankBtn.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>