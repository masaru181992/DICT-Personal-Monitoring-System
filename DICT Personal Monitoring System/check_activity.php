<?php
require_once 'config/database.php';

// Search for the specific activity
$activityTitle = 'Assist the CNHS for using the facility';
$today = date('Y-m-d');

try {
    // Check if the activity exists and its status
    $stmt = $pdo->prepare("
        SELECT 
            id, 
            title, 
            description, 
            start_date, 
            end_date, 
            status,
            CASE 
                WHEN end_date < :today AND status != 'completed' THEN 'Overdue'
                ELSE 'Not Overdue'
            END as overdue_status
        FROM activities 
        WHERE title LIKE :title
        OR description LIKE :desc
        ORDER BY end_date DESC
        LIMIT 1
    ");
    
    $searchTerm = "%CNHS%";
    $stmt->execute([
        ':today' => $today,
        ':title' => $searchTerm,
        ':desc' => $searchTerm
    ]);
    
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($activity) {
        echo "<h2>Activity Found</h2>";
        echo "<pre>";
        print_r($activity);
        echo "</pre>";
        
        // Check if it's overdue based on the same criteria used in the dashboard
        $isOverdue = ($activity['end_date'] < $today && $activity['status'] != 'completed');
        
        echo "<h3>Overdue Status: " . ($isOverdue ? 'OVERDUE' : 'Not Overdue') . "</h3>";
        echo "<p>Reason: ";
        if ($isOverdue) {
            echo "The activity's end date ({$activity['end_date']}) has passed and it's not marked as completed.";
        } else {
            if ($activity['status'] == 'completed') {
                echo "The activity is marked as completed.";
            } else {
                echo "The activity's end date ({$activity['end_date']}) is in the future or today.";
            }
        }
        echo "</p>";
    } else {
        echo "<p>No activity found matching the search term 'CNHS' in title or description.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Show all overdue activities for the current user
if (isset($_SESSION['user_id'])) {
    try {
        $overdueStmt = $pdo->prepare("
            SELECT id, title, end_date, status 
            FROM activities 
            WHERE user_id = :user_id
            AND end_date < :today 
            AND status != 'completed'
            AND (end_date IS NOT NULL AND end_date != '0000-00-00')
            ORDER BY end_date ASC
        ");
        
        $overdueStmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':today' => $today
        ]);
        
        $overdueActivities = $overdueStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>All Your Overdue Activities:</h3>";
        if (count($overdueActivities) > 0) {
            echo "<ul>";
            foreach ($overdueActivities as $activity) {
                $daysOverdue = floor((strtotime($today) - strtotime($activity['end_date'])) / (60 * 60 * 24));
                echo "<li>{$activity['title']} (Due: {$activity['end_date']}, Overdue by $daysOverdue days)</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>You have no overdue activities.</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p>Error fetching overdue activities: " . $e->getMessage() . "</p>";
    }
}
?>
