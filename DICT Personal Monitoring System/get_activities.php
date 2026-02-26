<?php
header('Content-Type: application/json');
require_once 'config/database.php';

$response = [
    'success' => false,
    'activities' => []
];

try {
    // Check if project_id is provided and valid
    if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
        throw new Exception('Invalid project ID');
    }

    $project_id = intval($_GET['project_id']);
    
    // Fetch all activities for the selected project (include past activities)
    $stmt = $pdo->prepare("
        SELECT 
            a.id, 
            a.title COLLATE utf8mb4_unicode_ci as title, 
            a.start_date, 
            a.end_date,
            a.status COLLATE utf8mb4_unicode_ci as status,
            a.google_drive_link,
            CASE 
                WHEN a.start_date > CURDATE() THEN 'Upcoming'
                WHEN a.end_date < CURDATE() THEN 'Completed'
                ELSE 'In Progress'
            END as status_display,
            CASE 
                WHEN a.start_date > CURDATE() THEN 1  -- Upcoming first
                WHEN a.end_date < CURDATE() THEN 3    -- Completed last
                ELSE 2                                -- In Progress in between
            END as sort_order
        FROM activities a
        WHERE a.project_id = ? 
        ORDER BY 
            sort_order,
            a.start_date DESC, 
            a.title COLLATE utf8mb4_unicode_ci ASC
    ");
    
    $stmt->execute([
        $project_id
    ]);
    
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response['success'] = true;
    $response['activities'] = $activities;
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);