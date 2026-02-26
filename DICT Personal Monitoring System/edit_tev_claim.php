<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$claim_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$error_message = '';
$success_message = '';

// Fetch claim details
$claim = null;
try {
    $stmt = $pdo->prepare("
        SELECT * FROM tev_claims 
        WHERE id = ? AND created_by = ?
    ");
    $stmt->execute([$claim_id, $user_id]);
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$claim) {
        header("Location: tev_claims.php");
        exit();
    }
    
} catch (Exception $e) {
    $error_message = "Error fetching claim: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $purpose = trim($_POST['purpose']);
        $amount = floatval($_POST['amount']);
        $project_id = !empty($_POST['project_id']) ? intval($_POST['project_id']) : null;
        $activity_id = !empty($_POST['activity_id']) ? intval($_POST['activity_id']) : null;
        
        // Basic validation
        if (empty($purpose) || $amount <= 0 || empty($project_id) || empty($activity_id)) {
            throw new Exception("Please fill in all required fields with valid data.");
        }
        
        // Get project title
        $project_title = null;
        if ($project_id) {
            $stmt = $pdo->prepare("SELECT title FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch();
            $project_title = $project ? $project['title'] : null;
        }
        
        // Update TEV claim
        $stmt = $pdo->prepare("
            UPDATE tev_claims 
            SET purpose = ?, 
                amount = ?, 
                project_id = ?, 
                project_title = ?, 
                activity_id = ?,
                updated_at = NOW()
            WHERE id = ? AND created_by = ?
        ");
        
        $stmt->execute([
            $purpose,
            $amount,
            $project_id,
            $project_title,
            $activity_id,
            $claim_id,
            $user_id
        ]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "TEV claim updated successfully!";
            header("Location: tev_claims.php");
            exit();
        } else {
            throw new Exception("No changes were made or you don't have permission to edit this claim.");
        }
        
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch projects for dropdown
try {
    $stmt = $pdo->query("SELECT id, title FROM projects WHERE status IN ('In Progress', 'Not Started') ORDER BY title");
    $projects = $stmt->fetchAll();
} catch (Exception $e) {
    $error_message = "Error fetching projects: " . $e->getMessage();
    $projects = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit TEV Claim - The Personal Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-bg: #0f172a;
            --secondary-bg: #1e293b;
            --accent-color: #64ffda;
            --accent-secondary: #7c3aed;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --card-bg: #1e293b;
            --card-hover: #334155;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --border-color: #334155;
        }

        body {
            background-color: var(--primary-bg);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: var(--secondary-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }

        .form-control, .form-select {
            background-color: var(--primary-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--primary-bg);
            color: var(--text-primary);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(100, 255, 218, 0.25);
        }

        .btn-custom {
            background-color: var(--accent-color);
            color: #0f172a;
            border: none;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #4fd1c5;
            transform: translateY(-2px);
        }

        .btn-outline-custom {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-outline-custom:hover {
            background-color: var(--accent-color);
            color: #0f172a;
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
                    <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">
                        <i class="bi bi-receipt me-2"></i>Edit TEV Claim
                    </h4>
                    <a href="tev_claims.php" class="btn btn-outline-custom">
                        <i class="bi bi-arrow-left"></i> Back to TEV Claims
                    </a>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success animate__animated animate__fadeIn">
                        <?php 
                        echo $_SESSION['success_message']; 
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($claim): ?>
                <div class="card animate__animated animate__fadeInUp">
                    <div class="card-header">
                        <h5 class="mb-0">Edit TEV Claim - <?php echo htmlspecialchars($claim['claim_reference']); ?></h5>
                    </div>
                    <div class="card-body">
                        <form id="editTevClaimForm" method="POST" action="">
                            <input type="hidden" name="action" value="update">
                            
                            <div class="mb-3">
                                <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="purpose" name="purpose" rows="3" required><?php echo htmlspecialchars($claim['purpose']); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="0.01" min="0.01" value="<?php echo htmlspecialchars($claim['amount']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                                    <select class="form-select" id="project_id" name="project_id" required>
                                        <option value="">Select Project</option>
                                        <?php foreach ($projects as $project): ?>
                                            <option value="<?php echo $project['id']; ?>" 
                                                <?php echo $project['id'] == $claim['project_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($project['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="activity_id" class="form-label">Activity <span class="text-danger">*</span></label>
                                <select class="form-select" id="activity_id" name="activity_id" required>
                                    <option value="">Select Project First</option>
                                    <!-- Will be populated by JavaScript -->
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="tev_claims.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-custom">
                                    <i class="bi bi-check-circle me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const projectId = <?php echo $claim ? $claim['project_id'] : 'null'; ?>;
            const activityId = <?php echo $claim ? $claim['activity_id'] : 'null'; ?>;
            
            // Load activities when page loads if project is already selected
            if (projectId) {
                loadActivities(projectId, activityId);
            }
            
            // Load activities when project changes
            document.getElementById('project_id').addEventListener('change', function() {
                const projectId = this.value;
                loadActivities(projectId);
            });
            
            function loadActivities(projectId, selectedActivityId = null) {
                if (!projectId) {
                    document.getElementById('activity_id').innerHTML = '<option value="">Select Project First</option>';
                    return;
                }
                
                fetch(`get_activities.php?project_id=${projectId}`)
                    .then(response => response.json())
                    .then(data => {
                        const activitySelect = document.getElementById('activity_id');
                        let options = '<option value="">Select Activity</option>';
                        
                        if (data.success && data.activities.length > 0) {
                            data.activities.forEach(activity => {
                                const selected = (selectedActivityId && activity.id == selectedActivityId) ? 'selected' : '';
                                options += `<option value="${activity.id}" ${selected}>${activity.title}</option>`;
                            });
                        } else {
                            options = '<option value="">No activities found for this project</option>';
                        }
                        
                        activitySelect.innerHTML = options;
                    })
                    .catch(error => {
                        console.error('Error loading activities:', error);
                        document.getElementById('activity_id').innerHTML = '<option value="">Error loading activities</option>';
                    });
            }
            
            // Form validation
            document.getElementById('editTevClaimForm').addEventListener('submit', function(e) {
                const amount = parseFloat(document.getElementById('amount').value);
                if (isNaN(amount) || amount <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid amount greater than 0');
                    return false;
                }
                
                const projectId = document.getElementById('project_id').value;
                const activityId = document.getElementById('activity_id').value;
                
                if (!projectId || !activityId) {
                    e.preventDefault();
                    alert('Please select both project and activity');
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>
