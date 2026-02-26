<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle note creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_note') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $priority = in_array($_POST['priority'] ?? '', ['low', 'medium', 'high']) ? $_POST['priority'] : 'medium';
        $project_id = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : null;
        $user_id = $_SESSION['user_id'];
        
        // Set default status to 'active' if not provided or invalid
        $status = 'active';
        if (isset($_POST['status']) && in_array($_POST['status'], ['active', 'completed', 'archived'])) {
            $status = $_POST['status'];
        }
        
        // Basic validation
        if (empty($title)) {
            throw new Exception('Title is required');
        }
        
        if (empty($content)) {
            throw new Exception('Content is required');
        }
        
        // Insert into database
        $stmt = $pdo->prepare("
            INSERT INTO notes (user_id, project_id, title, content, priority, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $user_id,
            $project_id,
            $title,
            $content,
            $priority,
            $status
        ]);
        
        $response['success'] = true;
        $response['message'] = 'Note added successfully!';
        $response['redirect'] = 'notes.php';
        
        // Return JSON response for AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // For non-AJAX requests, redirect with success message
        $_SESSION['success_message'] = $response['message'];
        header('Location: notes.php');
        exit();
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
        
        // Return JSON response for AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode($response);
            exit();
        }
        
        // For non-AJAX requests, show error
        $error_message = $response['message'];
    }
}

// Handle success message from redirect
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Initialize filter variables
$priority_filter = $_GET['priority'] ?? '';
$project_filter = $_GET['project_id'] ?? 0;
$status_filter = $_GET['status'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Build query with filters
$query = "SELECT n.*, u.full_name, p.title as project_title 
         FROM notes n 
         LEFT JOIN users u ON n.user_id = u.id 
         LEFT JOIN projects p ON n.project_id = p.id 
         WHERE 1=1";
$params = [];

// Apply filters
if (!empty($priority_filter)) {
    $query .= " AND n.priority = ?";
    $params[] = $priority_filter;
}

if ($project_filter > 0) {
    $query .= " AND n.project_id = ?";
    $params[] = $project_filter;
}

if (!empty($status_filter)) {
    // Convert status to match database values
    $status_filter = strtolower($status_filter);
    $status_mapping = [
        'in_progress' => 'active',
        'completed' => 'completed',
        'archived' => 'archived'
    ];
    
    if (array_key_exists($status_filter, $status_mapping)) {
        $query .= " AND n.status = ?";
        $params[] = $status_mapping[$status_filter];
    }
}

if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND n.created_at BETWEEN ? AND ?";
    $params[] = $start_date . ' 00:00:00';
    $params[] = $end_date . ' 23:59:59';
}

// Execute the query using safeQuery
$stmt = safeQuery($pdo, $query, $params);
$notes = $stmt ? $stmt->fetchAll() : [];

