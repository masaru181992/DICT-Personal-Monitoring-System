<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

// Start session and include database configuration
session_start();
require_once 'config/database.php';

// Function to send JSON response and exit
function sendResponse($success, $message = '', $data = null) {
    $response = ['success' => $success];
    if ($message) $response['message'] = $message;
    if ($data !== null) $response['data'] = $data;
    
    echo json_encode($response);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    sendResponse(false, 'Unauthorized: Please log in to continue');
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sendResponse(false, 'Method not allowed');
}

// Get claim ID, payment date, and amount from POST data
$claim_id = isset($_POST['claim_id']) ? intval($_POST['claim_id']) : 0;
$payment_date = isset($_POST['payment_date']) ? trim($_POST['payment_date']) : '';
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

// Validate inputs
if ($claim_id <= 0) {
    sendResponse(false, 'Invalid claim ID');
}

if (empty($payment_date)) {
    sendResponse(false, 'Payment date is required');
}

// Validate payment date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $payment_date)) {
    sendResponse(false, 'Invalid date format. Please use YYYY-MM-DD format.');
}

// Validate amount
if ($amount <= 0) {
    sendResponse(false, 'Please enter a valid amount greater than 0');
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Update the claim status, payment date, and amount
    $stmt = $pdo->prepare("
        UPDATE tev_claims 
        SET status = 'Paid', 
            payment_date = :payment_date,
            amount = :amount,
            updated_at = NOW()
        WHERE id = :claim_id
    ");
    
    $result = $stmt->execute([
        ':payment_date' => $payment_date,
        ':amount' => $amount,
        ':claim_id' => $claim_id
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        // Commit the transaction
        $pdo->commit();
        
        // Get the updated claim data
        $stmt = $pdo->prepare("SELECT * FROM tev_claims WHERE id = ?");
        $stmt->execute([$claim_id]);
        $updatedClaim = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Format the status with payment date for display
        $formattedStatus = 'Paid on ' . date('M d, Y', strtotime($updatedClaim['payment_date']));
        
        sendResponse(true, 'Claim marked as paid successfully', [
            'status' => $formattedStatus,
            'payment_date' => $updatedClaim['payment_date'],
            'amount' => number_format($updatedClaim['amount'], 2)
        ]);
    } else {
        // No rows affected - claim not found or already marked as paid
        $pdo->rollBack();
        sendResponse(false, 'Claim not found or already marked as paid');
    }
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log('Error marking claim as paid: ' . $e->getMessage());
    sendResponse(false, 'Database error: ' . $e->getMessage());
}
