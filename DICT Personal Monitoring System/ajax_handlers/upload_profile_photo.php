<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['profile_photo'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_photo'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $response['message'] = 'Only JPG, PNG, and GIF files are allowed.';
        echo json_encode($response);
        exit();
    }
    
    if ($file['size'] > $max_size) {
        $response['message'] = 'File size must be less than 2MB.';
        echo json_encode($response);
        exit();
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = __DIR__ . '/../uploads/profile_photos/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Update database
        try {
            // Delete old photo if exists
            $stmt = $pdo->prepare("SELECT profile_photo FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $old_photo = $stmt->fetchColumn();
            
            if ($old_photo && file_exists($upload_dir . $old_photo)) {
                unlink($upload_dir . $old_photo);
            }
            
            // Update user record
            $stmt = $pdo->prepare("UPDATE users SET profile_photo = ?, profile_photo_updated_at = NOW() WHERE id = ?");
            $stmt->execute([$filename, $user_id]);
            
            $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
            $response = [
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'photo_url' => $base_url . '/DICT%20Personal%20Monitoring%20System/uploads/profile_photos/' . $filename . '?t=' . time()
            ];
            
            // Update session with new photo
            $_SESSION['profile_photo'] = $filename;
            
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Failed to upload file';
    }
} else {
    $response['message'] = 'No file uploaded';
}

echo json_encode($response);
?>
