<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

// Start session and include database configuration
session_start();
require_once 'config/database.php';

// Log the start of the script
error_log('save_tev_claim.php started. POST data: ' . print_r($_POST, true));

// Function to send JSON response and exit
function sendResponse($success, $message = '', $data = null) {
    $response = ['success' => $success];
    if ($message) $response['message'] = $message;
    if ($data !== null) $response['data'] = $data;
    
    // Ensure no output before this
    if (headers_sent()) {
        // If headers already sent, log the error
        error_log('Headers already sent when trying to send JSON response');
    }
    
    echo json_encode($response);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    sendResponse(false, 'Unauthorized: Please log in to continue');
}

// Get action (add or edit)
$action = $_POST['action'] ?? '';
$errors = [];

// Common validation
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$activity_id = isset($_POST['activity_id']) ? intval($_POST['activity_id']) : 0;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$purpose = trim($_POST['purpose'] ?? '');
$status = trim($_POST['status'] ?? 'Draft');
$google_drive_link = trim($_POST['google_drive_link'] ?? '');
$user_id = $_SESSION['user_id'];

// Get user info for department
$department = 'DICT'; // Default value
$employee_name = '';

try {
    // Get user info - using 'name' column instead of 'fullname'
    try {
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        if (!$stmt) {
            throw new PDOException('Prepare failed: ' . implode(', ', $pdo->errorInfo()));
        }
        
        $result = $stmt->execute([$user_id]);
        if ($result === false) {
            throw new PDOException('Execute failed: ' . implode(', ', $stmt->errorInfo()));
        }
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log('User data: ' . print_r($user, true));
        
        if ($user && !empty($user['name'])) {
            $employee_name = $user['name'];
        } else {
            // Fallback to a default name if name is not set
            $employee_name = 'User ' . $user_id;
        }
    } catch (PDOException $e) {
        error_log('Error getting user info: ' . $e->getMessage());
        $employee_name = 'User ' . $user_id; // Fallback
    }
    
    // Get project title with error handling
    $project_title = '';
    if ($project_id) {
        try {
            $stmt = $pdo->prepare("SELECT title FROM projects WHERE id = ?");
            if (!$stmt) {
                throw new PDOException('Prepare failed: ' . implode(', ', $pdo->errorInfo()));
            }
            
            $result = $stmt->execute([$project_id]);
            if ($result === false) {
                throw new PDOException('Execute failed: ' . implode(', ', $stmt->errorInfo()));
            }
            
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log('Project data: ' . print_r($project, true));
            
            if ($project && !empty($project['title'])) {
                $project_title = $project['title'];
            }
        } catch (PDOException $e) {
            error_log('Error getting project info: ' . $e->getMessage());
            // Continue with empty project title
        }
    }

    // Handle different actions
    if ($action === 'add') {
        // Insert new TEV claim
        $stmt = $pdo->prepare("
            INSERT INTO tev_claims 
            (employee_name, department, claim_date, purpose, amount, status, project_id, project_title, activity_id, google_drive_link, created_by, created_at) 
            VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $employee_name,
            $department,
            $purpose,
            $amount,
            $status,
            $project_id,
            $project_title,
            $activity_id,
            $google_drive_link,
            $user_id
        ]);
        
        if ($result) {
            $claim_id = $pdo->lastInsertId();
            sendResponse(true, 'TEV claim added successfully', ['id' => $claim_id]);
        } else {
            sendResponse(false, 'Failed to add TEV claim');
        }
        
    } elseif ($action === 'edit' && isset($_POST['claim_id'])) {
        $claim_id = intval($_POST['claim_id']);
        
        // Verify ownership before updating
        $stmt = $pdo->prepare("SELECT id FROM tev_claims WHERE id = ? AND created_by = ?");
        $stmt->execute([$claim_id, $user_id]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            sendResponse(false, 'You are not authorized to edit this claim');
        }
        
        // Update existing TEV claim
        $stmt = $pdo->prepare("
            UPDATE tev_claims 
            SET purpose = ?, 
                amount = ?, 
                status = ?, 
                project_id = ?, 
                project_title = ?, 
                activity_id = ?,
                google_drive_link = ?,
                updated_at = NOW()
            WHERE id = ? AND created_by = ?
        ");
        
        $result = $stmt->execute([
            $purpose,
            $amount,
            $status,
            $project_id,
            $project_title,
            $activity_id,
            $google_drive_link,
            $claim_id,
            $user_id
        ]);
        
        if ($result) {
            sendResponse(true, 'TEV claim updated successfully', ['id' => $claim_id]);
        } else {
            sendResponse(false, 'No changes made or failed to update TEV claim');
        }
        
    } else {
        http_response_code(400);
        sendResponse(false, 'Invalid action or missing parameters');
    }
    
} catch (PDOException $e) {
    error_log('Database error in save_tev_claim.php: ' . $e->getMessage());
    http_response_code(500);
    sendResponse(false, 'Database error: ' . $e->getMessage());
    
} catch (Exception $e) {
    error_log('Error in save_tev_claim.php: ' . $e->getMessage());
    http_response_code(500);
    sendResponse(false, 'An error occurred: ' . $e->getMessage());
}
