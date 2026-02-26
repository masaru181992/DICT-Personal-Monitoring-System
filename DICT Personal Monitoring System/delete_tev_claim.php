<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if request is POST and has claim ID
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$claim_id = intval($_POST['id']);
$user_id = $_SESSION['user_id'];

try {
    // Verify the claim belongs to the user
    $stmt = $pdo->prepare("SELECT id FROM tev_claims WHERE id = ? AND created_by = ?");
    $stmt->execute([$claim_id, $user_id]);
    $claim = $stmt->fetch();
    
    if (!$claim) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Claim not found or cannot be deleted']);
        exit();
    }
    
    // Delete the claim
    $stmt = $pdo->prepare("DELETE FROM tev_claims WHERE id = ?");
    $stmt->execute([$claim_id]);
    
    echo json_encode(['success' => true, 'message' => 'Claim deleted successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error deleting claim: ' . $e->getMessage()]);
}
