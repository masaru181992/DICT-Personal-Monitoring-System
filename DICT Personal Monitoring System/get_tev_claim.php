<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if claim ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Claim ID is required']);
    exit();
}

$claim_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

try {
    // Get claim details with project and activity info
    $stmt = $pdo->prepare("
        SELECT tc.*, p.title as project_title, a.title as activity_title 
        FROM tev_claims tc
        LEFT JOIN projects p ON tc.project_id = p.id
        LEFT JOIN activities a ON tc.activity_id = a.id
        WHERE tc.id = ? AND tc.created_by = ?
    ");
    $stmt->execute([$claim_id, $user_id]);
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$claim) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Claim not found']);
        exit();
    }
    
    // Format date for display
    $claim['formatted_date'] = date('M d, Y', strtotime($claim['claim_date']));
    $claim['formatted_amount'] = '₱' . number_format($claim['amount'], 2);
    
    echo json_encode(['success' => true, 'data' => $claim]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error fetching claim: ' . $e->getMessage()]);
}
