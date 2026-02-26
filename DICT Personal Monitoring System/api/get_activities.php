<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    // Check if project_id is provided
    if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
        throw new Exception('Invalid or missing project ID');
    }

    $projectId = (int)$_GET['project_id'];
    
    // Prepare and execute the query
    $stmt = $pdo->prepare("
        SELECT 
            id,
            title,
            start_date,
            end_date,
            status
        FROM activities 
        WHERE project_id = :project_id 
        AND (end_date >= CURDATE() OR status IN ('Upcoming', 'Ongoing'))
        ORDER BY 
            FIELD(status, 'Ongoing', 'Upcoming', 'Completed') ASC,
            start_date ASC
    ");
    
    $stmt->execute([':project_id' => $projectId]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response
    $formattedActivities = array_map(function($activity) {
        return [
            'id' => $activity['id'],
            'title' => $activity['title'],
            'date_range' => [
                'start' => $activity['start_date'],
                'end' => $activity['end_date']
            ],
            'status' => $activity['status']
        ];
    }, $activities);

    $response = [
        'success' => true,
        'data' => $formattedActivities
    ];

} catch (PDOException $e) {
    error_log('Database error in get_activities.php: ' . $e->getMessage());
    $response['message'] = 'Database error occurred';
} catch (Exception $e) {
    error_log('Error in get_activities.php: ' . $e->getMessage());
    $response['message'] = $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