// Fetch projects for linking
$projects = [];
$projectsStmt = safeQuery($pdo, "SELECT id, title FROM projects WHERE status != 'completed'");
if ($projectsStmt) {
    $projects = $projectsStmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Personal Monitoring System - Notes Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Main Theme Variables */
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
        }

        .main-content {
            padding: 2rem;
            margin-left: 350px;
            max-width: calc(100% - 350px);
            transition: all 0.3s ease;
        }

        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
                max-width: 100%;
                padding: 1.5rem;
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

        .form-label {
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Table Styling to match projects.php */
        .table {
            color: var(--text-light);
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Table Header Colors */
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

        /* Table Cell Colors */
        .table td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-light);
            background-color: var(--card-bg);
            transition: all 0.2s ease;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover td {
            background-color: rgba(100, 255, 218, 0.05);
            transform: scale(1.01);
        }

        .table-hover tbody tr:hover {
            background-color: transparent;
        }

        .table-hover tbody tr:hover td {
            color: var(--accent-color);
        }

        .table-responsive {
            border-radius: 0 0 10px 10px;
            overflow: hidden;
        }

        .table tr:hover td {
            background: rgba(100, 255, 218, 0.05);
        }

        /* Empty State */
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

        /* Responsive Utilities */
        @media (max-width: 768px) {
            .filters .col-md-3, 
            .filters .col-md-4,
            .filters .col-md-2 {
                margin-bottom: 1rem;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row h-100">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main Content -->
            <div class="main-content animate__animated animate__fadeIn">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Notes Management</h4>    
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="bi bi-plus-circle me-2"></i>Add Note
                        </button>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="card mb-4 animate__animated animate__fadeInUp">
                    <div class="card-body">
                        <form action="" method="GET" id="filterForm" class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="">All Priorities</option>
                                    <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
                                    <option value="medium" <?php echo $priority_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="project_id" class="form-label">Project</label>
                                <select class="form-select" id="project_id" name="project_id">
                                    <option value="0">All Projects</option>
                                    <?php foreach ($projects as $project): ?>
                                    <option value="<?php echo $project['id']; ?>" <?php echo $project_filter == $project['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($project['title']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>
                                        <span class="badge bg-warning">In Progress</span>
                                    </option>
                                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>
                                        <span class="badge bg-success">Completed</span>
                                    </option>
                                    <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>
                                        <span class="badge bg-secondary">Archived</span>
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-3 d-flex align-items-end justify-content-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="button" class="btn btn-outline-secondary flex-grow-1" id="resetFilters">
                                        <i class="bi bi-x-circle me-1"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-custom flex-grow-1">
                                        <i class="bi bi-filter me-1"></i>Apply
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($success_message) && !empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message) && !empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Notes Table -->
                <div class="card animate__animated animate__fadeInUp">
                    <div class="card-body p-0">
                        <div class="table-responsive table-container" style="max-height: 70vh; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 20%">Title</th>
                                        <th style="width: 35%">Content</th>
                                        <th style="width: 12%">Priority</th>
                                        <th style="width: 18%">Project</th>
                                        <th style="width: 15%">Modified At</th>
                                        <th style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notes as $note): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($note['title']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($note['content'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $note['priority'] === 'high' ? 'bg-danger' : ($note['priority'] === 'medium' ? 'bg-warning' : 'bg-info'); ?>">
                                                <?php echo ucfirst($note['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($note['project_id']): ?>
                                                <span class="badge bg-primary">
                                                    <?php echo htmlspecialchars($note['project_title']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($note['updated_at'])); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button type="button" class="btn btn-primary btn-sm edit-note" 
                                                    data-id="<?php echo $note['id']; ?>" 
                                                    title="Edit Note">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm delete-note" 
                                                    data-id="<?php echo $note['id']; ?>" 
                                                    title="Delete Note">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($notes)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No notes found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addNoteForm" method="POST" action="">
                        <input type="hidden" name="action" value="add_note">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label">Project (Optional)</label>
                                <select class="form-select" id="project_id" name="project_id">
                                    <option value="">Select a project</option>
                                    <?php if (!empty($projects)): ?>
                                        <?php foreach ($projects as $project): ?>
                                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No projects available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="saveNoteBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                Save Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Note Modal -->
    <div class="modal fade" id="editNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editNoteForm" method="POST">
                        <input type="hidden" name="action" value="update_note">
                        <input type="hidden" name="id" id="editNoteId">
                        
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editContent" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editContent" name="content" rows="5" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPriority" class="form-label">Priority</label>
                                <select class="form-select" id="editPriority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="editProject" class="form-label">Related Project (Optional)</label>
                                <select class="form-select" id="editProject" name="project_id">
                                    <option value="">Select a project</option>
                                    <?php if (!empty($projects)): ?>
                                        <?php foreach ($projects as $project): ?>
                                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No projects available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveNoteChanges">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Operation completed successfully!
            </div>
        </div>
    </div>
    
    <!-- Error Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="errorToastMessage">
                An error occurred while processing your request.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handle add note form submission
        document.getElementById('addNoteForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const spinner = submitBtn.querySelector('.spinner-border');
            const originalBtnText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            
            try {
                const response = await fetch('notes.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    showToast('success', 'Success', result.message);
                    
                    // Close modal and reset form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addNoteModal'));
                    modal.hide();
                    form.reset();
                    
                    // Reload the page after a short delay
                    setTimeout(() => {
                        window.location.href = result.redirect || 'notes.php';
                    }, 1500);
                } else {
                    // Show error message
                    showToast('error', 'Error', result.message || 'Failed to save note');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error', 'An error occurred while saving the note');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
        
        // Show toast message
        function showToast(type, title, message) {
            const toastContainer = document.createElement('div');
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '1100';
            
            const toast = document.createElement('div');
            toast.className = `toast show`;
            toast.role = 'alert';
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            const toastHeader = document.createElement('div');
            toastHeader.className = `toast-header bg-${type === 'success' ? 'success' : 'danger'} text-white`;
            
            const toastTitle = document.createElement('strong');
            toastTitle.className = 'me-auto';
            toastTitle.textContent = title;
            
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'btn-close btn-close-white';
            closeBtn.setAttribute('data-bs-dismiss', 'toast');
            closeBtn.setAttribute('aria-label', 'Close');
            
            const toastBody = document.createElement('div');
            toastBody.className = 'toast-body';
            toastBody.textContent = message;
            
            toastHeader.appendChild(toastTitle);
            toastHeader.appendChild(closeBtn);
            
            toast.appendChild(toastHeader);
            toast.appendChild(toastBody);
            
            toastContainer.appendChild(toast);
            document.body.appendChild(toastContainer);
            
            // Auto-remove toast after 5 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toastContainer.remove();
                }, 300);
            }, 5000);
            
            // Close button functionality
            closeBtn.addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toastContainer.remove();
                }, 300);
            });
        }
        
        // Show success message if it exists
        <?php if (isset($success_message)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('success', 'Success', '<?= addslashes($success_message) ?>');
        });
        <?php endif; ?>
        
        // Set current date
        document.addEventListener('DOMContentLoaded', function() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-US', options);

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Initialize datepickers
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (!input.value) {
                    const today = new Date().toISOString().split('T')[0];
                    input.value = today;
                }
            });

        });

        // Handle delete note button click
        document.addEventListener('click', async function(e) {
            if (e.target.closest('.delete-note')) {
                const button = e.target.closest('.delete-note');
                const noteId = button.dataset.id;
                
                if (!confirm('Are you sure you want to delete this note? This action cannot be undone.')) {
                    return;
                }
                
                try {
                    const originalHtml = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    
                    const response = await fetch('api/delete_note.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${noteId}`
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Show success message
                        const toast = new bootstrap.Toast(document.getElementById('successToast'));
                        const toastMessage = document.getElementById('toastMessage');
                        toastMessage.textContent = 'Note deleted successfully!';
                        toast.show();
                        
                        // Remove the deleted note row from the table
                        button.closest('tr').remove();
                    } else {
                        throw new Error(result.message || 'Failed to delete note');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    const toast = new bootstrap.Toast(document.getElementById('errorToast'));
                    const toastMessage = document.getElementById('errorToastMessage');
                    toastMessage.textContent = 'Error: ' + (error.message || 'Failed to delete note');
                    toast.show();
                    
                    // Reset button state on error
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<i class="bi bi-trash"></i>';
                    }
                }
            }
        });

        // Handle edit note button click
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-note')) {
                const button = e.target.closest('.edit-note');
                const noteId = button.dataset.id;
                
                // Show loading state
                const originalHtml = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                
                // Fetch note details
                fetch(`api/get_note.php?id=${noteId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            const note = data.data;
                            
                            // Populate form
                            document.getElementById('editNoteId').value = note.id;
                            document.getElementById('editTitle').value = note.title;
                            document.getElementById('editContent').value = note.content;
                            document.getElementById('editPriority').value = note.priority;
                            
                            if (note.project_id) {
                                document.getElementById('editProject').value = note.project_id;
                            }
                            
                            // Show modal
                            const modal = new bootstrap.Modal(document.getElementById('editNoteModal'));
                            modal.show();
                        } else {
                            throw new Error(data.message || 'Failed to load note');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const toast = new bootstrap.Toast(document.getElementById('errorToast'));
                        const toastMessage = document.getElementById('errorToastMessage');
                        toastMessage.textContent = 'Error: ' + (error.message || 'Failed to load note');
                        toast.show();
                    })
                    .finally(() => {
                        button.disabled = false;
                        button.innerHTML = originalHtml;
                    });
            }
        });

        // Handle save note changes
        document.getElementById('saveNoteChanges')?.addEventListener('click', function() {
            const form = document.getElementById('editNoteForm');
            const formData = new FormData(form);
            const submitBtn = document.getElementById('saveNoteChanges');
            const originalBtnText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            
            // Send update request
            fetch('api/update_note.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const toast = new bootstrap.Toast(document.getElementById('successToast'));
                    const toastMessage = document.getElementById('toastMessage');
                    toastMessage.textContent = 'Note updated successfully!';
                    toast.show();
                    
                    // Close modal and reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editNoteModal'));
                    modal.hide();
                    
                    // Reload after a short delay
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Failed to update note');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const toast = new bootstrap.Toast(document.getElementById('errorToast'));
                const toastMessage = document.getElementById('errorToastMessage');
                toastMessage.textContent = 'Error: ' + (error.message || 'Failed to update note');
                toast.show();
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
        
        // Handle reset filters button
        document.getElementById('resetFilters')?.addEventListener('click', function() {
            // Reset all form fields
            document.getElementById('priority').value = '';
            document.getElementById('project_id').value = '0';
            document.getElementById('status').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            
            // Submit the form to refresh with no filters
            document.getElementById('filterForm').submit();
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            // Set default date to current date for all date inputs
            const dateInputs = elements.dateInputs;
            const today = new Date().toISOString().split('T')[0];
            
            dateInputs.forEach(input => {
                input.min = today;
                input.value = today;
                
                // Add change event listener to validate dates
                input.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const currentDate = new Date();
                    
                    if (selectedDate < currentDate) {
                        this.value = today;
                        alert('Please select a date from today onwards');
                    }
                });
            });
            
            initializeEventListeners();
        });

        // Initialize event listeners
        function initializeEventListeners() {
            // Save note button click
            elements.saveNoteBtn.addEventListener('click', handleSaveNote);



            // Toggle view function
            elements.toggleView.addEventListener('click', () => {
                elements.notesGrid.classList.toggle('d-none');
                elements.notesList.classList.toggle('d-none');
                const icon = elements.toggleView.querySelector('i');
                icon.classList.toggle('bi-grid');
                icon.classList.toggle('bi-list');
            });

            // Filter listeners
            elements.searchInput.addEventListener('input', filterNotes);
            elements.priorityFilter.addEventListener('change', filterNotes);
            elements.projectFilter.addEventListener('change', filterNotes);

            // Reset form when modal is closed
            elements.addNoteModal.addEventListener('hidden.bs.modal', () => {
                elements.noteForm.reset();
                currentNoteId = null;
                elements.saveNoteBtn.textContent = 'Save Note';
            });


        }

        // Handle save note (for new notes)
        async function handleSaveNote() {
            const formData = new FormData(elements.noteForm);
            const noteData = {
                title: formData.get('title'),
                content: formData.get('content'),
                priority: formData.get('priority'),
                project_id: formData.get('project_id') || null,
                reminder_date: formData.get('reminder_date') || null
            };

            try {
                const response = await fetch('api/save_note.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(noteData)
                });

                const result = await response.json();

                if (result.success) {
                    // Close modal and refresh page
                    const modal = bootstrap.Modal.getInstance(elements.addNoteModal);
                    modal.hide();
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Failed to save note');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }





        // Filter notes function
        function filterNotes() {
            const searchTerm = elements.searchInput.value.toLowerCase();
            const priority = elements.priorityFilter.value.toLowerCase();
            const projectId = elements.projectFilter.value;
            
            const notes = document.querySelectorAll('.note-card');
            notes.forEach(note => {
                const title = note.querySelector('h5').textContent.toLowerCase();
                const content = note.querySelector('p').textContent.toLowerCase();
                const notePriority = note.dataset.priority;
                const noteProject = note.querySelector('.note-project')?.dataset.projectId || '';

                const matchesSearch = title.includes(searchTerm) || content.includes(searchTerm);
                const matchesPriority = !priority || notePriority === priority;
                const matchesProject = !projectId || noteProject === projectId;

                note.style.display = matchesSearch && matchesPriority && matchesProject ? '' : 'none';
            });
        }
    </script>
</body>
</html> 