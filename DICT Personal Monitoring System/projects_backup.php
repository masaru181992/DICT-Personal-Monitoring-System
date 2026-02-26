<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize variables to prevent undefined variable errors
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'Not Started';
    
    try {
        if ($_POST['action'] == 'add') {
            // Add new project
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $start_date, $end_date, $status])) {
                $success_message = "Project added successfully!";
            } else {
                $error_message = "Error adding project.";
            }
        } elseif ($_POST['action'] == 'update' && isset($_POST['project_id'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $status = $_POST['status'];
            $project_id = (int)$_POST['project_id'];
            
            // Check if project can be marked as completed
            if ($status === 'Completed') {
                if (!canMarkProjectAsCompleted($project_id, $pdo)) {
                    $error_message = "Cannot mark project as completed. There are pending/incomplete activities, transactions, claims, offset requests, or notes associated with this project.";
                    // Keep current status
                    $status = getCurrentProjectStatus($project_id, $pdo);
                }
            }
            
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $start_date, $end_date, $status, $project_id])) {
                $success_message = "Project updated successfully!";
            } else {
                $error_message = "Error updating project.";
            }
        } elseif ($_POST['action'] == 'delete' && isset($_POST['project_id'])) {
            $project_id = (int)$_POST['project_id'];
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            if ($stmt->execute([$project_id])) {
                $success_message = "Project deleted successfully!";
            } else {
                $error_message = "Error deleting project.";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Get project ID from URL
$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get project details
$project = null;
$activities_count = ['pending' => 0, 'in_progress' => 0, 'completed' => 0, 'total' => 0];
$pending_transactions = 0;
$pending_notes = 0;

if ($projectId > 0) {
    try {
        // Get project details
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch();
        
        // Get activities count for this project
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM activities 
            WHERE project_id = ? AND status IN ('not started', 'in progress')
        ");
        $stmt->execute([$projectId]);
        $pending_activities = $stmt->fetch()['count'];
        
        // Check for pending transactions (TEV claims, offset requests, overtime requests)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM (
                SELECT id FROM tev_claims WHERE project_id = ? AND status = 'pending'
                UNION ALL
                SELECT id FROM offset_requests WHERE project_id = ? AND status = 'pending'
                UNION ALL
                SELECT id FROM overtime_requests WHERE project_id = ? AND status = 'pending'
            ) as pending_transactions
        ");
        $stmt->execute([$projectId, $projectId, $projectId]);
        $pending_transactions = $stmt->fetch()['count'];
        
        // Check for pending notes
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM notes 
            WHERE project_id = ? AND status IN ('pending', 'in_progress')
        ");
        $stmt->execute([$projectId]);
        $pending_notes = $stmt->fetch()['count'];
        
        // Get total activities count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activities WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $activities_count['total'] = $stmt->fetch()['count'];
        
        // Get completed activities count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activities WHERE project_id = ? AND status = 'completed'");
        $stmt->execute([$projectId]);
        $activities_count['completed'] = $stmt->fetch()['count'];
        
        // Get in progress activities count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activities WHERE project_id = ? AND status = 'in progress'");
        $stmt->execute([$projectId]);
        $activities_count['in_progress'] = $stmt->fetch()['count'];
        
        $activities_count['pending'] = $pending_notes;
        
    } catch (PDOException $e) {
        $error_message = "Error fetching project details: " . $e->getMessage();
    }
}

// Get all projects
$projects = [];
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error fetching projects: " . $e->getMessage();
}

// Helper functions
function canMarkProjectAsCompleted($projectId, $pdo) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM (
            SELECT id FROM activities WHERE project_id = ? AND status IN ('not started', 'in progress')
            UNION ALL
            SELECT id FROM tev_claims WHERE project_id = ? AND status = 'pending'
            UNION ALL
            SELECT id FROM offset_requests WHERE project_id = ? AND status = 'pending'
            UNION ALL
            SELECT id FROM overtime_requests WHERE project_id = ? AND status = 'pending'
            UNION ALL
            SELECT id FROM notes WHERE project_id = ? AND status IN ('pending', 'in_progress')
        ) as pending_items
    ");
    $stmt->execute([$projectId, $projectId, $projectId, $projectId, $projectId]);
    $result = $stmt->fetch();
    return $result['count'] == 0;
}

function getCurrentProjectStatus($projectId, $pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            CASE 
                WHEN EXISTS(SELECT 1 FROM activities WHERE project_id = ? AND status IN ('not started', 'in progress')) 
                THEN 'In Progress'
                WHEN EXISTS(SELECT 1 FROM activities WHERE project_id = ? AND status = 'completed')
                THEN 'Completed'
                ELSE 'Not Started'
            END as status
    ");
    $stmt->execute([$projectId, $projectId]);
    $result = $stmt->fetch();
    return $result['status'];
}

$page_title = "Projects";
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
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main Content -->
            <div class="main-content animate__animated animate__fadeIn">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Projects</h4>
                            
                            <!-- Success/Error Messages -->
                            <?php if ($success_message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $success_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Add Project Button -->
                            <div class="mb-4">
                                <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                                    <i class="bi bi-plus-circle me-2"></i>Add New Project
                                </button>
                            </div>
                            
                            <!-- Projects Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Project Title</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($projects)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No projects found.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($projects as $proj): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($proj['title']); ?></td>
                                                    <td><?php echo htmlspecialchars(substr($proj['description'], 0, 100)); ?><?php echo strlen($proj['description']) > 100 ? '...' : ''; ?></td>
                                                    <td><?php echo htmlspecialchars($proj['start_date']); ?></td>
                                                    <td><?php echo htmlspecialchars($proj['end_date']); ?></td>
                                                    <td>
                                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $proj['status'])); ?>">
                                                            <?php echo htmlspecialchars($proj['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                    onclick="editProject(<?php echo $proj['id']; ?>, '<?php echo htmlspecialchars($proj['title']); ?>', '<?php echo htmlspecialchars($proj['description']); ?>', '<?php echo $proj['start_date']; ?>', '<?php echo $proj['end_date']; ?>', '<?php echo $proj['status']; ?>')"
                                                                    data-bs-toggle="modal" data-bs-target="#editProjectModal">
                                                                <i class="bi bi-pencil"></i> Edit
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="deleteProject(<?php echo $proj['id']; ?>)"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                                                                <i class="bi bi-trash"></i> Delete
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
                </div>
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
                        
                        <?php if ($project && $pending_transactions > 0): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-shield-exclamation me-2"></i>
                                <strong>Cannot Delete:</strong> This project has pending transactions (activities, claims, requests, or notes) that must be completed or cancelled first.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" 
                                <?php echo ($pending_transactions > 0) ? 'disabled' : ''; ?>
                                onclick="return confirm('Are you sure you want to delete this project?')">
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
        }
        
        function deleteProject(id) {
            document.getElementById('delete_project_id').value = id;
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
        
        <?php if ($success_message): ?>
            showAlert('<?php echo $success_message; ?>', 'success');
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            showAlert('<?php echo $error_message; ?>', 'danger');
        <?php endif; ?>
    </script>
</body>
</html>
