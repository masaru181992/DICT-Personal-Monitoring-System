<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Log the complete POST data for debugging
error_log("POST data: " . print_r($_POST, true));

// Check if required fields are provided
$required_fields = [
    'target_id', 'title', 'category_id', 'target_quantity', 
    'target_date', 'status', 'priority'
];

$missing_fields = [];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    error_log("Missing required fields: " . implode(', ', $missing_fields));
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required fields',
        'missing_fields' => $missing_fields
    ]);
    exit();
}

// Sanitize and validate input
$target_id = (int)$_POST['target_id'];
$user_id = $_SESSION['user_id'];
$title = trim($_POST['title']);
$description = trim($_POST['description'] ?? '');
$category_id = (int)$_POST['category_id'];
$target_quantity = (float)$_POST['target_quantity'];
$quantity_accomplished = (float)($_POST['actual_accomplishments_quantity'] ?? 0);
$unit = trim($_POST['unit'] ?? 'unit(s)');
$target_date = $_POST['target_date'];
$status = trim($_POST['status']);
$priority = trim($_POST['priority']);
$function_type = trim($_POST['function_type'] ?? 'Core Function');

// Validate function type
$valid_function_types = ['Core Function', 'Support Function'];
if (!in_array($function_type, $valid_function_types)) {
    $function_type = 'Core Function'; // Default to Core Function if invalid
}

// Validate status and priority
$valid_statuses = ['Not Started', 'In Progress', 'Completed', 'On Hold', 'Cancelled'];
$valid_priorities = ['Low', 'Medium', 'High'];

if (!in_array($status, $valid_statuses) || !in_array($priority, $valid_priorities)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status or priority']);
    exit();
}

// Log database connection status
error_log("Database connection status: " . ($pdo ? "Connected" : "Not connected"));

try {
    // Begin transaction
    $pdo->beginTransaction();

    // First, verify the target exists and belongs to the user
    $targetCheck = $pdo->prepare("SELECT id FROM ipcr_entries WHERE id = ? AND user_id = ?");
    $targetCheck->execute([$target_id, $user_id]);
    
    if ($targetCheck->rowCount() === 0) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Target not found or access denied']);
        exit();
    }
    
        // Skip category check since we're using a default function_type
    // This is a simplified version that doesn't require the categories table
    
    // Set default priority if not set
    if (empty($priority)) {
        $priority = 'Medium';
    }
    
    // Update the target
    $updateStmt = $pdo->prepare("
        UPDATE ipcr_entries 
        SET 
            success_indicators = ?,
            actual_accomplishments = ?,
            function_type = ?,
            success_indicators_quantity = ?,
            actual_accomplishments_quantity = ?,
            year = ?,
            semester = ?,
            updated_at = NOW()
        WHERE id = ? AND user_id = ?
    ");
    
    // Get year from form or use current year as fallback
    $year = !empty($_POST['year']) ? (int)$_POST['year'] : date('Y');
    
    // Default to 1st semester if not specified
    $semester = !empty($_POST['semester']) ? $_POST['semester'] : '1st';
    
    $updateStmt->execute([
        $title,  // success_indicators
        $description,  // actual_accomplishments
        $function_type,  // function_type from form
        $target_quantity,  // success_indicators_quantity
        $quantity_accomplished,  // actual_accomplishments_quantity
        $year,  // year
        $semester,  // semester
        $target_id,
        $user_id
    ]);
    
    // Handle activities if any are provided
    if (isset($_POST['activities']) && is_array($_POST['activities'])) {
        // First, delete existing activity relationships
        $deleteStmt = $pdo->prepare("DELETE FROM ipcr_activities WHERE ipcr_entry_id = ?");
        $deleteStmt->execute([$target_id]);
        
        // Prepare insert statement for new relationships
        $insertStmt = $pdo->prepare("INSERT INTO ipcr_activities (ipcr_entry_id, activity_id) VALUES (?, ?)");
        
        // Insert each selected activity
        foreach ($_POST['activities'] as $activityId) {
            $activityId = (int)$activityId;
            if ($activityId > 0) {  // Only insert valid activity IDs
                $insertStmt->execute([$target_id, $activityId]);
            }
        }
    }
    
    // Commit the transaction
    $pdo->commit();
    
    // Return success response with activity information
    $activities = [];
    if (isset($_POST['activities'])) {
        $activities = array_map('intval', $_POST['activities']);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Target updated successfully',
        'target_id' => $target_id,
        'activities_count' => count($activities),
        'activities' => $activities
    ]);
    
} catch (PDOException $e) {
    // Rollback the transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log detailed error information
    $errorInfo = [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'post_data' => $_POST,
        'session' => $_SESSION
    ];
    
    error_log("Error updating IPCR target: " . print_r($errorInfo, true));
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while updating the target',
        'error' => $e->getMessage(),
        'error_info' => $errorInfo
    ]);
}
?>
