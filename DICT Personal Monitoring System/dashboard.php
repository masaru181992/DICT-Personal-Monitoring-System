<?php
session_start();

// Include database configuration with error handling
if (!function_exists('safeQuery')) {
    require_once __DIR__ . '/config/database.php';
}

// Initialize variables to prevent undefined variable errors
$overallStats = [
    'total_activities' => 0,
    'completed_activities' => 0,
    'in_progress_activities' => 0,
    'pending_activities' => 0,
    'overdue_activities' => 0,
    'total_projects' => 0,
    'active_projects' => 0,
    'total_notes' => 0,
    'overdue_notes' => 0,
    'productivity_score' => 0,
    'current_month_completion' => 0,
    'completion_percentage' => 0
];

$overdueNotes = [];

/**
 * Get overdue activities and optionally create notifications
 * 
 * @param PDO $pdo Database connection
 * @param int $userId User ID to check activities for
 * @param bool $createNotifications Whether to create notifications for overdue items
 * @return array Array containing overdue activities and count
 */
function getOverdueActivities($pdo, $userId, $createNotifications = true) {
    $result = [
        'overdue_activities' => 0,
        'overdue_activities_list' => []
    ];
    
    if (!is_numeric($userId) || $userId <= 0) {
        error_log("Invalid user ID provided to getOverdueActivities: " . $userId);
        return $result;
    }
    
    $today = date('Y-m-d');
    $notificationsTableExists = false;
    
    try {
        // Check if notifications table exists if we need to create notifications
        if ($createNotifications) {
            $notificationsTableStmt = safeQuery($pdo, "SHOW TABLES LIKE 'notifications'");
            $notificationsTableExists = $notificationsTableStmt && $notificationsTableStmt->rowCount() > 0;
        }
        
        // Get all overdue activities for the user
        $query = "
            SELECT a.*
            FROM activities a
            WHERE a.status != 'completed' 
            AND a.end_date < :today
            AND a.user_id = :userId
            AND a.end_date IS NOT NULL 
            AND a.end_date != '0000-00-00'
            ORDER BY a.end_date ASC
        ";
        
        $overdueActivities = [];
        $stmt = safeQuery($pdo, $query, [
            'today' => $today,
            'userId' => $userId
        ]);
        
        if ($stmt) {
            $overdueActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $result['overdue_activities'] = count($overdueActivities);
        
        // Count in-progress activities that are overdue
        $inProgressOverdue = array_filter($overdueActivities, function($activity) {
            return isset($activity['status']) && strtolower($activity['status']) === 'in progress';
        });
        
        $result['in_progress_overdue_count'] = count($inProgressOverdue);
        $result['overdue_activities_list'] = $overdueActivities;
        
        // Only process notifications if needed and table exists
        if ($createNotifications && $notificationsTableExists && !empty($overdueActivities)) {
            // Get activities that don't have notifications yet
            $activityIds = array_column($overdueActivities, 'id');
            $placeholders = rtrim(str_repeat('?,', count($activityIds)), ',');
            
            $query = "
                SELECT a.id 
                FROM activities a
                LEFT JOIN notifications n ON n.reference_id = a.id 
                    AND n.type = 'overdue_activity' 
                    AND n.user_id = ?
                WHERE a.id IN ($placeholders)
                AND n.id IS NULL
            ";
            
            $params = array_merge([$userId], $activityIds);
            $stmt = safeQuery($pdo, $query, $params);
            $activitiesNeedingNotification = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
            
            // Create notifications for activities that need them
            if (!empty($activitiesNeedingNotification)) {
                $notificationQuery = "
                    INSERT INTO notifications 
                    (user_id, title, message, type, reference_id, is_read, created_at, updated_at)
                    VALUES (?, ?, ?, 'overdue_activity', ?, 0, NOW(), NOW())
                ";;
                
                foreach ($activitiesNeedingNotification as $activityId) {
                    $activity = array_filter($overdueActivities, function($a) use ($activityId) {
                        return $a['id'] == $activityId;
                    });
                    
                    if (!empty($activity)) {
                        $activity = reset($activity);
                        $daysOverdue = floor((strtotime($today) - strtotime($activity['end_date'])) / 86400);
                        $message = sprintf(
                            "Activity '%s' is %d day%s overdue",
                            htmlspecialchars($activity['title'], ENT_QUOTES),
                            $daysOverdue,
                            $daysOverdue != 1 ? 's' : ''
                        );
                        
                        try {
                            safeQuery($pdo, $notificationQuery, [
                                $userId,
                                'Overdue Activity',
                                $message,
                                $activityId
                            ]);
                        } catch (PDOException $e) {
                            error_log("Error creating notification for activity {$activityId}: " . $e->getMessage());
                        }
                    }
                }
            }
        }
        
    } catch (PDOException $e) {
        error_log("Database error in getOverdueActivities: " . $e->getMessage());
    }
    
    return $result;
}

// Get overdue activities for the current user
$overdueItems = [];
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    $overdueItems = getOverdueActivities($pdo, $_SESSION['user_id']);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch comprehensive statistics
$currentYear = date('Y');
$currentMonth = date('n');
$currentQuarter = ceil($currentMonth / 3);

// Get monthly activity counts for the current year
$monthlyActivityData = $pdo->query(
    "SELECT 
        MONTH(created_at) as month,
        COUNT(*) as total,
        SUM(CASE WHEN LOWER(status) = 'completed' THEN 1 ELSE 0 END) as completed
    FROM activities 
    WHERE YEAR(created_at) = $currentYear
    GROUP BY MONTH(created_at)
    ORDER BY month"
)->fetchAll(PDO::FETCH_ASSOC);

// Calculate monthly completion rates
$monthlyCompletionRates = [];
$monthlyTotals = array_fill(1, 12, 0);
$monthlyCompleted = array_fill(1, 12, 0);

foreach ($monthlyActivityData as $data) {
    $monthlyTotals[$data['month']] = (int)$data['total'];
    $monthlyCompleted[$data['month']] = (int)$data['completed'];
    $monthlyCompletionRates[$data['month']] = $data['total'] > 0 ? 
        round(($data['completed'] / $data['total']) * 100) : 0;
}

// Get current month's completion rate
$currentMonthRate = $monthlyCompletionRates[$currentMonth] ?? 0;

// Get quarterly stats
$quarterlyStats = [];
$quarterlyData = $pdo->query(
    "SELECT 
        QUARTER(created_at) as quarter,
        COUNT(*) as total,
        SUM(CASE WHEN LOWER(status) = 'completed' THEN 1 ELSE 0 END) as completed
    FROM activities 
    WHERE YEAR(created_at) = $currentYear
    GROUP BY QUARTER(created_at)
    ORDER BY quarter"
)->fetchAll(PDO::FETCH_ASSOC);

// Convert to associative array with quarter as key
foreach ($quarterlyData as $row) {
    $quarterlyStats[$row['quarter']] = [
        'total' => (int)$row['total'],
        'completed' => (int)$row['completed']
    ];
}

// Get overall stats
$overallStats = $pdo->query(
    "SELECT 
        -- Activities (current year only)
        (SELECT COUNT(*) FROM activities a WHERE YEAR(a.created_at) = {$currentYear}) as total_activities,
        (SELECT COUNT(*) FROM activities a WHERE YEAR(a.created_at) = {$currentYear} AND LOWER(a.status) = 'completed') as completed_activities,
        (SELECT COUNT(*) FROM activities a WHERE YEAR(a.created_at) = {$currentYear} AND LOWER(a.status) = 'in progress') as in_progress_activities,
        
        -- Projects (current year only)
        (SELECT COUNT(*) FROM projects p WHERE YEAR(p.created_at) = {$currentYear}) as total_projects,
        (SELECT COUNT(*) FROM projects p WHERE YEAR(p.created_at) = {$currentYear} AND p.status = 'Completed') as completed_projects,
        (SELECT COUNT(*) FROM projects p WHERE YEAR(p.created_at) = {$currentYear} AND p.status = 'In Progress') as active_projects,
        
        -- Notes (current year only)
        (SELECT COUNT(*) FROM notes n WHERE YEAR(n.created_at) = {$currentYear}) as total_notes,
        (SELECT COUNT(*) FROM notes n WHERE YEAR(n.created_at) = {$currentYear} AND n.status = 'completed') as completed_notes"
)->fetch(PDO::FETCH_ASSOC);

// Calculate completion percentages
$completion_percentage = $overallStats['total_activities'] > 0 ? 
    round(($overallStats['completed_activities'] / $overallStats['total_activities']) * 100) : 0;

$project_completion_percentage = $overallStats['total_projects'] > 0 ? 
    round(($overallStats['completed_projects'] / $overallStats['total_projects']) * 100) : 0;

$notes_completion_percentage = $overallStats['total_notes'] > 0 ? 
    round(($overallStats['completed_notes'] / $overallStats['total_notes']) * 100) : 0;

// Calculate productivity score (weighted average of completion rates)
$productivityScore = 0;
$totalWeight = 0;

// Add weights to recent months (more weight to recent months)
foreach ($monthlyCompletionRates as $month => $rate) {
    $weight = ($month == $currentMonth) ? 2 : 1; // Higher weight for current month
    $productivityScore += $rate * $weight;
    $totalWeight += $weight;
}

$productivityScore = $totalWeight > 0 ? round($productivityScore / $totalWeight) : 0;

// Prepare stats array with all metrics
$stats = [
    // Activity metrics
    'total_activities' => (int)$overallStats['total_activities'],
    'completed_activities' => (int)$overallStats['completed_activities'],
    'in_progress_activities' => (int)$overallStats['in_progress_activities'],
    'completion_percentage' => $completion_percentage,
    'pending_activities' => (int)$overallStats['total_activities'] - 
                            (int)$overallStats['completed_activities'] - 
                            (int)$overallStats['in_progress_activities'],
    
    // Project metrics
    'total_projects' => (int)$overallStats['total_projects'],
    'completed_projects' => (int)$overallStats['completed_projects'],
    'active_projects' => (int)$overallStats['active_projects'],
    'project_completion_percentage' => $project_completion_percentage,
    
    // Notes metrics
    'total_notes' => (int)$overallStats['total_notes'],
    'completed_notes' => (int)$overallStats['completed_notes'],
    'notes_completion_percentage' => $notes_completion_percentage,
    
    // Calculated metrics
    'productivity_score' => $productivityScore,
    'current_month_completion' => $currentMonthRate,
    'monthly_completion_rates' => $monthlyCompletionRates,
    'monthly_totals' => $monthlyTotals,
    'monthly_completed' => $monthlyCompleted,
    'quarterly_stats' => $quarterlyStats,
    'current_quarter' => $currentQuarter,
    'current_year' => $currentYear
];

// Create variables for cleaner template usage
$total_activities = $stats['total_activities'];
$completed_count = $stats['completed_activities'];
$in_progress_count = $stats['in_progress_activities'];
$total_projects = $stats['total_projects'];
$completion_percentage = $stats['completion_percentage'];
$in_progress_percentage = $stats['in_progress_activities'] > 0 ? 
    round(($stats['in_progress_activities'] / $stats['total_activities']) * 100) : 0;

// Set default values for any undefined stats variables
$stats['completed_count'] = $stats['completed_count'] ?? $stats['completed_activities'];
$stats['in_progress_count'] = $stats['in_progress_count'] ?? $stats['in_progress_activities'];

// Initialize important notes as empty array
$important_notes = [];

// Check if we can connect to the database
if ($pdo) {
    // Check if the notes table exists
    $tableCheck = safeQuery($pdo, "SHOW TABLES LIKE 'notes'");
    $tableExists = $tableCheck && $tableCheck->rowCount() > 0;
    
    if ($tableExists) {
        // First, try a simple query to ensure basic functionality (current year only)
        $simpleQuery = "SELECT n.* FROM notes n WHERE n.status = 'active' AND YEAR(n.created_at) = {$currentYear} LIMIT 10";
        $stmt = safeQuery($pdo, $simpleQuery);
        
        if ($stmt) {
            $important_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Successfully fetched " . count($important_notes) . " notes with simple query");
            
            // If we got results, try the full query
            if ($important_notes !== false) {
                $fullQuery = "
                    SELECT n.*, u.full_name, p.title as project_title 
                    FROM notes n 
                    LEFT JOIN users u ON n.user_id = u.id 
                    LEFT JOIN projects p ON n.project_id = p.id 
                    WHERE n.status = 'active' AND YEAR(n.created_at) = {$currentYear}
                    ORDER BY 
                        CASE 
                            WHEN n.priority = 'high' THEN 1 
                            WHEN n.priority = 'medium' THEN 2 
                            WHEN n.priority = 'low' THEN 3 
                            ELSE 4
                        END,
                        n.created_at DESC
                    LIMIT 50
                ";
                
                $fullStmt = safeQuery($pdo, $fullQuery);
                if ($fullStmt) {
                    $important_notes = $fullStmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
        } else {
            error_log("Failed to execute simple query");
        }
    } else {
        error_log("Notes table does not exist in the database");
    }
} else {
    error_log("Database connection not available");
}

// Initialize offset_data with default values (in days)
$offset_data = [
    'available_offset_days' => 0,
    'expiring_soon_days' => 0
];

// Get overtime statistics (matching offset_status.php structure)
try {
    // Get overtime statistics
    $overtime_stats = $pdo->prepare("
        SELECT 
            COUNT(CASE WHEN status = 'approved' AND (used_days < total_days OR used_days IS NULL) THEN 1 END) as remaining_overtime,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_overtime,
            COALESCE(SUM(total_days - COALESCE(used_days, 0)), 0) as total_remaining_days,
            (SELECT COALESCE(SUM(total_days), 0) FROM overtime_requests WHERE user_id = ? AND status = 'pending') as total_pending_days
        FROM overtime_requests 
        WHERE user_id = ? AND status = 'approved'"
    );
    $overtime_stats->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $overtime_data = $overtime_stats->fetch(PDO::FETCH_ASSOC);
    
    // Get used offsets (from overtime requests)
    $used_offsets_query = $pdo->prepare("
        SELECT COALESCE(SUM(used_days), 0) as total_used_days
        FROM overtime_requests 
        WHERE user_id = ? AND status = 'approved' AND used_days > 0"
    );
    $used_offsets_query->execute([$_SESSION['user_id']]);
    $used_offsets_data = $used_offsets_query->fetch(PDO::FETCH_ASSOC);
    
    // Calculate the values for the score cards (in days)
    $remaining_offset = $overtime_data ? (float)$overtime_data['total_remaining_days'] : 0;
    $used_offsets = $used_offsets_data ? (float)$used_offsets_data['total_used_days'] : 0;
    $pending_overtime = $overtime_data ? (int)$overtime_data['pending_overtime'] : 0;
    $total_pending_days = $overtime_data ? (float)$overtime_data['total_pending_days'] : 0;
    
    // Calculate total approved overtime (remaining + used)
    $total_approved_overtime = $remaining_offset + $used_offsets;
    
    // Calculate progress percentages
    $overtime_progress = $total_approved_overtime > 0 
        ? min(($remaining_offset / $total_approved_overtime) * 100, 100) : 0;
    
    // For offset, we'll assume a reasonable maximum based on remaining + used
    $offset_max = max($total_approved_overtime, 10); // At least 10 days max
    $offset_progress = $offset_max > 0 
        ? min(($remaining_offset / $offset_max) * 100, 100) : 0;
    
    // Update offset_data with calculated values (in days)
    $offset_data['available_offset_days'] = $remaining_offset;
    $offset_data['expiring_soon_days'] = 0; // Not currently tracked
    
    // Add to stats array (whole numbers only)
    $stats['overtime_approved'] = (int)round($remaining_offset);
    $stats['overtime_pending'] = (int)round($total_pending_days);
    $stats['offset_available'] = (int)round($remaining_offset);
    $stats['offset_expiring'] = '0'; // Not currently tracked in the database
    
    // Store progress values for the scorecards
    $stats['overtime_progress'] = round($overtime_progress);
    $stats['offset_progress'] = round($offset_progress);
    
} catch (PDOException $e) {
    error_log("Error fetching overtime/offset data: " . $e->getMessage());
    // Set default values
    $stats['overtime_approved'] = '0';
    $stats['overtime_pending'] = '0';
    $stats['offset_available'] = '0';
    $stats['offset_expiring'] = '0';
}

// Initialize variables
$projects = [];
$notifications = [];
$unread_count = 0;

// Fetch all active projects for dropdown (current year only)
try {
    $projectsStmt = $pdo->query("SELECT id, title FROM projects WHERE status != 'completed' AND YEAR(created_at) = {$currentYear} ORDER BY title");
    if ($projectsStmt) {
        $projects = $projectsStmt->fetchAll(PDO::FETCH_ASSOC);
        $projectsStmt->closeCursor();
    }
} catch (PDOException $e) {
    error_log("Error fetching projects: " . $e->getMessage());
}

// Fetch notifications for the current user
try {
    // First get the notifications
    $notifStmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    if ($notifStmt && $notifStmt->execute([$_SESSION['user_id']])) {
        $notifications = $notifStmt->fetchAll(PDO::FETCH_ASSOC);
        $notifStmt->closeCursor();
        
        // Now get the unread count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        if ($countStmt && $countStmt->execute([$_SESSION['user_id']])) {
            $unread_count = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['count'];
            $countStmt->closeCursor();
        }
    }
} catch (PDOException $e) {
    // Table might not exist yet, we'll handle this gracefully
    error_log("Error fetching notifications: " . $e->getMessage());
    $notifications = [];
    $unread_count = 0;
}

// Add to $stats array for use in the dashboard
$stats['notifications'] = $notifications;
$stats['unread_count'] = $unread_count;

// Use the overdue activities data
$overdueActivities = $overdueItems['overdue_activities'] ?? 0;
$overdue_activities = $overdueItems['overdue_activities_list'] ?? [];

// Log the count for debugging (only if we have a valid user ID)
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    error_log("Overdue activities count for user {$_SESSION['user_id']}: " . $overdueActivities);
}

// Fetch overdue notes (notes with reminder_date before today and status not 'completed' or 'archived') - current year only
try {
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE reminder_date < CURDATE() AND status NOT IN ('completed', 'archived') AND user_id = ? AND YEAR(created_at) = {$currentYear} ORDER BY reminder_date ASC");
    $stmt->execute([$_SESSION['user_id']]);
    $overdue_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $overdue_notes = [];
}

// Add to stats array
$stats['overdue_activities'] = $overdueActivities; // Just the count
$stats['overdue_activities_list'] = $overdue_activities; // The full list of activities
$stats['overdue_notes'] = $overdue_notes; // The full list of notes

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Personal Monitoring System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
        
    <style>
        /* Modern Card Styles */
        .analytics-card {
            border: 1px solid var(--card-border, #e9ecef);
            transition: all 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }
        
        .analytics-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            border-color: rgba(var(--bs-primary-rgb), 0.2);
        }
        
        .analytics-card .icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--icon-color, var(--bs-primary));
            transition: all 0.3s ease;
        }
        
        .analytics-card .display-5 {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--bs-dark), var(--bs-gray-700));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .trend-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            background-color: rgba(var(--bs-success-rgb), 0.1);
            color: var(--trend-color, var(--bs-success));
        }
        
        .trend-badge.down {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
            color: var(--bs-danger);
        }
        
        .trend-badge i {
            font-size: 0.8em;
            margin-right: 0.25rem;
        }
        
        /* Sparkline chart container */
        .sparkline-container {
            position: relative;
            width: 100%;
            height: 40px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1199.98px) {
            .analytics-card .display-5 {
                font-size: 1.6rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .analytics-card {
                margin-bottom: 1rem;
            }
            
            .analytics-card .display-5 {
                font-size: 1.8rem;
            }
        }
        
        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        
        /* Add staggered animation delay */
        .analytics-card:nth-child(1) { animation-delay: 0.1s; }
        .analytics-card:nth-child(2) { animation-delay: 0.2s; }
        .analytics-card:nth-child(3) { animation-delay: 0.3s; }
        .analytics-card:nth-child(4) { animation-delay: 0.4s; }
        .analytics-card:nth-child(5) { animation-delay: 0.5s; }
        .analytics-card:nth-child(6) { animation-delay: 0.6s; }
        
        /* Custom column width for 5 columns */
        .col-xl-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
        
        @media (max-width: 1399.98px) {
            .col-xl-2-4 {
                width: 25%;
            }
        }
        
        @media (max-width: 1199.98px) {
            .col-xl-2-4 {
                width: 33.333333%;
            }
        }
        
        @media (max-width: 767.98px) {
            .col-xl-2-4 {
                width: 50%;
            }
        }
        
        @media (max-width: 575.98px) {
            .col-xl-2-4 {
                width: 100%;
            }
        }
    </style>
    <style>
        /* Updated theme with white containers */
        :root {
            --primary-bg: #0a192f;
            --secondary-bg: rgba(16, 32, 56, 0.9);
            --accent-color: #64ffda;
            --accent-secondary: #7928ca;
            --accent-tertiary: #0083b0;
            --accent-notes1: #ffb347;
            --accent-notes2: #ff5e62;
            --text-dark: #1a1a1a;
            --text-secondary-dark: #4a5568;
            --border-color: rgba(100, 255, 218, 0.3);
            --card-bg: #ffffff;
            --hover-bg: rgba(100, 255, 218, 0.05);
            --text-white: #ffffff;
            --note-edit-color: #64ffda;
            --note-delete-color: #ff5e62;
        }

        /* Modern button styles */
        .note-action-btn {
            position: relative;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(100, 255, 218, 0.05);
            color: var(--accent-color);
            overflow: hidden;
            z-index: 1;
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .note-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(100, 255, 218, 0.1),
                transparent
            );
            transition: 0.6s;
            z-index: -1;
        }

        .note-action-btn:hover::before {
            left: 100%;
        }

        .note-edit-btn {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .note-edit-btn:hover {
            background: rgba(100, 255, 218, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(100, 255, 218, 0.2);
        }

        .note-delete-btn {
            color: #ff6b6b;
            border-color: #ff6b6b;
        }

        .note-delete-btn:hover {
            background: rgba(255, 107, 107, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(255, 107, 107, 0.2);
        }

        .note-action-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(100, 255, 218, 0.3);
        }

        .note-action-btn i {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
        }

        .note-action-btn:hover i {
            transform: scale(1.15);
        }
        
        .note-actions, .activity-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Activity card styles */
        .activity-card {
            background: rgba(10, 25, 47, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(100, 255, 218, 0.1) !important;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .activity-title {
            color: var(--accent-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .activity-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .activity-due {
            font-size: 0.85rem;
            color: #a5b1c9;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .activity-due i {
            color: #64ffda;
        }

        .activity-description {
            color: #e2e8f0;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }

        .activity-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .activity-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .recent-activities-header, .important-notes-header {
            background: var(--secondary-bg) !important;
            color: var(--text-white) !important;
            border-bottom: 2px solid var(--accent-color);
            border-radius: 16px 16px 0 0;
            box-shadow: none;
            padding: 0.75rem 1.25rem;
            margin: 0;
        }
        .dashboard-card-section {
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            color: var(--text-white);
            margin: 0;
            padding: 0;
        }
        .dashboard-card-section .card-body {
            padding: 0;
            margin: 0;
        }
        .dashboard-card-section .card-body {
            background: var(--secondary-bg);
            color: var(--text-white);
            border-radius: 0 0 16px 16px;
        }
        .dashboard-card-section .list-group-item {
            background: rgba(28,32,59,0.93) !important;
            color: var(--text-white) !important;
            border-bottom: 1px solid var(--border-color);
            border-radius: 0;
            margin-bottom: 0;
            padding: 0.6rem 1rem;
            box-shadow: none;
            transition: background 0.2s, box-shadow 0.2s;
            position: relative;
            z-index: 1;
            font-size: 0.85rem;
            border-left: none;
            border-right: none;
        }
        .dashboard-card-section .list-group-item:first-child {
            border-top: none;
        }
        .dashboard-card-section .list-group-item:last-child {
            border-bottom: none;
            border-radius: 0 0 16px 16px;
        }
        
        /* Ensure dropdown menu is above other elements */
        .dropdown-menu {
            z-index: 9999 !important;
            position: absolute !important;
            bottom: 100% !important;
            top: auto !important;
            left: 0 !important;
            right: auto !important;
            margin-bottom: 5px;
            min-width: 180px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 1px solid rgba(100,255,218,0.2);
            background: rgba(28,32,59,0.98) !important;
            transform: none !important;
            padding: 0.5rem 0;
            border-radius: 12px;
            display: block !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }
        
        .dropdown.show .dropdown-menu {
            opacity: 1;
            visibility: visible;
        }
        
        .dropdown-item {
            color: #e2e8f0 !important;
            padding: 0.5rem 1.25rem;
            border-radius: 12px;
            transition: background 0.2s;
        }
        
        .dropdown-item:hover {
            background: rgba(100,255,218,0.15) !important;
            color: #fff !important;
        }
        
        /* Fix for dropdown in activity cards */
        .card {
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }
        
        .dropdown {
            position: relative;
            z-index: 2;
            display: inline-block;
        }
        
        .dropdown-toggle::after {
            vertical-align: middle;
            margin-left: 0.5em;
        }
        
        .btn-update-status {
            background: rgba(100,255,218,0.1) !important;
            border: 1px solid var(--accent-color);
            color: var(--accent-color) !important;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 20px;
            transition: all 0.2s ease;
        }
        
        .btn-update-status:hover {
            background: rgba(100,255,218,0.2) !important;
            transform: translateY(-1px);
        }
        .dashboard-card-section .list-group-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .dashboard-card-section .list-group-item:hover {
            background: rgba(100,255,218,0.08) !important;
            box-shadow: 0 4px 16px rgba(100,255,218,0.08);
        }
        .dashboard-card-section .activity-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.25rem;
            letter-spacing: 0.01em;
        }
        .dashboard-card-section .activity-desc {
            color: #b2becd;
            font-size: 0.98rem;
            margin-bottom: 0.5rem;
        }
        .dashboard-card-section .activity-meta {
            font-size: 0.93rem;
            color: #a0aec0;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .dashboard-card-section .badge {
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 20px;
            padding: 5px 12px;
            letter-spacing: 0.01em;
        }
        .dashboard-card-section .project-badge {
            background: rgba(100,255,218,0.13);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
            margin-left: 7px;
        }
        .dashboard-card-section .activity-date-badge {
            background: #222e3c;
            color: #fff;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            padding: 6px 14px;
            display: inline-block;
            margin-left: 0.5rem;
            letter-spacing: 0.01em;
        }
        .dashboard-card-section .activity-date-badge i {
            color: var(--accent-color);
        }
        .dashboard-card-section .badge {
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 20px;
        }
        .dashboard-card-section .btn,
        .dashboard-card-section .btn-outline-primary,
        .dashboard-card-section .btn-outline-danger {
            border-radius: 8px;
            font-weight: 500;
        }

        body {
            background-color: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(100, 255, 218, 0.1) 0%, transparent 50%),
                radial-gradient(at 100% 0%, rgba(121, 40, 202, 0.1) 0%, transparent 50%);
            font-family: 'Space Grotesk', sans-serif;
            color: var(--text-white);
        }

        .main-content {
            padding: 30px;
            position: relative;
            color: var(--text-white);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            position: relative;
            overflow: hidden;
            color: var(--text-dark);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-color), var(--accent-secondary));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stats-card:hover::before {
            opacity: 1;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(100, 255, 218, 0.2);
        }

        .stats-icon {
            color: var(--accent-color);
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 10px 0;
        }

        .stats-title {
            color: var(--text-secondary-dark);
            font-size: 1rem;
            font-weight: 500;
        }

        /* Info Cards */
        .info-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(100, 255, 218, 0.2);
        }

        .info-card .card-header {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
            padding: 20px;
            position: relative;
        }

        .info-card .card-header h5 {
            color: var(--primary-bg);
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card .card-header h5 i {
            color: var(--primary-bg);
        }

        .info-card .card-body {
            padding: 1rem;
            background: var(--card-bg);
        }

        /* Table Styling */
        .table {
            color: var(--text-dark);
            margin: 0;
            font-family: 'JetBrains Mono', monospace;
        }

        .table th {
            color: var(--text-dark);
            font-weight: 600;
            border-bottom: 2px solid var(--accent-color);
            padding: 15px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(100, 255, 218, 0.05);
        }

        .table td {
            color: var(--text-secondary-dark);
            padding: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .table tr:hover td {
            background: rgba(100, 255, 218, 0.05);
            color: var(--text-dark);
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            background: var(--card-bg);
        }

        .status-not-started {
            color: #6c757d;
            border: 1px solid #6c757d;
        }

        .status-pending {
            color: #ffc107;
            background-color: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .status-in-progress {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
            border: 1px solid #0d6efd;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .status-completed {
            color: #198754;
            background-color: rgba(25, 135, 84, 0.1);
            border: 1px solid #198754;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .status-on-hold {
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        /* Priority Badges */
        .priority-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .priority-high {
            background-color: #dc3545;
            color: white;
        }

        .priority-medium {
            background-color: #ffc107;
            color: var(--text-dark);
        }

        .priority-low {
            background-color: #28a745;
            color: white;
        }

        /* Welcome Text */
        .welcome-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-white);
            margin-bottom: 0.2rem;
        }

        /* Current Date */
        .current-date {
            font-size: 1.1rem;
            color: var(--text-white);
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .current-date i {
            color: var(--accent-color);
        }

        /* Animations */
        .animate__fadeInUp {
            animation-duration: 0.6s;
        }

        .animate__delay-1s {
            animation-delay: 0.3s;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {<div class="activity-footer">
        </div>
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(100, 255, 218, 0.05);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(100, 255, 218, 0.2);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 255, 218, 0.3);
        }

        
        /* Status Indicator */
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        
        .status-online {
            background-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
        }
    </style>
    <style>
        /* Modern button styles */
        .note-action-btn {
            position: relative;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(100, 255, 218, 0.05);
            color: var(--accent-color);
            overflow: hidden;
            z-index: 1;
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .note-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(100, 255, 218, 0.1),
                transparent
            );
            transition: 0.6s;
            z-index: -1;
        }

        .note-action-btn:hover::before {
            left: 100%;
        }

        .note-edit-btn {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .note-edit-btn:hover {
            background: rgba(100, 255, 218, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(100, 255, 218, 0.2);
        }

        .note-delete-btn {
            color: #ff6b6b;
            border-color: #ff6b6b;
        }

        .note-delete-btn:hover {
            background: rgba(255, 107, 107, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(255, 107, 107, 0.2);
        }

        .note-action-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(100, 255, 218, 0.3);
        }

        .note-action-btn i {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
        }

        .note-action-btn:hover i {
            transform: scale(1.15);
        }
        
        .note-actions, .activity-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Activity card styles */
        .activity-card {
            background: rgba(10, 25, 47, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(100, 255, 218, 0.1) !important;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .activity-title {
            color: var(--accent-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .activity-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .activity-due {
            font-size: 0.85rem;
            color: #a5b1c9;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .activity-due i {
            color: #64ffda;
        }

        .activity-description {
            color: #e2e8f0;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }

        .activity-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .activity-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .recent-activities-header, .important-notes-header {
            background: var(--secondary-bg) !important;
            color: var(--text-white) !important;
            border-bottom: 2px solid var(--accent-color);
            border-radius: 16px 16px 0 0;
            box-shadow: none;
            padding: 0.75rem 1.25rem;
            margin: 0;
        }
        .dashboard-card-section {
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            color: var(--text-white);
            margin: 0;
            padding: 0;
        }
        .dashboard-card-section .card-body {
            padding: 0;
            margin: 0;
        }
        .dashboard-card-section .card-body {
            background: var(--secondary-bg);
            color: var(--text-white);
            border-radius: 0 0 16px 16px;
        }
        .dashboard-card-section .list-group-item {
            background: rgba(28,32,59,0.93) !important;
            color: var(--text-white) !important;
            border-bottom: 1px solid var(--border-color);
            border-radius: 0;
            margin-bottom: 0;
            padding: 0.6rem 1rem;
            box-shadow: none;
            transition: background 0.2s, box-shadow 0.2s;
            position: relative;
            z-index: 1;
            font-size: 0.85rem;
            border-left: none;
            border-right: none;
        }
        .dashboard-card-section .list-group-item:first-child {
            border-top: none;
        }
        .dashboard-card-section .list-group-item:last-child {
            border-bottom: none;
            border-radius: 0 0 16px 16px;
        }
        
        /* Ensure dropdown menu is above other elements */
        .dropdown-menu {
            z-index: 9999 !important;
            position: absolute !important;
            bottom: 100% !important;
            top: auto !important;
            left: 0 !important;
            right: auto !important;
            margin-bottom: 5px;
            min-width: 180px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 1px solid rgba(100,255,218,0.2);
            background: rgba(28,32,59,0.98) !important;
            transform: none !important;
            padding: 0.5rem 0;
            border-radius: 12px;
            display: block !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }
        
        .dropdown.show .dropdown-menu {
            opacity: 1;
            visibility: visible;
        }
        
        .dropdown-item {
            color: #e2e8f0 !important;
            padding: 0.5rem 1.25rem;
            border-radius: 12px;
            transition: background 0.2s;
        }
        
        .dropdown-item:hover {
            background: rgba(100,255,218,0.15) !important;
            color: #fff !important;
        }
        
        /* Fix for dropdown in activity cards */
        .card {
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }
        
        .dropdown {
            position: relative;
            z-index: 2;
            display: inline-block;
        }
        
        .dropdown-toggle::after {
            vertical-align: middle;
            margin-left: 0.5em;
        }
        
        .btn-update-status {
            background: rgba(100,255,218,0.1) !important;
            border: 1px solid var(--accent-color);
            color: var(--accent-color) !important;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 20px;
            transition: all 0.2s ease;
        }
        
        .btn-update-status:hover {
            background: rgba(100,255,218,0.2) !important;
            transform: translateY(-1px);
        }
        .dashboard-card-section .list-group-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .dashboard-card-section .list-group-item:hover {
            background: rgba(100,255,218,0.08) !important;
            box-shadow: 0 4px 16px rgba(100,255,218,0.08);
        }
        .dashboard-card-section .activity-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.25rem;
            letter-spacing: 0.01em;
        }
        .dashboard-card-section .activity-desc {
            color: #b2becd;
            font-size: 0.98rem;
            margin-bottom: 0.5rem;
        }
        .dashboard-card-section .activity-meta {
            font-size: 0.93rem;
            color: #a0aec0;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .dashboard-card-section .badge {
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 20px;
            padding: 5px 12px;
            letter-spacing: 0.01em;
        }
        .dashboard-card-section .project-badge {
            background: rgba(100,255,218,0.13);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
            margin-left: 7px;
        }
        .dashboard-card-section .activity-date-badge {
            background: #222e3c;
            color: #fff;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            padding: 6px 14px;
            display: inline-block;
            margin-left: 0.5rem;
            letter-spacing: 0.01em;
        }
        .dashboard-card-section .activity-date-badge i {
            color: var(--accent-color);
        }
        .dashboard-card-section .badge {
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 20px;
        }
        .dashboard-card-section .btn,
        .dashboard-card-section .btn-outline-primary,
        .dashboard-card-section .btn-outline-danger {
            border-radius: 8px;
            font-weight: 500;
        }

        body {
            background-color: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(100, 255, 218, 0.1) 0%, transparent 50%),
                radial-gradient(at 100% 0%, rgba(121, 40, 202, 0.1) 0%, transparent 50%);
            font-family: 'Space Grotesk', sans-serif;
            color: var(--text-white);
        }

        .main-content {
            padding: 30px;
            position: relative;
            color: var(--text-white);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            position: relative;
            overflow: hidden;
            color: var(--text-dark);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-color), var(--accent-secondary));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stats-card:hover::before {
            opacity: 1;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(100, 255, 218, 0.2);
        }

        .stats-icon {
            color: var(--accent-color);
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 10px 0;
        }

        .stats-title {
            color: var(--text-secondary-dark);
            font-size: 1rem;
            font-weight: 500;
        }

        /* Info Cards */
        .info-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(100, 255, 218, 0.2);
        }

        .info-card .card-header {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
            padding: 20px;
            position: relative;
        }

        .info-card .card-header h5 {
            color: var(--primary-bg);
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card .card-header h5 i {
            color: var(--primary-bg);
        }

        .info-card .card-body {
            padding: 1rem;
            background: var(--card-bg);
        }

        /* Table Styling */
        .table {
            color: var(--text-dark);
            margin: 0;
            font-family: 'JetBrains Mono', monospace;
        }

        .table th {
            color: var(--text-dark);
            font-weight: 600;
            border-bottom: 2px solid var(--accent-color);
            padding: 15px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(100, 255, 218, 0.05);
        }

        .table td {
            color: var(--text-secondary-dark);
            padding: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .table tr:hover td {
            background: rgba(100, 255, 218, 0.05);
            color: var(--text-dark);
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            background: var(--card-bg);
        }

        .status-not-started {
            color: #6c757d;
            border: 1px solid #6c757d;
        }

        .status-pending {
            color: #ffc107;
            background-color: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .status-in-progress {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
            border: 1px solid #0d6efd;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .status-completed {
            color: #198754;
            background-color: rgba(25, 135, 84, 0.1);
            border: 1px solid #198754;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .status-on-hold {
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        /* Priority Badges */
        .priority-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .priority-high {
            background-color: #dc3545;
            color: white;
        }

        .priority-medium {
            background-color: #ffc107;
            color: var(--text-dark);
        }

        .priority-low {
            background-color: #28a745;
            color: white;
        }

        /* Welcome Text */
        .welcome-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-white);
            margin-bottom: 0.2rem;
        }

        /* Current Date */
        .current-date {
            font-size: 1.1rem;
            color: var(--text-white);
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .current-date i {
            color: var(--accent-color);
        }

        /* Animations */
        .animate__fadeInUp {
            animation-duration: 0.6s;
        }

        .animate__delay-1s {
            animation-delay: 0.3s;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {<div class="activity-footer">
        </div>
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(100, 255, 218, 0.05);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(100, 255, 218, 0.2);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 255, 218, 0.3);
        }

        
        /* Status Indicator */
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        
        .status-online {
            background-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row h-100">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content animate__animated animate__fadeIn" style="margin-left: 350px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Dashboard Overview</h4>
                    </div>
                </div>
                
                <!-- Scorecard and What's New Row -->
                <div class="row g-3">
                    <!-- Scorecard Section (Main Column) -->
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0 small text-uppercase fw-bold" style="font-size: 0.75rem;"><i class="bi bi-speedometer2 me-1"></i>Performance Scorecard - <?= $currentYear ?></h5>
                                    <div class="text-muted small" style="font-size: 0.7rem;">Updated just now</div>
                                </div>
                                <div class="row g-1">
                                    <?php 
                                    // Calculate completion percentage if not already set
                                    if (!isset($stats['completion_percentage'])) {
                                        $stats['completion_percentage'] = $overallStats['total_activities'] > 0 ? 
                                            round(($overallStats['completed_activities'] / $overallStats['total_activities']) * 100) : 0;
                                    }
                                    
                                    // Set current year if not already set
                                    if (!isset($stats['current_year'])) {
                                        $stats['current_year'] = date('Y');
                                    }
                                    
                                    // Set in_progress_percentage if not already set
                                    if (!isset($stats['in_progress_percentage'])) {
                                        $stats['in_progress_percentage'] = $overallStats['total_activities'] > 0 ? 
                                            round(($overallStats['in_progress_activities'] / $overallStats['total_activities']) * 100) : 0;
                                    }
                                    
                                    // Calculate additional metrics
                                    $currentMonth = date('m');
                                    $currentQuarter = ceil($currentMonth / 3);
                                    $monthlyGrowth = 12.5; // Example growth percentage
                                    $quarterlyGrowth = 8.3; // Example growth percentage
                                    
                                    // Calculate trends and changes
                                    $lastMonth = $currentMonth > 1 ? $currentMonth - 1 : 12;
                                    $lastMonthRate = $monthlyCompletionRates[$lastMonth] ?? 0;
                                    $monthlyGrowth = $lastMonthRate > 0 ? 
                                        round((($stats['current_month_completion'] - $lastMonthRate) / $lastMonthRate) * 100, 1) : 0;
                                    
                                    $lastQuarter = $currentQuarter > 1 ? $currentQuarter - 1 : 4;
                                    $lastQProjects = $quarterlyStats[$lastQuarter]['total'] ?? 0;
                                    $currentQProjects = $quarterlyStats[$currentQuarter]['total'] ?? 0;
                                    $quarterlyGrowth = $lastQProjects > 0 ? 
                                        round((($currentQProjects - $lastQProjects) / $lastQProjects) * 100, 1) : 0;
                                    
                                    // Prepare monthly data for sparklines (last 6 months)
                                    $monthsToShow = 6;
                                    $monthlyCompletionForSparkline = [];
                                    for ($i = $monthsToShow - 1; $i >= 0; $i--) {
                                        $month = $currentMonth - $i;
                                        $year = $stats['current_year'];
                                        if ($month < 1) {
                                            $month += 12;
                                            $year--;
                                        }
                                        $monthlyCompletionForSparkline[] = $monthlyCompletionRates[$month] ?? 0;
                                    }
                                    
                                    // Prepare project completion data for sparkline
                                    $projectCompletionData = [
                                        $stats['completed_projects'],
                                        $stats['active_projects'],
                                        $stats['total_projects'] - $stats['completed_projects'] - $stats['active_projects']
                                    ];
                                    
                                    // Prepare overtime data for sparkline (last 6 months)
                                    $overtimeSparkline = [];
                                    for ($i = 5; $i >= 0; $i--) {
                                        $date = new DateTime();
                                        $date->modify("-$i months");
                                        $month = (int)$date->format('n');
                                        $year = (int)$date->format('Y');
                                        
                                        $overtime = $pdo->prepare(
                                            "SELECT 
                                                COALESCE(SUM(hours), 0) as monthly_overtime 
                                            FROM overtime_entries 
                                            WHERE user_id = ? 
                                            AND status = 'approved' 
                                            AND MONTH(date) = ? 
                                            AND YEAR(date) = ?"
                                        );
                                        $overtime->execute([$_SESSION['user_id'], $month, $year]);
                                        $overtimeData = $overtime->fetch(PDO::FETCH_ASSOC);
                                        $overtimeSparkline[] = (float)$overtimeData['monthly_overtime'];
                                    }

                                    $statCards = [
                                        [
                                            'title' => 'Offset Days',
                                            'value' => $stats['offset_available'],
                                            'icon' => 'bi-clock-history',
                                            'bg' => 'warning',
                                            'progress' => $stats['offset_progress'],
                                            'change' => $stats['offset_expiring'] . ' days expiring soon',
                                            'subtitle' => $stats['offset_available'] . ' days available',
                                            'trend_improvement' => true,
                                            'sparkline' => $overtimeSparkline, // Using overtime sparkline as a placeholder
                                            'sparkline_color' => '#f6c23e'
                                        ]
                                    ];
                                    
                                    foreach ($statCards as $card): 
                                        $trend_icon = isset($card['trend']) && $card['trend'] === 'up' ? 'bi-arrow-up' : 'bi-arrow-down';
                                        $trend_class = isset($card['trend']) && $card['trend'] === 'up' ? 'text-success' : 'text-danger';
                                    ?>
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2-4 p-1">
                                        <div class="analytics-card bg-white rounded-3 shadow-sm h-100 position-relative overflow-hidden transition-all" 
                                             style="--card-bg: var(--bs-<?= $card['bg'] ?>-bg-subtle); --card-border: var(--bs-<?= $card['bg'] ?>-border-subtle); height: 140px;">
                                            <!-- Glow effect -->
                                            <div class="position-absolute top-0 end-0" style="width: 60px; height: 60px; background: radial-gradient(circle, rgba(var(--bs-<?= $card['bg'] ?>-rgb), 0.1) 0%, rgba(var(--bs-<?= $card['bg'] ?>-rgb), 0) 70%); transform: translate(30%, -30%);"></div>
                                            
                                            <div class="p-2 h-100 d-flex flex-column">
                                                <!-- Header with title and icon -->
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 text-uppercase fw-semibold text-muted" style="font-size: 0.6rem;">
                                                        <?= htmlspecialchars($card['title']) ?>
                                                    </h6>
                                                    <div class="icon-container" style="width: 24px; height: 24px; background: rgba(var(--bs-<?= $card['bg'] ?>-rgb), 0.15); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi <?= $card['icon'] ?> text-<?= $card['bg'] ?>" style="font-size: 0.9rem;"></i>
                                                    </div>
                                                </div>
                                                
                                                <!-- Main value -->
                                                <div class="text-center my-1">
                                                    <div class="fw-bold text-dark" style="font-size: 1.8rem; line-height: 1.2;">
                                                        <?= htmlspecialchars($card['value']) ?>
                                                        <?php if (isset($card['trend'])): ?>
                                                            <i class="bi <?= $trend_icon ?> ms-1 <?= $trend_class ?>" style="font-size: 0.8em;"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (isset($card['trend_value'])): ?>
                                                    <div class="d-flex align-items-center justify-content-center mt-1">
                                                        <span class="small <?= $card['trend_improvement'] ? 'text-success' : 'text-danger' ?>">
                                                            <i class="bi <?= $card['trend'] === 'up' ? 'bi-arrow-up' : 'bi-arrow-down' ?> me-1"></i>
                                                            <?= number_format($card['trend_value'], 1) ?>%
                                                        </span>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Progress bar -->
                                                <?php if (isset($card['progress'])): ?>
                                                <div class="mt-auto">
                                                    <div class="d-flex justify-content-between small text-muted" style="font-size: 0.6rem;">
                                                        <span>Progress</span>
                                                        <span><?= $card['progress'] ?>%</span>
                                                    </div>
                                                    <div class="progress" style="height: 4px; background-color: rgba(var(--bs-<?= $card['bg'] ?>-rgb), 0.1);">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: <?= $card['progress'] ?>%; 
                                                                    background: var(--bs-<?= $card['bg'] ?>);
                                                                    border-radius: 2px;" 
                                                             aria-valuenow="<?= $card['progress'] ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <!-- Subtitle -->
                                                <?php if (isset($card['subtitle'])): ?>
                                                <div class="small text-muted mt-1" style="font-size: 0.6rem;"><?= $card['subtitle'] ?></div>
                                                <?php endif; ?>
                                                
                                                <!-- Change indicator -->
                                                <?php if (isset($card['change'])): ?>
                                                <div class="mt-3 pt-2 border-top small d-flex align-items-center">
                                                    <i class="bi <?= $card['trend_improvement'] ? 'bi-arrow-up-right-circle text-success' : 'bi-arrow-down-right-circle text-danger' ?> me-2"></i>
                                                    <span class="text-muted"><?= $card['change'] ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Corner accent -->
                                            <div class="position-absolute top-0 end-0" style="width: 0; height: 0; border-style: solid; border-width: 0 60px 60px 0; border-color: transparent var(--bs-<?= $card['bg'] ?>-border-subtle) transparent transparent; opacity: 0.7;"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- What's New Banner (Right Column) -->
                    <div class="col-12 col-lg-4">
                        <div id="whatsNewBanner" class="alert alert-info alert-dismissible fade show" role="alert" style="background: rgba(100,255,218,0.08); color: var(--text-white); border: 1px solid var(--border-color); margin-bottom: 0; height: 93%; display: flex; flex-direction: column;">
                            <div class="d-flex align-items-start flex-grow-1">
                                <i class="bi bi-stars me-2" style="color: var(--accent-color); flex-shrink: 0;"></i>
                                <div class="flex-grow-1">
                                    <strong>What's new in v2.9.2 (January 9, 2026):</strong>
                                    <ul class="mb-0 mt-1 small" style="font-size: 0.85rem;">
                                        <li>TEV Claims: Added comprehensive filtering by project, status, and date range</li>
                                        <li>Activities: Added delete button in calendar modal between close and edit buttons</li>
                                        <li>Activities: View mode persistence - calendar/table view maintained on refresh</li>
                                    </ul>
                                    <small>See full details in <a href="about.php" class="link-light text-decoration-underline">About</a>.</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="position: absolute; top: 10px; right: 10px;"></button>
                        </div>
                    </div>
                </div>

                <script>
                    (function() {
                        const key = 'hideWhatsNew_2.9.2';
                        const banner = document.getElementById('whatsNewBanner');
                        try {
                            if (localStorage.getItem(key) === 'true' && banner) {
                                banner.style.display = 'none';
                            }
                        } catch (e) {
                            // Ignore localStorage errors
                        }
                    })();
                </script>

                <!-- IPCR Analytics Section -->
                <?php
                // Include IPCR analytics
                if (file_exists('includes/ipcr_analytics.php')) {
                    include 'includes/ipcr_analytics.php';
                }
                ?>
                
            </div>
        </div>
    </div>

    <!-- Status Update Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="statusToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Activity status updated successfully!
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content light-theme">
                <div class="modal-header">
                    <h5 class="modal-title">Update Activity Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Updating status for: <strong id="activityTitle"></strong></p>
                    <form id="statusUpdateForm">
                        <input type="hidden" id="activityId" name="activity_id">
                        <div class="mb-3">
                            <label for="statusSelect" class="form-label">Status</label>
                            <select class="form-select" id="statusSelect" name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="statusNotes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="statusNotes" name="notes" rows="3" placeholder="Add any additional notes about this status update"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveStatusUpdate">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content light-theme">
                <div class="modal-header">
                    <h5 class="modal-title">Update Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" name="note_id" id="updateNoteId">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select light-theme" name="status" id="updateStatus">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select class="form-select light-theme" name="priority" id="updatePriority">
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-custom" id="saveStatus">Update Note</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Operation completed successfully!
            </div>
        </div>
    </div>


    <!-- Error Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="errorToastMessage">
                An error occurred. Please try again.
            </div>
        </div>
    </div>

F

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard-edit-buttons.js"></script>

    <!-- Main Script -->
    <script>
    // Clock functionality
    function updateClock() {
        const timeElement = document.getElementById('current-time');
        if (!timeElement) return;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        
        timeElement.textContent = timeString;
    }

    // Show toast notification
    function showToast(type, message) {
        const toastElement = document.getElementById(`${type}Toast`);
        const toastMessage = document.getElementById(`${type}ToastMessage`);
        
        if (toastElement && toastMessage) {
            toastMessage.textContent = message;
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }



    // Handle status update for activities
    async function handleActivityStatusUpdate() {
        const form = document.getElementById('statusUpdateForm');
        const formData = new FormData(form);
        const saveButton = document.getElementById('saveStatusUpdate');
        const originalText = saveButton.innerHTML;
        const activityId = formData.get('activity_id');
        const newStatus = formData.get('status');
        const notes = formData.get('notes');

        // Show loading state
        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

        try {
            const response = await fetch('api/update_activity_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    activity_id: activityId,
                    status: newStatus,
                    notes: notes || ''
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('success', 'Activity status updated successfully!');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal'));
                modal.hide();
                
                // Update the UI without refreshing
                const statusBadge = document.querySelector(`.activity-status[data-activity-id="${activityId}"]`);
                if (statusBadge) {
                    // Update status badge
                    const statusClass = getStatusBadgeClass(newStatus);
                    statusBadge.className = `badge ${statusClass} activity-status`;
                    statusBadge.textContent = newStatus;
                    
                    // If this is a card, update the status text as well
                    const card = statusBadge.closest('.card');
                    if (card) {
                        const statusText = card.querySelector('.activity-status-text');
                        if (statusText) {
                            statusText.textContent = newStatus;
                        }
                    }
                }
                
            } else {
                throw new Error(data.message || 'Failed to update activity status');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', error.message || 'An error occurred while updating the activity status.');
        } finally {
            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = originalText;
        }
    }
    
    // Helper function to get appropriate badge class based on status
    function getStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'completed':
                return 'bg-success-subtle text-success';
            case 'in progress':
                return 'bg-primary-subtle text-primary';
            case 'on hold':
                return 'bg-warning-subtle text-warning';
            case 'pending':
            default:
                return 'bg-secondary-subtle text-secondary';
        }
    }
    }



    // Handle update status button clicks
    function setupStatusUpdateButtons() {
        document.querySelectorAll('.update-status').forEach(button => {
            button.addEventListener('click', function() {
                const noteId = this.getAttribute('data-note-id');
                const status = this.getAttribute('data-status');
                const priority = this.closest('.card').querySelector('.badge').textContent.trim().toLowerCase();
                
                document.getElementById('updateNoteId').value = noteId;
                document.getElementById('updateStatus').value = status;
                document.getElementById('updatePriority').value = priority;
                
                const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                modal.show();
            });
        });
    }

    // Setup click handler for status update button
    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.update-activity-status')) {
            e.preventDefault();
            const activityId = e.target.getAttribute('data-activity-id');
            const activityTitle = e.target.getAttribute('data-activity-title');
            const currentStatus = e.target.getAttribute('data-current-status') || 'Pending';
            
            // Set modal fields
            document.getElementById('activityTitle').textContent = activityTitle;
            document.getElementById('activityId').value = activityId;
            document.getElementById('statusSelect').value = currentStatus;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
            modal.show();
        }
    });
    
    // Handle save status update button click
    const saveStatusBtn = document.getElementById('saveStatusUpdate');
    if (saveStatusBtn) {
        saveStatusBtn.addEventListener('click', handleActivityStatusUpdate);
    }
    
    // Also handle form submission with Enter key
    const statusForm = document.getElementById('statusUpdateForm');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleActivityStatusUpdate();
        });
    }


    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener for the save status update button
        document.getElementById('saveStatusUpdate').addEventListener('click', function() {
            handleActivityStatusUpdate();
        });
        
        // Add click handlers for all update status buttons
        document.querySelectorAll('.update-status-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const activityId = this.getAttribute('data-activity-id');
                const activityTitle = this.getAttribute('data-activity-title');
                const currentStatus = this.getAttribute('data-current-status') || 'Pending';
                openStatusUpdateModal(activityId, activityTitle, currentStatus);
            });
        });
        
        // Initialize Bootstrap modals
        const statusUpdateModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
        
        // Initialize dropdown toggles
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdown = this.closest('.dropdown');
                const isOpen = dropdown.classList.contains('show');
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown').forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                if (!isOpen) {
                    dropdown.classList.add('show');
                }
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        });
        
        // Add subtle animation to user avatar
        const avatar = document.querySelector('.user-avatar');
        if (avatar) {
            avatar.style.transition = 'all 0.3s ease';
            
            avatar.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05) rotate(5deg)';
                this.style.boxShadow = '0 5px 15px rgba(100, 255, 218, 0.3)';
            });
            
            avatar.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1) rotate(0)';
                this.style.boxShadow = 'none';
            });
        }
        
        // Add hover effect for sidebar menu items
        const sidebarLinks = document.querySelectorAll('.sidebar > a');
        sidebarLinks.forEach(link => {
            // Add initial state
            link.style.transition = 'all 0.3s ease';
            link.style.borderRadius = '8px';
            link.style.margin = '4px 10px';
            link.style.padding = '10px 15px';
            
            // Add hover effect
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(8px)';
                this.style.background = 'rgba(100, 255, 218, 0.1)';
                this.style.boxShadow = '2px 2px 12px rgba(100, 255, 218, 0.15)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
                this.style.background = '';
                this.style.boxShadow = 'none';
            });
            
            // Add active state for current page
            if (link.href === window.location.href) {
                link.style.background = 'rgba(100, 255, 218, 0.15)';
                link.style.borderLeft = '3px solid var(--accent-color)';
            }
        });
        
        // Initialize sparkline charts
        const sparklineContainers = document.querySelectorAll('.sparkline-container');
        sparklineContainers.forEach(container => {
            const data = JSON.parse(container.getAttribute('data-sparkline'));
            const color = container.getAttribute('data-color') || '#4e73df';
            
            // Create canvas element if it doesn't exist
            if (!container.querySelector('canvas')) {
                const canvas = document.createElement('canvas');
                container.appendChild(canvas);
                
                // Set canvas dimensions
                canvas.width = container.offsetWidth;
                canvas.height = container.offsetHeight;
                
                // Create gradient
                const ctx = canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
                gradient.addColorStop(0, color + '33'); // 20% opacity
                gradient.addColorStop(1, color + '00'); // 0% opacity
                
                // Create chart
                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: Array(data.length).fill(''),
                        datasets: [{
                            data: data,
                            borderColor: color,
                            backgroundColor: gradient,
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        },
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false,
                                min: Math.min(...data) * 0.9,
                                max: Math.max(...data) * 1.1
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }
        });
        
        // Add click handler to analytics cards
        document.querySelectorAll('.analytics-card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking on a link or button inside the card
                if (e.target.tagName === 'A' || e.target.closest('a, button, [role="button"]')) {
                    return;
                }
                
                // Add a ripple effect
                const ripple = document.createElement('div');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.transform = 'scale(0)';
                ripple.style.background = 'rgba(255, 255, 255, 0.7)';
                ripple.style.width = '10px';
                ripple.style.height = '10px';
                ripple.style.pointerEvents = 'none';
                
                // Position the ripple at the click position
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                
                card.style.position = 'relative';
                card.style.overflow = 'hidden';
                
                card.appendChild(ripple);
                
                // Animate the ripple
                const scale = Math.max(rect.width, rect.height) / 5;
                
                ripple.style.transition = 'transform 0.6s ease-out, opacity 0.6s ease-out';
                ripple.style.transform = `scale(${scale})`;
                ripple.style.opacity = '0';
                
                // Remove the ripple after animation
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add resize observer to update chart dimensions
        if (typeof ResizeObserver !== 'undefined') {
            const resizeObserver = new ResizeObserver(entries => {
                for (let entry of entries) {
                    const container = entry.target;
                    const canvas = container.querySelector('canvas');
                    if (canvas) {
                        canvas.width = container.offsetWidth;
                        canvas.height = container.offsetHeight;
                        
                        // Recreate the chart with new dimensions
                        const chart = Chart.getChart(canvas);
                        if (chart) {
                            chart.resize();
                        }
                    }
                }
            });
            
            // Observe all sparkline containers
            document.querySelectorAll('.sparkline-container').forEach(container => {
                resizeObserver.observe(container);
            });
        }
    });
    
    // Start clock
    updateClock();
    setInterval(updateClock, 1000);
    
    // Setup status update buttons
    document.querySelectorAll('.status-update').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const noteId = this.getAttribute('data-note-id');
            const status = this.getAttribute('data-status');
            const priority = this.closest('.card').querySelector('.badge').textContent.trim().toLowerCase();
            
            document.getElementById('updateNoteId').value = noteId;
            document.getElementById('updateStatus').value = status;
            document.getElementById('updatePriority').value = priority;
            
            const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
            modal.show();
        });
    });
    
    // Handle save status update button click
    const saveStatus = document.getElementById('saveStatus');
    if (saveStatus) {
        saveStatus.addEventListener('click', function() {
            const form = document.getElementById('updateStatusForm');
            const formData = new FormData(form);
            const saveButton = this;
            const originalText = saveButton.innerHTML;
            
            // Show loading state
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            
            // Send AJAX request
            fetch('update_note_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Note updated successfully!');
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
                    modal.hide();
                    
                    // Reload the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to update note status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', error.message || 'Failed to update note status');
            })
            .finally(() => {
                // Reset button state
                saveButton.disabled = false;
                saveButton.innerHTML = originalText;
            });
        });
    }
    
    // Handle delete note
    document.querySelectorAll('.delete-note').forEach(button => {
        button.addEventListener('click', async function() {
            const noteId = this.getAttribute('data-note-id');
            
            if (confirm('Are you sure you want to delete this note?')) {
                try {
                    const response = await fetch(`api/delete_note.php?id=${noteId}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showToast('success', 'Note deleted successfully!');
                        
                        // Remove note from UI
                        document.querySelector(`[data-note-id="${noteId}"]`).remove();
                    } else {
                        throw new Error(result.message || 'Failed to delete note');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Failed to delete note');
                }
            }
        });
    });
    
    // Handle edit note button clicks using event delegation
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.edit-note')) {
            e.preventDefault();
            const noteId = e.target.closest('.edit-note').getAttribute('data-note-id');
            if (noteId) {
                const modal = document.getElementById('noteModal');
                
                // Set form values
                modal.querySelector('#noteId').value = noteId;
                modal.querySelector('#noteTitle').value = e.target.closest('.edit-note').querySelector('.note-title').textContent;
                modal.querySelector('#noteContent').value = e.target.closest('.edit-note').querySelector('.note-content').textContent;
                
                // Set priority
                if (e.target.closest('.edit-note').querySelector('.badge')) {
                    modal.querySelector('#notePriority').value = e.target.closest('.edit-note').querySelector('.badge').textContent.trim().toLowerCase();
                }
                
                // Set project dropdown
                const projectSelect = modal.querySelector('#noteProject');
                projectSelect.innerHTML = '<option value="">None</option>';
                
                if (result.projects && result.projects.length > 0) {
                    result.projects.forEach(project => {
                        const option = document.createElement('option');
                        option.value = project.id;
                        option.textContent = project.title;
                        if (e.target.closest('.edit-note').querySelector('.project-badge')) {
                            option.selected = true;
                        }
                        projectSelect.appendChild(option);
                    });
                }
                
                // Show the modal
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            }
        }
    });
    
    // Reset form when modal is hidden
    document.getElementById('noteModal').addEventListener('hidden.bs.modal', function () {
        const form = document.getElementById('noteForm');
        if (form) {
            form.reset();
            // Clear any additional fields
            const additionalFields = form.querySelectorAll('input[type="text"], input[type="date"], textarea, select');
            additionalFields.forEach(field => {
                if (field.type === 'select-one') {
                    field.selectedIndex = 0; // Reset select to first option
                } else if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = false;
                } else {
                    field.value = '';
                }
            });
            
            // Reset any rich text editors or other dynamic content
            const noteId = document.getElementById('noteId');
            if (noteId) noteId.value = '';
            
            const modalLabel = document.getElementById('noteModalLabel');
            if (modalLabel) modalLabel.textContent = 'Add New Note';
            
            // Remove any validation errors
            const errorMessages = form.querySelectorAll('.is-invalid, .invalid-feedback');
            errorMessages.forEach(error => error.remove());
            
            // Remove was-validated class to clear any validation states
            form.classList.remove('was-validated');
        }
    });
    
    // Prevent form submission from redirecting
    const noteForm = document.getElementById('noteForm');
    if (noteForm) {
        noteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Handle form submission via AJAX here if needed
            // This prevents the default form submission that would cause a page redirect
        });
    }
    
    // Update time every second
    function updateTime() {
            const now = new Date();
            const options = { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            };
            const timeElement = document.querySelector('.live-time');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', options);
            }
        }
        
        // Update time immediately and then every second
        updateTime();
        setInterval(updateTime, 1000);

        // Function to open status update modal
        function openStatusModal(activityId, activityTitle, currentStatus) {
            document.getElementById('activityId').value = activityId;
            document.getElementById('activityTitle').textContent = activityTitle;
            
            // Set the current status in the dropdown
            const statusSelect = document.getElementById('statusSelect');
            statusSelect.value = currentStatus;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
            modal.show();
        }
        
        // Handle save status update
        document.getElementById('saveStatusUpdate').addEventListener('click', function() {
            const form = document.getElementById('statusUpdateForm');
            const formData = new FormData(form);
            const saveButton = this;
            const originalText = saveButton.innerHTML;
            
            // Show loading state
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            
            // Send AJAX request
            fetch('update_activity_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Activity status updated successfully!');
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal'));
                    modal.hide();
                    
                    // Reload the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to update activity status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', error.message || 'Failed to update activity status');
            })
            .finally(() => {
                // Reset button state
                saveButton.disabled = false;
                saveButton.innerHTML = originalText;
            });
        });
        
    }
    
    // Add Chart.js
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    
    <!-- Edit Activity Modal -->
    <div class="modal fade" id="editActivityModal" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editActivityModalLabel">Edit Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dashboard Edit Buttons JS -->
    <script src="assets/js/dashboard-edit-buttons.js"></script>
</body>
</html>