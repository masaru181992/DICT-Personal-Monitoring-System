<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        // Handle add action
        if ($_POST['action'] === 'add') {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $status = strtolower(trim($_POST['status']));
            
            // Basic validation
            if (empty($title) || empty($start_date) || empty($end_date)) {
                throw new Exception("All required fields must be filled out.");
            }
            
            // Date validation
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $today = new DateTime();
            
            if ($end < $start) {
                throw new Exception("End date cannot be before start date.");
            }
            
            // Insert project
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $start_date, $end_date, $status]);
            
            $success_message = 'Project added successfully!';
            echo '<script>setTimeout(function(){window.location.href=window.location.pathname;},1000);</script>';
        }
        
        // Handle update action
        elseif ($_POST['action'] === 'update') {
            $project_id = (int)$_POST['project_id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $status = strtolower(trim($_POST['status']));
            
            // Basic validation
            if (empty($title) || empty($start_date) || empty($end_date)) {
                throw new Exception("All required fields must be filled out.");
            }
            
            // Date validation
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            
            if ($end < $start) {
                throw new Exception("End date cannot be before start date.");
            }
            
            // Update project
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $description, $start_date, $end_date, $status, $project_id]);
            
            $success_message = 'Project updated successfully!';
            echo '<script>setTimeout(function(){window.location.href=window.location.pathname;},1000);</script>';
        }
        
        // Handle delete action
        elseif ($_POST['action'] === 'delete') {
            $project_id = (int)$_POST['project_id'];
            
            // Check if project has activities
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activities WHERE project_id = ?");
            $stmt->execute([$project_id]);
            $activity_count = $stmt->fetch()['count'];
            
            if ($activity_count > 0) {
                throw new Exception("Cannot delete project with existing activities. Please delete or reassign activities first.");
            }
            
            // Delete project
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            
            $success_message = 'Project deleted successfully!';
            echo '<script>setTimeout(function(){window.location.href=window.location.pathname;},1000);</script>';
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get filter parameters
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
$start_date_filter = isset($_GET['start_date_filter']) ? $_GET['start_date_filter'] : '';
$end_date_filter = isset($_GET['end_date_filter']) ? $_GET['end_date_filter'] : '';

// Build base query
$params = [];
$where = [];

if (!empty($status_filter)) {
    $where[] = 'status = ?';
    $params[] = $status_filter;
}

if (!empty($start_date_filter)) {
    $where[] = 'end_date >= ?';
    $params[] = $start_date_filter;
}

if (!empty($end_date_filter)) {
    $where[] = 'start_date <= ?';
    $params[] = $end_date_filter;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get all projects
$projects = [];
try {
    $sql = "SELECT *, DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as formatted_updated_at FROM projects $where_clause ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error fetching projects: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Personal Monitoring System - Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Main Theme Variables - Matching Notes */
        :root {
            --primary-bg: #0f172a;
            --secondary-bg: #1e293b;
            --accent-color: #64ffda;
            --accent-secondary: #7c3aed;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --text-secondary: #64748b;
            --border-color: #2d3748;
            --card-bg: #1e293b;
            --card-hover-bg: #334155;
            --hover-bg: rgba(100, 255, 218, 0.1);
            --text-white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark-blue: #1e40af;
            --indigo: #4f46e5;
            --purple: #7c3aed;
        }
        
        body {
            background-color: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(100, 255, 218, 0.1) 0%, transparent 50%),
                radial-gradient(at 100% 0%, rgba(121, 40, 202, 0.1) 0%, transparent 50%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-light);
            min-height: 100vh;
            overflow: hidden;
        }

        .main-content {
            padding: 1rem;
            margin-left: 350px;
            max-width: calc(100% - 350px);
            transition: all 0.3s ease;
            height: calc(100vh - 60px);
            overflow: hidden;
        }

        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
                max-width: 100%;
                padding: 0.75rem;
                height: calc(100vh - 60px);
                overflow: hidden;
            }
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            color: var(--text-light);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--accent-color);
        }

        .card-header {
            background: rgba(30, 41, 59, 0.9);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }

        .card-header h5 {
            color: var(--accent-color);
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--accent-color);
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            border-radius: 6px;
        }

        .status-badge {
            padding: 0.35em 0.8em;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .status-badge i {
            font-size: 0.7em;
        }

        .status-high {
            color: #f87171;
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .status-medium {
            color: #fbbf24;
            background-color: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .status-low {
            color: #60a5fa;
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .status-not-started {
            color: #fbbf24;
            background-color: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .status-in-progress {
            color: var(--accent-color);
            background-color: rgba(100, 255, 218, 0.1);
            border: 1px solid rgba(100, 255, 218, 0.2);
        }

        .status-completed {
            color: var(--success);
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-on-hold {
            color: var(--warning);
            background-color: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
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
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            color: #0f172a;
            opacity: 0.95;
        }

        .btn-outline-secondary {
            color: var(--text-muted);
            border-color: var(--border-color);
        }

        .btn-outline-secondary:hover {
            background: var(--hover-bg);
            color: var(--accent-color);
            border-color: var(--accent-color);
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

        /* Filter Section Styles - Matching Notes */
        #filterSection {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            color: var(--text-light);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--accent-color);
        }

        #filterSection:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--accent-color);
        }

        /* Badge Styles - Matching Notes */
        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.6em;
            border-radius: 6px;
        }
        
        .bg-info {
            background-color: var(--info) !important;
            color: var(--text-light) !important;
            border: 1px solid var(--info);
        }

        /* Enhanced Table Styles - Matching Notes */
        .table-container {
            max-height: calc(100vh - 600px);
            min-height: 550px;
            overflow-y: auto;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 10px 10px;
            width: 100%;
        }
        
        .table {
            color: var(--text-light);
            margin: 0;
            width: 100%;
            table-layout: fixed;
            font-size: 0.9rem;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
            color: var(--text-light);
            background-color: rgba(30, 41, 59, 0.9);
            border-bottom: 2px solid var(--accent-color);
            padding: 1.1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(10px);
            white-space: nowrap;
        }
        
        .table td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-light);
            background-color: var(--card-bg);
            transition: all 0.2s ease;
        }
        
        .table td:nth-child(2) {
            white-space: normal;
            word-wrap: break-word;
        }
        
        .table tbody tr:hover {
            background-color: rgba(100, 255, 218, 0.05);
            transform: scale(1.01);
        }

        .table-hover tbody tr:hover {
            background-color: transparent;
        }

        .table-hover tbody tr:hover td {
            color: var(--accent-color);
        }

        .table tr:hover td {
            background: rgba(100, 255, 218, 0.05);
        }

        /* Enhanced Empty State - Matching Notes */
        .table tbody tr td .py-5 {
            padding: 3rem 0;
        }
        
        .table tbody tr td .bi-folder-x {
            color: var(--accent-color);
            opacity: 0.7;
        }

        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            color: var(--accent-color);
            opacity: 0.7;
        }

        /* Button Enhancements - Matching Notes */
        .btn-primary {
            background: var(--info);
            border: 1px solid var(--info);
            color: var(--text-light);
        }
        
        .btn-primary:hover {
            background: var(--dark-blue);
            border-color: var(--dark-blue);
            color: var(--text-light);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        /* Form Enhancements - Matching Notes */
        .form-select-sm {
            background: rgba(16, 32, 56, 0.9);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            border-radius: 6px;
        }
        
        .form-control-sm {
            background: rgba(16, 32, 56, 0.9);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            border-radius: 6px;
        }

        /* Custom scrollbar - Matching Notes */
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #4dffd1;
        }

        /* Responsive Utilities - Matching Notes */
        @media (max-width: 768px) {
            .filters .col-md-3, 
            .filters .col-md-4,
            .filters .col-md-2 {
                margin-bottom: 1rem;
            }
            
            .main-content {
                padding: 0.5rem;
            }
            
            .table-container {
                max-height: calc(100vh - 120px);
                min-height: 350px;
            }
            
            .table {
                font-size: 0.85rem;
            }
            
            .table th, .table td {
                padding: 0.4rem;
            }
        }
        
        @media (max-width: 992px) {
            .table-container {
                max-height: calc(100vh - 130px);
            }
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
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Projects</h4>
                    <div class="d-flex gap-2 align-items-center">
                        <!-- Add Project Button -->
                        <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                            <i class="bi bi-plus-circle me-2"></i>Add Project
                        </button>
                    </div>
                </div>
                            
                            <!-- Success/Error Messages -->
                            <?php if (!empty($success_message)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <?php echo htmlspecialchars($success_message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <?php echo htmlspecialchars($error_message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Filter Section -->
                            <div class="card mb-3 animate__animated animate__fadeIn" id="filterSection">
                                <div class="card-body p-3">
                                    <form method="GET" class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small text-white mb-0">Status</label>
                                            <select name="status_filter" class="form-select form-select-sm">
                                                <option value="">All Status</option>
                                                <option value="Not Started" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                                                <option value="In Progress" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Completed" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                <option value="On Hold" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-white mb-0">From</label>
                                            <input type="date" name="start_date_filter" class="form-control form-control-sm" value="<?php echo isset($_GET['start_date_filter']) ? htmlspecialchars($_GET['start_date_filter']) : ''; ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-white mb-0">To</label>
                                            <input type="date" name="end_date_filter" class="form-control form-control-sm" value="<?php echo isset($_GET['end_date_filter']) ? htmlspecialchars($_GET['end_date_filter']) : ''; ?>">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                                            <div class="d-flex gap-2 w-100">
                                                <a href="projects.php" class="btn btn-custom" id="resetFilters" style="height: 48px;">
                                                    <i class="bi bi-x-circle me-1"></i>Reset
                                                </a>
                                                <button type="submit" class="btn btn-custom" style="height: 48px;">
                                                    <i class="bi bi-funnel me-1"></i>Apply
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Projects Table -->
                            <div class="card animate__animated animate__fadeInUp">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-folder-fill me-2"></i>
                                        Projects Management
                                        <?php if (!empty($status_filter)): ?>
                                            <span class="badge bg-info ms-2">Filtered: <?php echo htmlspecialchars($status_filter); ?></span>
                                        <?php endif; ?>
                                    </h5>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-info">
                                            <?php echo count($projects); ?> <?php echo count($projects) == 1 ? 'Project' : 'Projects'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-container">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%">Title
                                                        <?php if (isset($_GET['status_filter']) && !empty($_GET['status_filter'])): ?>
                                                            <span class="badge bg-info ms-1"><?php echo htmlspecialchars($_GET['status_filter']); ?></span>
                                                        <?php endif; ?>
                                                    </th>
                                                    <th style="width: 35%">Description</th>
                                                    <th style="width: 12%">Status</th>
                                                    <th style="width: 18%">Date Range</th>
                                                    <th style="width: 15%">Modified At</th>
                                                    <th style="width: 10%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($projects)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center py-4">
                                                            <div class="empty-state">
                                                                <i class="bi bi-folder-x"></i>
                                                                <p class="mb-0">No projects found. Start by adding your first project!</p>
                                                                <button type="button" class="btn btn-custom mt-3" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                                                                    <i class="bi bi-plus-circle me-2"></i>Add Your First Project
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($projects as $project): ?>
                                                        <?php 
                                                        // Get activity count for this project
                                                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activities WHERE project_id = ?");
                                                        $stmt->execute([$project['id']]);
                                                        $activity_count = $stmt->fetch()['count'];
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $description = $project['description'] ?? '';
                                                                if (strlen($description) > 150) {
                                                                    echo nl2br(htmlspecialchars(substr($description, 0, 150))) . '...';
                                                                } else {
                                                                    echo nl2br(htmlspecialchars($description));
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <span class="status-badge status-<?php echo str_replace(' ', '-', strtolower($project['status'] ?? 'not-started')); ?>">
                                                                    <?php echo htmlspecialchars($project['status'] ?? 'Not Started'); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $start_date = $project['start_date'] ? new DateTime($project['start_date']) : null;
                                                                $end_date = $project['end_date'] ? new DateTime($project['end_date']) : null;
                                                                
                                                                if ($start_date && $end_date) {
                                                                    if ($start_date->format('Y-m-d') === $end_date->format('Y-m-d')) {
                                                                        // Single day
                                                                        echo $start_date->format('M j, Y');
                                                                    } else if ($start_date->format('Y-m') === $end_date->format('Y-m')) {
                                                                        // Same month, different days
                                                                        echo $start_date->format('M j') . ' - ' . $end_date->format('M j, Y');
                                                                    } else if ($start_date->format('Y') === $end_date->format('Y')) {
                                                                        // Same year, different months
                                                                        echo $start_date->format('M j') . ' - ' . $end_date->format('M j, Y');
                                                                    } else {
                                                                        // Different years
                                                                        echo $start_date->format('M j, Y') . ' - ' . $end_date->format('M j, Y');
                                                                    }
                                                                } else {
                                                                    echo '<span class="text-muted">No dates set</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?php echo date('M d, Y h:i A', strtotime($project['formatted_updated_at'])); ?></td>
                                                            <td class="text-center">
                                                                <div class="d-flex gap-2 justify-content-center">
                                                                    <button type="button" class="btn btn-primary btn-sm" onclick="editProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>', '<?php echo htmlspecialchars($project['description'] ?? ''); ?>', '<?php echo htmlspecialchars($project['start_date'] ?? ''); ?>', '<?php echo htmlspecialchars($project['end_date'] ?? ''); ?>', '<?php echo htmlspecialchars($project['status'] ?? 'Not Started'); ?>')">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteProject(<?php echo $project['id']; ?>)">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
    
    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Add New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label text-white mb-1">Project Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label text-white mb-1">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label text-white mb-1">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label text-white mb-1">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label text-white mb-1">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">Add Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Project Modal -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="project_id" id="edit_project_id">
                        
                        <div class="mb-3">
                            <label for="edit_title" class="form-label text-white mb-1">Project Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label text-white mb-1">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_start_date" class="form-label text-white mb-1">Start Date</label>
                                <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_end_date" class="form-label text-white mb-1">End Date</label>
                                <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_status" class="form-label text-white mb-1">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Project Modal -->
    <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProjectModalLabel">Delete Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="project_id" id="delete_project_id">
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Warning:</strong> This action cannot be undone. Are you sure you want to delete this project?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this project?')">
                            <i class="bi bi-trash"></i> Delete Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function editProject(id, title, description, startDate, endDate, status) {
            document.getElementById('edit_project_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_start_date').value = startDate;
            document.getElementById('edit_end_date').value = endDate;
            document.getElementById('edit_status').value = status;
            
            // Show the edit modal
            const editModal = new bootstrap.Modal(document.getElementById('editProjectModal'));
            editModal.show();
        }
        
        function deleteProject(id) {
            document.getElementById('delete_project_id').value = id;
            
            // Show the delete modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteProjectModal'));
            deleteModal.show();
        }
        
        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.body.appendChild(alertDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }
    </script>
</body>
</html>
