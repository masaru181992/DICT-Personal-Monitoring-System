<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        // Handle add action
        if ($_POST['action'] === 'add') {
            $project_id = (int)$_POST['project_id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $google_drive_link = trim($_POST['google_drive_link'] ?? '');
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $status = strtolower(trim($_POST['status']));
            
            // Basic validation
            if (empty($title) || empty($start_date) || empty($end_date)) {
                throw new Exception("All required fields must be filled out.");
            }
            
            // URL validation for Google Drive link if provided
            if (!empty($google_drive_link) && !filter_var($google_drive_link, FILTER_VALIDATE_URL)) {
                throw new Exception("Please enter a valid URL for the Google Drive link.");
            }
            
            // Date validation
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $today = new DateTime();
            
            if ($end < $start) {
                throw new Exception("End date cannot be before start date.");
            }
            
            // Recurrence parameters
            $repeat_enabled = isset($_POST['repeat_enabled']) && $_POST['repeat_enabled'] === '1';
            $repeat_frequency = $_POST['repeat_frequency'] ?? 'daily'; // daily, weekly, monthly
            $repeat_interval = max(1, (int)($_POST['repeat_interval'] ?? 1));
            $repeat_end_type = $_POST['repeat_end_type'] ?? 'never'; // never, on_date, after_count
            $repeat_end_date_str = $_POST['repeat_end_date'] ?? '';
            $repeat_count = (int)($_POST['repeat_count'] ?? 0);

            // Calculate base duration (inclusive days difference)
            $baseDiff = $start->diff($end);
            $durationDays = (int)$baseDiff->days; // 0 means single day

            // Helper to insert one activity
            $insertOne = function(DateTime $s) use ($pdo, $project_id, $title, $description, $google_drive_link, $status, $durationDays) {
                $sFormatted = $s->format('Y-m-d');
                $e = clone $s;
                if ($durationDays > 0) {
                    $e->modify("+{$durationDays} day");
                }
                $eFormatted = $e->format('Y-m-d');
                $stmt = $pdo->prepare("INSERT INTO activities (project_id, title, description, google_drive_link, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                return $stmt->execute([$project_id, $title, $description, $google_drive_link, $sFormatted, $eFormatted, $status]);
            };

            if (!$repeat_enabled) {
                // Single insert
                if ($insertOne($start)) {
                    $success_message = "Activity added successfully!";
                    echo '<script>setTimeout(function(){window.location.href=window.location.pathname;},1000);</script>';
                } else {
                    throw new Exception("Failed to add activity. Please try again.");
                }
            } else {
                // Validate end condition for recurrence
                $maxOccurrences = 200; // safety cap
                $targetCount = 0;
                $endDateLimit = null;
                if ($repeat_end_type === 'on_date') {
                    if (empty($repeat_end_date_str)) {
                        throw new Exception("Please provide an end date for the repeat.");
                    }
                    $endDateLimit = new DateTime($repeat_end_date_str);
                    if ($endDateLimit < $start) {
                        throw new Exception("Repeat end date cannot be before the start date.");
                    }
                } elseif ($repeat_end_type === 'after_count') {
                    if ($repeat_count <= 0) {
                        throw new Exception("Please provide a valid number of occurrences for the repeat.");
                    }
                    $targetCount = min($repeat_count, $maxOccurrences);
                } else {
                    // never: still apply a safe cap
                    $targetCount = 50;
                }

                // Start transaction for batch insert
                $pdo->beginTransaction();
                try {
                    $occurrences = 0;
                    $current = clone $start;

                    $advance = function(DateTime $dt) use ($repeat_frequency, $repeat_interval) {
                        $new = clone $dt;
                        if ($repeat_frequency === 'daily') {
                            $new->modify("+{$repeat_interval} day");
                        } elseif ($repeat_frequency === 'weekly') {
                            $new->modify("+" . (7 * $repeat_interval) . " day");
                        } elseif ($repeat_frequency === 'monthly') {
                            $new->modify("+{$repeat_interval} month");
                        } elseif ($repeat_frequency === 'yearly') {
                            $new->modify("+{$repeat_interval} year");
                        } else {
                            $new->modify("+{$repeat_interval} day");
                        }
                        return $new;
                    };

                    // Loop based on end condition
                    if ($repeat_end_type === 'on_date') {
                        while ($current <= $endDateLimit && $occurrences < $maxOccurrences) {
                            if (!$insertOne($current)) {
                                throw new Exception("Failed to add one of the repeated activities.");
                            }
                            $occurrences++;
                            $current = $advance($current);
                        }
                    } else {
                        // after_count or never (capped)
                        $toCreate = ($repeat_end_type === 'after_count') ? $targetCount : $targetCount;
                        for ($i = 0; $i < $toCreate && $occurrences < $maxOccurrences; $i++) {
                            if (!$insertOne($current)) {
                                throw new Exception("Failed to add one of the repeated activities.");
                            }
                            $occurrences++;
                            $current = $advance($current);
                        }
                    }

                    $pdo->commit();
                    $success_message = "Added $occurrences activities (repeating).";
                    echo '<script>setTimeout(function(){window.location.href=window.location.pathname;},1000);</script>';
                } catch (Exception $ex) {
                    $pdo->rollBack();
                    throw $ex;
                }
            }
        } 
        
        // Handle update action
        if ($_POST['action'] === 'update' && isset($_POST['activity_id'])) {
            $activity_id = (int)$_POST['activity_id'];
            $project_id = (int)$_POST['project_id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $google_drive_link = trim($_POST['google_drive_link'] ?? '');
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $status = strtolower(trim($_POST['status']));
            
            // Basic validation
            if (empty($title) || empty($start_date) || empty($end_date)) {
                throw new Exception("All required fields must be filled out.");
            }
            
            // URL validation for Google Drive link if provided
            if (!empty($google_drive_link) && !filter_var($google_drive_link, FILTER_VALIDATE_URL)) {
                throw new Exception("Please enter a valid URL for the Google Drive link.");
            }
            
            // Date validation
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            
            if ($end < $start) {
                throw new Exception("End date cannot be before start date.");
            }
            
            // Start transaction
            $pdo->beginTransaction();
            
            try {
                // Update activity
                $stmt = $pdo->prepare("UPDATE activities SET project_id = ?, title = ?, description = ?, google_drive_link = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
                $stmt->execute([$project_id, $title, $description, $google_drive_link, $start_date, $end_date, $status, $activity_id]);
                
                // Handle requirements data
                $requirements = json_decode($_POST['requirements'] ?? '{}', true);
                
                // Check if requirements record exists
                $stmt = $pdo->prepare("SELECT id FROM activity_requirements WHERE activity_id = ?");
                $stmt->execute([$activity_id]);
                $exists = $stmt->fetch();
                
                if ($exists) {
                    // Update existing requirements
                    $stmt = $pdo->prepare("
                        UPDATE activity_requirements SET 
                            request_letter = ?, 
                            reply_letter = ?, 
                            ad = ?, 
                            `to` = ?, 
                            to_number = ?,
                            post_activity = ?, 
                            certificates = ?, 
                            verification_statements = ?, 
                            pnpki_application = ?, 
                            photos = ?, 
                            published = ?, 
                            published_link = ?
                        WHERE activity_id = ?
                    ");
                    
                    $stmt->execute([
                        $requirements['request_letter'] ?? 0,
                        $requirements['reply_letter'] ?? 0,
                        $requirements['ad'] ?? 0,
                        $requirements['to'] ?? 0,
                        $requirements['to_number'] ?? null,
                        $requirements['post_activity'] ?? 0,
                        $requirements['certificates'] ?? 0,
                        $requirements['verification_statements'] ?? 0,
                        $requirements['pnpki_application'] ?? 0,
                        $requirements['photos'] ?? 0,
                        $requirements['published'] ?? 0,
                        $requirements['published_link'] ?? null,
                        $activity_id
                    ]);
                } else {
                    // Insert new requirements
                    $stmt = $pdo->prepare("
                        INSERT INTO activity_requirements (
                            activity_id, request_letter, reply_letter, ad, `to`, to_number,
                            post_activity, certificates, verification_statements, 
                            pnpki_application, photos, published, published_link
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $activity_id,
                        $requirements['request_letter'] ?? 0,
                        $requirements['reply_letter'] ?? 0,
                        $requirements['ad'] ?? 0,
                        $requirements['to'] ?? 0,
                        $requirements['to_number'] ?? null,
                        $requirements['post_activity'] ?? 0,
                        $requirements['certificates'] ?? 0,
                        $requirements['verification_statements'] ?? 0,
                        $requirements['pnpki_application'] ?? 0,
                        $requirements['photos'] ?? 0,
                        $requirements['published'] ?? 0,
                        $requirements['published_link'] ?? null
                    ]);
                }
                
                $pdo->commit();
                $success_message = "Activity updated successfully! Refreshing page...";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = window.location.href.split('?')[0];
                    }, 1500);
                </script>";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error_message = "Failed to update activity: " . $e->getMessage();
            }
        } 
        // Handle delete action
        elseif ($_POST['action'] === 'delete' && isset($_POST['activity_id'])) {
            $activity_id = (int)$_POST['activity_id'];
            if ($activity_id <= 0) {
                throw new Exception("Invalid activity ID");
            }
            
            $stmt = $pdo->prepare("DELETE FROM activities WHERE id = ?");
            if ($stmt->execute([$activity_id])) {
                // Redirect to prevent form resubmission and trigger auto-scroll
                header("Location: " . basename(__FILE__) . "?deleted=1");
                exit();
            } else {
                throw new Exception("Failed to delete activity. Please try again.");
            }
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Fetch all projects for dropdown
$stmt = $pdo->query("SELECT id, title FROM projects ORDER BY title");
$projects = $stmt->fetchAll();

// Handle filter parameters
$project_filter = isset($_GET['project_filter']) ? (int)$_GET['project_filter'] : 0;
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
$start_date_filter = isset($_GET['start_date_filter']) ? $_GET['start_date_filter'] : '';
$end_date_filter = isset($_GET['end_date_filter']) ? $_GET['end_date_filter'] : '';
$search_filter = isset($_GET['search']) ? trim($_GET['search']) : '';
$view_mode = isset($_GET['view']) ? $_GET['view'] : 'calendar'; // Default to calendar view

// Build the base query
$params = [];
$where = [];

if (!empty($search_filter)) {
    $where[] = '(LOWER(a.title) LIKE LOWER(?) OR LOWER(a.description) LIKE LOWER(?) OR LOWER(p.title) LIKE LOWER(?))';
    $search_param = '%' . $search_filter . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($_GET['project_filter'])) {
    $where[] = 'a.project_id = ?';
    $params[] = (int)$_GET['project_filter'];
}

if (!empty($_GET['status_filter'])) {
    $where[] = 'LOWER(a.status) = LOWER(?)';
    $params[] = $_GET['status_filter'];
}

if (!empty($_GET['start_date_filter'])) {
    $where[] = 'a.end_date >= ?';
    $params[] = $_GET['start_date_filter'];
}

if (!empty($_GET['end_date_filter'])) {
    $where[] = 'a.start_date <= ?';
    $params[] = $_GET['end_date_filter'];
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Build the query with joins for project and requirements
$sql = "
    SELECT a.*, p.title as project_title,
           ar.request_letter, ar.reply_letter, ar.ad, ar.`to`, ar.to_number,
           ar.post_activity, ar.certificates, ar.verification_statements,
           ar.pnpki_application, ar.photos, ar.published, ar.published_link
    FROM activities a
    LEFT JOIN projects p ON a.project_id = p.id
    LEFT JOIN activity_requirements ar ON a.id = ar.activity_id
    $where_clause
    ORDER BY a.start_date DESC, a.title
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$activities = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Group requirements data
    $requirements = [];
    $requirementFields = [
        'request_letter', 'reply_letter', 'ad', 'to', 'to_number', 'post_activity',
        'certificates', 'verification_statements', 'pnpki_application', 'photos', 'published', 'published_link'
    ];
    
    foreach ($requirementFields as $field) {
        if (array_key_exists($field, $row)) {
            $requirements[$field] = $row[$field];
            unset($row[$field]);
        }
    }
    
    $row['requirements'] = $requirements;
    $activities[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Personal Monitoring System - Activities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-bg: #0a192f;
            --secondary-bg: rgba(16, 32, 56, 0.9);
            --accent-color: #64ffda;
            --accent-secondary: #7928ca;
            --text-white: #ffffff;
            --border-color: rgba(100, 255, 218, 0.1);
        }
        
        body {
            background-color: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(100, 255, 218, 0.1) 0%, transparent 50%),
                radial-gradient(at 100% 0%, rgba(121, 40, 202, 0.1) 0%, transparent 50%);
            color: var(--text-white);
            font-family: 'Space Grotesk', sans-serif;
        }
        
        .form-control, .form-select {
            background: rgba(16, 32, 56, 0.9);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(16, 32, 56, 0.95);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(100, 255, 218, 0.2);
            color: var(--text-white);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select option {
            background: var(--primary-bg);
            color: var(--text-white);
        }

        /* Modal Styles */
        .modal-content {
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
        }

        .modal-title {
            color: var(--text-white);
            font-weight: 600;
        }

        /* Sidebar Enhancement */
        .sidebar {
            min-height: 100vh;
            background: rgba(10, 25, 47, 0.95);
            border-right: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .sidebar h3 {
            color: var(--text-white);
            font-size: 1.5rem;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(100, 255, 218, 0.05);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(100, 255, 218, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 255, 218, 0.3);
        }

        .main-content {
            overflow-y: auto;
            height: 100vh;
            padding: 1.5rem;
            margin-left: 350px;
            max-width: calc(100% - 350px);
            box-sizing: border-box;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                max-width: 100%;
                padding: 1rem;
            }
        }

        /* Nav Item Styling */
        .nav-item {
            color: #a0aec0;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            opacity: 0.8;
        }

        .sidebar a:hover, .sidebar a.active {
            background: linear-gradient(135deg, rgba(100, 255, 218, 0.1), rgba(121, 40, 202, 0.1));
            color: var(--accent-color);
            transform: translateX(5px);
            opacity: 1;
        }

        .sidebar a i {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .sidebar a.active {
            background: linear-gradient(135deg, rgba(100, 255, 218, 0.2), rgba(121, 40, 202, 0.2));
            border-left: 4px solid var(--accent-color);
        }

        .main-content {
            padding: 30px;
            color: var(--text-white);
        }

        .card {
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            color: var(--text-white);
            margin: 0;
        }

        /* Table Header Colors */
        .table th {
            color: var(--accent-color);
            font-weight: 600;
            border-bottom: 2px solid var(--accent-color);
            padding: 15px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(100, 255, 218, 0.05);
        }

        /* Table Cell Colors */
        .table td {
            color: var(--text-white);
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            background: var(--secondary-bg);
        }

        .table tr:hover td {
            background: rgba(100, 255, 218, 0.05);
        }

        /* Button Enhancements */
        .btn-custom {
            background: linear-gradient(135deg, var(--accent-color) 0%, #4ad3b5 100%);
            color: var(--primary-bg);
            font-weight: 600;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(100, 255, 218, 0.4);
            color: var(--primary-bg);
        }

        .btn-custom i {
            font-size: 1.1rem;
        }

        /* Status Badge Colors */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
        }

        .status-pending {
            color: #ffd700;
            border-color: rgba(255, 215, 0, 0.3);
        }

        .status-in-progress {
            color: var(--accent-color);
            border-color: rgba(100, 255, 218, 0.3);
        }

        .status-completed {
            color: #00ff00;
            border-color: rgba(0, 255, 0, 0.3);
        }

        .status-on-hold {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(100, 255, 218, 0.2);
        }

        /* Custom scrollbar */
        .table-responsive::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #4dffd1;
        }
        
        /* Fixed header */
        .table thead th {
            position: sticky;
            top: 0;
            background-color: var(--secondary-bg);
            z-index: 10;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Responsive table container */
        .table-container {
            max-height: calc(100vh - 250px);
            min-height: 300px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        @media (max-width: 992px) {
            .table-container {
                max-height: calc(100vh - 280px);
            }
            
            .table {
                width: 100%;
                margin-bottom: 1rem;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table thead,
            .table tbody,
            .table th,
            .table td,
            .table tr {
                display: block;
            }
            
            .table thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            
            .table tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
            }
            
            .table td {
                border: none;
                border-bottom: 1px solid var(--border-color);
                position: relative;
                padding-left: 50%;
                width: 100%;
                box-sizing: border-box;
            }
            
            .table td:before {
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: 600;
                color: var(--accent-color);
            }
            
            .table td:nth-of-type(1):before { content: 'Project'; }
            .table td:nth-of-type(2):before { content: 'Activity'; }
            .table td:nth-of-type(3):before { content: 'Description'; }
            .table td:nth-of-type(4):before { content: 'Date Range'; }
            .table td:nth-of-type(5):before { content: 'Status'; }
            .table td:nth-of-type(6):before { content: 'Actions'; }
            
            .table td:last-child {
                border-bottom: none;
            }
            
            .table td .d-flex {
                justify-content: flex-end;
            }
        }

        .card {
            background: var(--secondary-bg);
            border: none;
            border-radius: 16px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(100, 255, 218, 0.2);
        }

        /* Calendar View Styles */
        .calendar-header {
            background: rgba(16, 32, 56, 0.8);
            border-bottom: 1px solid var(--border-color);
        }

        .calendar-grid {
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 1rem;
            min-height: 600px;
        }

        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
        }

        .weekday {
            text-align: center;
            font-weight: 600;
            color: var(--accent-color);
            padding: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            align-content: start;
        }

        .calendar-day {
            min-height: 100px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.5rem;
            background: rgba(16, 32, 56, 0.6);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .calendar-day:hover {
            background: rgba(16, 32, 56, 0.8);
            border-color: var(--accent-color);
            transform: translateY(-2px);
        }

        .calendar-day.other-month {
            opacity: 0.3;
            background: rgba(16, 32, 56, 0.3);
        }

        .calendar-day.today {
            border-color: var(--accent-color);
            background: rgba(100, 255, 218, 0.1);
        }

        .calendar-day.today .day-number {
            background: var(--accent-color);
            color: var(--primary-bg);
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .day-number {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-white);
            font-size: 0.9rem;
        }

        .calendar-activities {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .calendar-activity {
            background: rgba(100, 255, 218, 0.1);
            border: 1px solid rgba(100, 255, 218, 0.3);
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            color: var(--text-white);
            cursor: pointer;
            transition: all 0.2s ease;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .calendar-activity:hover {
            background: rgba(100, 255, 218, 0.2);
            border-color: var(--accent-color);
            transform: scale(1.02);
        }

        .calendar-activity.status-not-started {
            border-left: 3px solid #dc3545;
        }

        .calendar-activity.status-in-progress {
            border-left: 3px solid #ffc107;
        }

        .calendar-activity.status-completed {
            border-left: 3px solid #28a745;
        }

        .calendar-activity.status-on-hold {
            border-left: 3px solid #6c757d;
        }

        /* View Toggle Button Styles */
        .btn-group .btn-custom.active {
            background: var(--accent-color);
            color: var(--primary-bg);
            border-color: var(--accent-color);
        }

        /* Responsive Calendar */
        @media (max-width: 768px) {
            .calendar-day {
                min-height: 80px;
                padding: 0.25rem;
            }
            
            .calendar-activity {
                font-size: 0.7rem;
                padding: 0.2rem 0.3rem;
            }
            
            .weekday {
                font-size: 0.8rem;
                padding: 0.25rem;
            }
        }

        /* Repeat/Settings Section Styles */
        .settings-card .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(16, 32, 56, 0.9);
            border-bottom: 1px solid var(--border-color);
        }
        .settings-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .settings-subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.65);
        }
        .form-hint {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
        }
        .input-group-text {
            background: rgba(16, 32, 56, 0.9);
            border: 1px solid var(--border-color);
            color: var(--text-white);
        }
        .section-divider {
            border-color: rgba(255,255,255,0.1);
            margin: 0.5rem 0 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row h-100" style="margin-left: 0; margin-right: 0;">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main Content -->
            <div class="main-content animate__animated animate__fadeIn">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Activities Management</h4>
                    <div class="d-flex gap-2 align-items-center">
                        <!-- View Toggle Buttons -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-custom active" id="tableViewBtn" data-view="table">
                                <i class="bi bi-table me-1"></i> Table
                            </button>
                            <button type="button" class="btn btn-custom" id="calendarViewBtn" data-view="calendar">
                                <i class="bi bi-calendar3 me-1"></i> Calendar
                            </button>
                        </div>
                        <!-- Add Activity Button -->
                        <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                            <i class="bi bi-plus-circle me-2"></i> Add Activity
                        </button>
                    </div>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success animate__animated animate__fadeIn"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Activities Table -->
                <!-- Compact Filter Form -->
                <div class="card mb-3 animate__animated animate__fadeIn" id="filterSection">
                    <div class="card-body p-3">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small text-white mb-0">Search</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search activities..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-white mb-0">Project</label>
                                <select name="project_filter" class="form-select form-select-sm">
                                    <option value="">All Projects</option>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?php echo $project['id']; ?>" <?php echo (isset($_GET['project_filter']) && $_GET['project_filter'] == $project['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-white mb-0">Status</label>
                                <select name="status_filter" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="not started" <?php echo (isset($_GET['status_filter']) && strtolower($_GET['status_filter']) == 'not started') ? 'selected' : ''; ?>>Not Started</option>
                                    <option value="in progress" <?php echo (isset($_GET['status_filter']) && strtolower($_GET['status_filter']) == 'in progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo (isset($_GET['status_filter']) && strtolower($_GET['status_filter']) == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="on hold" <?php echo (isset($_GET['status_filter']) && strtolower($_GET['status_filter']) == 'on hold') ? 'selected' : ''; ?>>On Hold</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-white mb-0">From</label>
                                <input type="date" name="start_date_filter" class="form-control form-control-sm" value="<?php echo isset($_GET['start_date_filter']) ? htmlspecialchars($_GET['start_date_filter']) : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-white mb-0">To</label>
                                <input type="date" name="end_date_filter" class="form-control form-control-sm" value="<?php echo isset($_GET['end_date_filter']) ? htmlspecialchars($_GET['end_date_filter']) : ''; ?>">
                            </div>
                            <div class="col-md-1 d-flex align-items-end justify-content-end">
                                <div class="d-flex gap-2 w-100">
                                    <a href="activities.php?view=table" class="btn btn-custom" id="resetFilters" style="height: 48px;" title="Reset Filters">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                    <button type="submit" class="btn btn-custom" style="height: 48px;" title="Apply Filters">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card animate__animated animate__fadeInUp">
                    <div class="card-body p-0">
                        <div class="table-responsive table-container">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Project
                                            <?php if (isset($_GET['project_filter']) && !empty($_GET['project_filter'])): ?>
                                                <span class="badge bg-info ms-1">Filtered</span>
                                            <?php endif; ?>
                                        </th>
                                        <th>Activity
                                            <?php if (isset($_GET['status_filter']) && !empty($_GET['status_filter'])): ?>
                                                <span class="badge bg-info ms-1"><?php echo ucfirst(htmlspecialchars($_GET['status_filter'])); ?></span>
                                            <?php endif; ?>
                                        </th>
                                        <th>Description</th>
                                        <th>Date Range</th>
                                        <th>Status</th>
                                        <th>Drive Link</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($activity['project_title']); ?></td>
                                        <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($activity['description'])); ?></td>
                                        <td>
                                            <?php 
                                            $start_date = new DateTime($activity['start_date']);
                                            $end_date = new DateTime($activity['end_date']);
                                            
                                            if ($start_date->format('Y-m-d') === $end_date->format('Y-m-d')) {
                                                // Single day
                                                echo $start_date->format('l, F j, Y');
                                            } else if ($start_date->format('Y-m') === $end_date->format('Y-m')) {
                                                // Same month, different days
                                                echo $start_date->format('D, M j') . ' - ' . $end_date->format('D, M j, Y');
                                            } else if ($start_date->format('Y') === $end_date->format('Y')) {
                                                // Same year, different months
                                                echo $start_date->format('D, M j') . ' - ' . $end_date->format('D, M j, Y');
                                            } else {
                                                // Different years
                                                echo $start_date->format('D, M j, Y') . ' - ' . $end_date->format('D, M j, Y');
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $activity['status'])); ?>">
                                                <?php echo ucfirst($activity['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($activity['google_drive_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($activity['google_drive_link']); ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   data-bs-toggle="tooltip" 
                                                   title="<?php echo htmlspecialchars($activity['google_drive_link']); ?>">
                                                    <i class="bi bi-google-drive"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-white">No link</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm edit-activity" 
                                                data-id="<?php echo $activity['id']; ?>"
                                                data-project-id="<?php echo $activity['project_id']; ?>"
                                                data-title="<?php echo htmlspecialchars($activity['title']); ?>"
                                                data-description="<?php echo htmlspecialchars($activity['description']); ?>"
                                                data-google-drive-link="<?php echo isset($activity['google_drive_link']) ? htmlspecialchars($activity['google_drive_link']) : ''; ?>"
                                                data-published-link="<?php echo isset($activity['requirements']['published_link']) ? htmlspecialchars($activity['requirements']['published_link']) : ''; ?>"
                                                data-start-date="<?php echo $activity['start_date']; ?>"
                                                data-end-date="<?php echo $activity['end_date']; ?>"
                                                data-status="<?php echo $activity['status']; ?>"
                                                data-requirements='<?php echo json_encode($activity['requirements'] ?? []); ?>'
                                                data-bs-toggle="modal" data-bs-target="#editActivityModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="activity_id" value="<?php echo $activity['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this activity?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-info btn-sm view-requirements" 
                                                data-bs-toggle="tooltip" 
                                                title="View Requirements"
                                                data-requirements='<?php 
                                                    $stmt = $pdo->prepare("SELECT * FROM activity_requirements WHERE activity_id = ?");
                                                    $stmt->execute([$activity['id']]);
                                                    $requirements = $stmt->fetch(PDO::FETCH_ASSOC);
                                                    echo htmlspecialchars(json_encode($requirements ?: []), ENT_QUOTES, 'UTF-8'); 
                                                ?>'>
                                                <i class="bi bi-clipboard-check"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Calendar View (Hidden by default) -->
                <div class="card animate__animated animate__fadeIn" id="calendarView" style="display: none;">
                    <div class="card-body p-0">
                        <!-- Calendar Header -->
                        <div class="calendar-header d-flex justify-content-between align-items-center p-3 border-bottom">
                            <button type="button" class="btn btn-sm btn-outline-light" id="prevMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <h5 class="mb-0" id="currentMonth"></h5>
                            <button type="button" class="btn btn-sm btn-outline-light" id="nextMonth">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        
                        <!-- Calendar Grid -->
                        <div class="calendar-container p-3">
                            <div class="calendar-grid">
                                <!-- Weekday Headers -->
                                <div class="calendar-weekdays">
                                    <div class="weekday">Sun</div>
                                    <div class="weekday">Mon</div>
                                    <div class="weekday">Tue</div>
                                    <div class="weekday">Wed</div>
                                    <div class="weekday">Thu</div>
                                    <div class="weekday">Fri</div>
                                    <div class="weekday">Sat</div>
                                </div>
                                
                                <!-- Calendar Days -->
                                <div class="calendar-days" id="calendarDays">
                                    <!-- Days will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Activity Modal -->
    <div class="modal fade" id="addActivityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project</label>
                            <select class="form-control" id="project_id" name="project_id" required>
                                <?php if (empty($projects)): ?>
                                    <option value="">No projects available</option>
                                <?php else: ?>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?php echo $project['id']; ?>" 
                                            <?php echo (isset($_POST['project_id']) && $_POST['project_id'] == $project['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Activity Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Notes</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="google_drive_link" class="form-label">Google Drive Link</label>
                            <input type="url" class="form-control" id="google_drive_link" name="google_drive_link" placeholder="https://drive.google.com/...">
                            <div class="form-text text-muted">Paste the full URL to your Google Drive file or folder</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                                <div class="form-text text-muted">End date must be on or after start date.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <?php 
                                $statuses = ['in progress', 'completed'];
                                $current_status = isset($_POST['status']) ? strtolower(trim($_POST['status'])) : 'in progress';
                                foreach ($statuses as $status): 
                                    $selected = ($current_status === strtolower($status)) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $status; ?>" <?php echo $selected; ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Repeat Section -->
                        <div class="card settings-card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex flex-column">
                                    <h6 class="settings-title"><i class="bi bi-arrow-repeat"></i> Repeat</h6>
                                    <span class="settings-subtitle">Create a series of activities on a schedule</span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="repeat_enabled" name="repeat_enabled" value="1">
                                    <label class="form-check-label" for="repeat_enabled">Enable</label>
                                </div>
                            </div>
                            <div class="card-body" id="repeat_options" style="display:none;">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label small text-white-50">Frequency</label>
                                        <select class="form-select" id="repeat_frequency" name="repeat_frequency">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-white-50">Interval</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Every</span>
                                            <input type="number" class="form-control" id="repeat_interval" name="repeat_interval" min="1" value="1">
                                            <span class="input-group-text" id="interval_label">day(s)</span>
                                        </div>
                                        <div class="form-hint mt-1">How many units between repeats</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-white-50">Ends</label>
                                        <select class="form-select" id="repeat_end_type" name="repeat_end_type">
                                            <option value="never">Never</option>
                                            <option value="on_date">On date</option>
                                            <option value="after_count">After N occurrences</option>
                                        </select>
                                    </div>
                                </div>
                                <hr class="section-divider">
                                <div class="row g-3" id="repeat_end_on_date" style="display:none;">
                                    <div class="col-md-6">
                                        <label class="form-label small text-white-50">End Date</label>
                                        <input type="date" class="form-control" id="repeat_end_date" name="repeat_end_date">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-hint">No occurrences will be scheduled after this date.</div>
                                    </div>
                                </div>
                                <div class="row g-3" id="repeat_end_after_count" style="display:none;">
                                    <div class="col-md-6">
                                        <label class="form-label small text-white-50">Occurrences</label>
                                        <input type="number" class="form-control" id="repeat_count" name="repeat_count" min="1" value="5">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-hint">Number of total activities to create in this series.</div>
                                    </div>
                                </div>
                                <div class="form-hint mt-3">
                                    Each occurrence is saved as a separate activity preserving the selected date range.
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Activity</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Activity Modal -->
    <div class="modal fade" id="editActivityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="activity_id" id="edit_activity_id">
                        <div class="mb-3">
                            <label for="edit_project_id" class="form-label">Project</label>
                            <select class="form-control" id="edit_project_id" name="project_id" required>
                                <?php if (empty($projects)): ?>
                                    <option value="">No projects available</option>
                                <?php else: ?>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?php echo $project['id']; ?>">
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Activity Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_google_drive_link" class="form-label">Google Drive Link</label>
                            <input type="url" class="form-control" id="edit_google_drive_link" name="google_drive_link" placeholder="https://drive.google.com/...">
                            <div class="form-text text-white-50">Paste the full URL to your Google Drive file or folder</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                                <div class="form-text text-white-50">End date must be on or after start date.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <?php 
                                $statuses = ['in progress', 'completed'];
                                foreach ($statuses as $status): 
                                ?>
                                    <option value="<?php echo $status; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Requirements Status -->
                        <div class="card mb-3">
                            <div class="card-header bg-dark text-white">
                                <h6 class="mb-0">Requirements Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_request_letter" name="requirements[request_letter]" value="1">
                                            <label class="form-check-label" for="edit_request_letter">Request Letter</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_reply_letter" name="requirements[reply_letter]" value="1">
                                            <label class="form-check-label" for="edit_reply_letter">Reply Letter</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_ad" name="requirements[ad]" value="1">
                                            <label class="form-check-label" for="edit_ad">AD</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_to" name="requirements[to]" value="1">
                                            <label class="form-check-label" for="edit_to">TO</label>
                                        </div>
                                        <div id="to_number_container" class="mb-2 ms-4" style="display: none;">
                                            <label for="edit_to_number" class="form-label small">TO #</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R13-</span>
                                                <input type="text" class="form-control" id="edit_to_number" name="requirements[to_number]" placeholder="Enter TO number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_post_activity" name="requirements[post_activity]" value="1">
                                            <label class="form-check-label" for="edit_post_activity">Post Activity</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_certificates" name="requirements[certificates]" value="1">
                                            <label class="form-check-label" for="edit_certificates">Certificates</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_verification_statements" name="requirements[verification_statements]" value="1">
                                            <label class="form-check-label" for="edit_verification_statements">Verification Statements</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_pnpki_application" name="requirements[pnpki_application]" value="1">
                                            <label class="form-check-label" for="edit_pnpki_application">PNPKI Application</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input requirement-checkbox" type="checkbox" id="edit_photos" name="requirements[photos]" value="1">
                                            <label class="form-check-label" for="edit_photos">Photos</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="edit_published" name="requirements[published]" value="1">
                                        <label class="form-check-label" for="edit_published">Published</label>
                                    </div>
                                    <div id="published_link_container" class="mt-2" style="display: none;">
                                        <label for="edit_published_link" class="form-label">Published Link</label>
                                        <input type="url" class="form-control" id="edit_published_link" name="requirements[published_link]" placeholder="https://example.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Activity</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
            document.querySelectorAll('.live-time').forEach(el => {
                el.textContent = timeString;
            });
        }

        // Update clock immediately and then every minute
        updateClock();
        setInterval(updateClock, 60000);

        // Update date when needed (e.g., at midnight)
        function updateDate() {
            const now = new Date();
            const dateString = now.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
            document.querySelectorAll('.current-date').forEach(el => {
                el.textContent = dateString;
            });
        }

        // Update date on page load
        updateDate();

        // Delete activity function (global scope)
        function deleteActivity(activityId) {
            if (confirm('Are you sure you want to delete this activity?')) {
                // Create a form to submit the delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'activity_id';
                idInput.value = activityId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Toggle TO number field
            function toggleToNumber(show) {
                const container = document.getElementById('to_number_container');
                if (container) {
                    container.style.display = show ? 'block' : 'none';
                }
            }
            
            // Toggle published link field
            function togglePublishedLink(show) {
                const container = document.getElementById('published_link_container');
                const publishedLinkInput = document.getElementById('edit_published_link');
                
                if (container) {
                    container.style.display = show ? 'block' : 'none';
                    
                    // Clear the published link when hiding the container (unchecked)
                    if (!show && publishedLinkInput) {
                        publishedLinkInput.value = '';
                    }
                    if (show) {
                        container.querySelector('input').focus();
                    }
                }
            }

            // Toggle published link when checkbox changes
            document.getElementById('edit_published')?.addEventListener('change', function() {
                togglePublishedLink(this.checked);
            });

            // Add Activity Form
            const addActivityForm = document.querySelector('#addActivityModal form');
            const addStartDateInput = document.getElementById('start_date');
            const addEndDateInput = document.getElementById('end_date');
            
            // Edit Activity Form
            const editActivityForm = document.querySelector('#editActivityModal form');
            const editStartDateInput = document.getElementById('edit_start_date');
            const editEndDateInput = document.getElementById('edit_end_date');

            // Function to setup date validation
            function setupDateValidation(startInput, endInput, form) {
                // Update end date min when start date changes
                if (startInput && endInput) {
                    startInput.addEventListener('change', function() {
                        if (this.value) {
                            endInput.min = this.value;
                            if (endInput.value && new Date(endInput.value) < new Date(this.value)) {
                                endInput.value = this.value;
                            }
                        }
                    });
                    
                    // Initialize end date min if start date is already set
                    if (startInput.value) {
                        endInput.min = startInput.value;
                    }
                }
            }

            // Setup validation for add activity form
            if (addActivityForm) {
                setupDateValidation(addStartDateInput, addEndDateInput, addActivityForm);
                
                // Handle form submission with validation
                addActivityForm.addEventListener('submit', function(e) {
                    // Basic validation is handled by HTML5 required attributes
                    // Additional validation for dates
                    const startDate = new Date(addStartDateInput.value);
                    const endDate = new Date(addEndDateInput.value);
                    
                    if (endDate < startDate) {
                        e.preventDefault();
                        alert('End date cannot be before start date.');
                        return false;
                    }

                    // Repeat validation
                    const repeatEnabled = document.getElementById('repeat_enabled')?.checked;
                    if (repeatEnabled) {
                        const endType = document.getElementById('repeat_end_type')?.value || 'never';
                        if (endType === 'on_date') {
                            const endRepeatInput = document.getElementById('repeat_end_date');
                            if (!endRepeatInput.value) {
                                e.preventDefault();
                                alert('Please provide a repeat end date.');
                                return false;
                            }
                            const repeatEndDate = new Date(endRepeatInput.value);
                            if (repeatEndDate < startDate) {
                                e.preventDefault();
                                alert('Repeat end date cannot be before the start date.');
                                return false;
                            }
                        } else if (endType === 'after_count') {
                            const count = parseInt(document.getElementById('repeat_count')?.value || '0', 10);
                            if (!(count > 0)) {
                                e.preventDefault();
                                alert('Please enter a valid number of occurrences (> 0).');
                                return false;
                            }
                        }
                    }
                    
                    // If validation passes, the form will submit normally
                    return true;
                });

                // Repeat UI wiring
                const repeatSwitch = document.getElementById('repeat_enabled');
                const repeatOptions = document.getElementById('repeat_options');
                const freq = document.getElementById('repeat_frequency');
                const intervalLabel = document.getElementById('interval_label');
                const endTypeSel = document.getElementById('repeat_end_type');
                const endOnDate = document.getElementById('repeat_end_on_date');
                const endAfterCount = document.getElementById('repeat_end_after_count');
                const endDateInput = document.getElementById('repeat_end_date');

                function updateIntervalLabel() {
                    if (!freq || !intervalLabel) return;
                    const map = { daily: 'day(s)', weekly: 'week(s)', monthly: 'month(s)', yearly: 'year(s)' };
                    intervalLabel.textContent = map[freq.value] || 'day(s)';
                }

                function updateEndTypeVisibility() {
                    const type = endTypeSel?.value || 'never';
                    if (endOnDate) endOnDate.style.display = (type === 'on_date') ? 'flex' : 'none';
                    if (endAfterCount) endAfterCount.style.display = (type === 'after_count') ? 'flex' : 'none';
                }

                function toggleRepeatOptions() {
                    if (!repeatOptions || !repeatSwitch) return;
                    repeatOptions.style.display = repeatSwitch.checked ? 'block' : 'none';
                }

                repeatSwitch?.addEventListener('change', toggleRepeatOptions);
                freq?.addEventListener('change', updateIntervalLabel);
                endTypeSel?.addEventListener('change', updateEndTypeVisibility);

                // Initialize on load
                toggleRepeatOptions();
                updateIntervalLabel();
                updateEndTypeVisibility();

                // Keep repeat end date min in sync with start date
                if (endDateInput && addStartDateInput) {
                    const syncRepeatEndMin = () => {
                        endDateInput.min = addStartDateInput.value || '';
                    };
                    addStartDateInput.addEventListener('change', syncRepeatEndMin);
                    syncRepeatEndMin();
                }
            }


            // Setup validation for edit activity form
            if (editActivityForm) {
                setupDateValidation(editStartDateInput, editEndDateInput, editActivityForm);
                
                // Handle form submission for requirements
                editActivityForm.addEventListener('submit', function(e) {
                    // Convert requirements checkboxes to JSON
                    const requirements = {};
                    document.querySelectorAll('.requirement-checkbox, #edit_published').forEach(checkbox => {
                        const name = checkbox.name.replace('requirements[', '').replace(']', '');
                        requirements[name] = checkbox.checked ? 1 : 0;
                    });
                    
                    // Add published link if exists
                    const publishedLink = document.getElementById('edit_published_link');
                    if (publishedLink && publishedLink.value) {
                        requirements['published_link'] = publishedLink.value;
                    }
                    
                    // Add TO number if exists
                    const toNumber = document.getElementById('edit_to_number');
                    if (toNumber && toNumber.value) {
                        requirements['to_number'] = toNumber.value;
                    }
                    
                    // Add hidden input with requirements JSON
                    let requirementsInput = document.getElementById('activity_requirements');
                    if (!requirementsInput) {
                        requirementsInput = document.createElement('input');
                        requirementsInput.type = 'hidden';
                        requirementsInput.name = 'requirements';
                        requirementsInput.id = 'activity_requirements';
                        this.appendChild(requirementsInput);
                    }
                    requirementsInput.value = JSON.stringify(requirements);
                });
                
                // Handle form submission with validation
                editActivityForm.addEventListener('submit', function(e) {
                    // Basic validation is handled by HTML5 required attributes
                    // Additional validation for dates
                    const startDate = new Date(editStartDateInput.value);
                    const endDate = new Date(editEndDateInput.value);
                    
                    if (endDate < startDate) {
                        e.preventDefault();
                        alert('End date cannot be before start date.');
                        return false;
                    }
                    
                    // If validation passes, the form will submit normally
                    return true;
                });

                // Track active popover state and related elements
                let activePopover = null;
                let activeButton = null;
                let clickOutsideHandler = null;
                
                // Function to close active popover and clean up
                const closeActivePopover = () => {
                    if (activePopover) {
                        // Remove scroll and click handlers
                        if (clickOutsideHandler) {
                            document.removeEventListener('click', clickOutsideHandler);
                            window.removeEventListener('scroll', closeActivePopover, { passive: true });
                            clickOutsideHandler = null;
                        }
                        
                        // Hide the popover
                        activePopover.hide();
                        
                        // Reset active elements
                        if (activeButton) {
                            activeButton = null;
                        }
                        activePopover = null;
                    }
                };

                // Handle clicks outside the popover
                clickOutsideHandler = (e) => {
                    if (activeButton && !activeButton.contains(e.target) && 
                        !document.querySelector('.popover.show')?.contains(e.target)) {
                        closeActivePopover();
                    }
                };

                // Add scroll handler with debounce
                let scrollTimeout;
                const handleScroll = () => {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        closeActivePopover();
                    }, 100);
                };
                
                // Use passive scroll listener for better performance
                const scrollOptions = { passive: true };
                
                // Function to set up event listeners
                const setupEventListeners = () => {
                    if (clickOutsideHandler) {
                        document.removeEventListener('click', clickOutsideHandler);
                        window.removeEventListener('scroll', handleScroll, scrollOptions);
                    }
                    
                    document.addEventListener('click', clickOutsideHandler);
                    window.addEventListener('scroll', handleScroll, scrollOptions);
                };
                
                // Initial setup
                setupEventListeners();
                
                // Handle view requirements button click
                document.querySelectorAll('.view-requirements').forEach(button => {
                    // Initialize popover
                    const popover = new bootstrap.Popover(button, {
                        html: true,
                        trigger: 'manual',
                        placement: 'left',
                        container: 'body',
                        sanitize: false
                    });
                    
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Toggle if clicking the same button
                        if (activePopover === popover) {
                            closeActivePopover();
                            return;
                        }
                        
                        // Close any other open popover
                        closeActivePopover();
                        
                        // Get requirements data
                        const requirements = JSON.parse(this.getAttribute('data-requirements') || '{}');
                        
                        // Build requirements list HTML
                        let requirementsList = '<div class="requirements-popover p-2 text-dark">';
                        requirementsList += '<h6 class="border-bottom pb-2 mb-2 text-dark">Requirements Status</h6>';
                        requirementsList += '<ul class="list-unstyled mb-0">';
                        
                        const requirementLabels = {
                            'request_letter': 'Request Letter',
                            'reply_letter': 'Reply Letter',
                            'ad': 'AD',
                            'to': 'TO',
                            'post_activity': 'Post Activity',
                            'certificates': 'Certificates',
                            'verification_statements': 'Verification Statements',
                            'pnpki_application': 'PNPKI Application',
                            'photos': 'Photos',
                            'published': 'Published'
                        };
                        
                        Object.entries(requirementLabels).forEach(([key, label]) => {
                            const isChecked = requirements[key] === 1 || requirements[key] === '1';
                            const iconClass = isChecked ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger';
                            
                            requirementsList += `
                                <li class="d-flex align-items-center mb-1">
                                    <i class="bi ${iconClass} me-2"></i>
                                    <span>${label}</span>`;
                            
                            if (key === 'published' && isChecked && requirements.published_link) {
                                requirementsList += `
                                    <a href="${requirements.published_link}" 
                                       target="_blank" 
                                       class="ms-2 text-info text-decoration-none">
                                        View Link <i class="bi-box-arrow-up-right"></i>
                                    </a>`;
                            }
                            
                            requirementsList += '</li>';
                        });
                        
                        requirementsList += '</ul></div>';
                        
                        // Update and show popover
                        popover._config.title = '<div class="d-flex justify-content-between align-items-center">' +
                                             '<span class="text-dark">Activity Requirements</span>' +
                                             '</div>';
                        popover._config.content = requirementsList;
                        popover.show();
                        
                        // Set as active popover and button
                        activePopover = popover;
                        activeButton = button;
                        
                        // Update event listeners
                        setupEventListeners();
                        
                        // Add close button handler
                        const popoverElement = button.nextElementSibling;
                        if (popoverElement && popoverElement.classList.contains('popover')) {
                            const closeButton = popoverElement.querySelector('.btn-close');
                            if (closeButton) {
                                closeButton.addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    closeActivePopover();
                                });
                            }
                        }
                    });
                });
                
                // Handle edit button click
                document.querySelectorAll('.edit-activity').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const projectId = this.getAttribute('data-project-id');
                        const title = this.getAttribute('data-title');
                        const description = this.getAttribute('data-description');
                        const googleDriveLink = button.getAttribute('data-google-drive-link');
                        const startDate = button.getAttribute('data-start-date');
                        const endDate = this.getAttribute('data-end-date');
                        const status = this.getAttribute('data-status');
                        const requirements = JSON.parse(this.getAttribute('data-requirements') || '{}');

                        // Set form values
                        document.getElementById('edit_activity_id').value = id;
                        document.getElementById('edit_project_id').value = projectId;
                        document.getElementById('edit_title').value = title;
                        document.getElementById('edit_description').value = description;
                        document.getElementById('edit_google_drive_link').value = googleDriveLink || '';
                        document.getElementById('edit_start_date').value = startDate;
                        document.getElementById('edit_end_date').value = endDate;
                        document.getElementById('edit_status').value = status;
                        
                        // Set requirements checkboxes
                        const requirementFields = [
                            'request_letter', 'reply_letter', 'ad', 'to', 
                            'post_activity', 'certificates', 'verification_statements',
                            'pnpki_application', 'photos', 'published'
                        ];
                        
                        requirementFields.forEach(field => {
                            const checkbox = document.getElementById(`edit_${field}`);
                            if (checkbox) {
                                checkbox.checked = requirements[field] === 1 || requirements[field] === '1';
                                
                                // Initialize TO number field if TO is checked
                                if (field === 'to') {
                                    toggleToNumber(checkbox.checked);
                                    if (requirements['to_number']) {
                                        let toNumber = requirements['to_number'];
                                        // Remove existing R13- prefix if present to avoid duplication
                                        toNumber = toNumber.replace(/^R13-?\s*/, '');
                                        document.getElementById('edit_to_number').value = toNumber;
                                    }
                                }
                            }
                        });
                        
                        // Set published link if exists
                        if (requirements.published_link) {
                            document.getElementById('edit_published_link').value = requirements.published_link;
                            document.getElementById('published_link_container').style.display = 'block';
                        }
                        
                        // Add event listener for TO checkbox
                        const toCheckbox = document.getElementById('edit_to');
                        if (toCheckbox) {
                            toCheckbox.addEventListener('change', function() {
                                toggleToNumber(this.checked);
                            });
                        }
                        
                        // Format TO number when saving
                        const toNumberInput = document.getElementById('edit_to_number');
                        if (toNumberInput) {
                            toNumberInput.addEventListener('blur', function() {
                                let value = this.value.trim();
                                if (value && !value.startsWith('R13-')) {
                                    this.value = 'R13-' + value.replace(/^R13-?\s*/, '');
                                }
                            });
                        }
                        
                        // Update end date min based on start date
                        if (editStartDateInput && editEndDateInput) {
                            editStartDateInput.dispatchEvent(new Event('change'));
                        }
                    });
                });
                
                // Auto-scroll to nearest date activities on page refresh
                function scrollToNearestDateActivity() {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Set to start of day for comparison
                    
                    const tableRows = document.querySelectorAll('tbody tr');
                    let nearestRow = null;
                    let smallestDiff = Infinity;
                    
                    tableRows.forEach(row => {
                        // Extract date from the row - date is in the 4th column (index 3)
                        const dateCell = row.cells[3];
                        if (dateCell) {
                            const dateText = dateCell.textContent.trim();
                            
                            // Parse the formatted date string
                            // Examples: "Monday, January 15, 2024", "Mon, Jan 15 - Tue, Jan 16, 2024"
                            let activityDate = null;
                            
                            // Try to match single date format: "Day, Month Day, Year"
                            const singleDateMatch = dateText.match(/([A-Za-z]+,\s+[A-Za-z]+\s+\d{1,2},\s+\d{4})/);
                            if (singleDateMatch) {
                                activityDate = new Date(singleDateMatch[1]);
                            } else {
                                // Try to match date range format - take the start date
                                const rangeMatch = dateText.match(/([A-Za-z]+,\s+[A-Za-z]+\s+\d{1,2})\s*-\s*[A-Za-z]+,\s+[A-Za-z]+\s+\d{1,2},\s+(\d{4})/);
                                if (rangeMatch) {
                                    const startDateFormat = rangeMatch[1] + ', ' + rangeMatch[2];
                                    activityDate = new Date(startDateFormat);
                                }
                            }
                            
                            if (activityDate && !isNaN(activityDate.getTime())) {
                                activityDate.setHours(0, 0, 0, 0);
                                
                                // Calculate difference in days
                                const timeDiff = Math.abs(activityDate.getTime() - today.getTime());
                                const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                                
                                // Check if this is the nearest date
                                if (dayDiff < smallestDiff) {
                                    smallestDiff = dayDiff;
                                    nearestRow = row;
                                }
                            }
                        }
                    });
                    
                    // Scroll to the nearest date activity if found
                    if (nearestRow) {
                        // Add highlight effect
                        nearestRow.style.backgroundColor = 'rgba(100, 255, 218, 0.2)';
                        nearestRow.style.transition = 'background-color 0.5s ease';
                        
                        // Scroll into view
                        nearestRow.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        
                        // Remove highlight after 3 seconds
                        setTimeout(() => {
                            nearestRow.style.backgroundColor = '';
                        }, 3000);
                    }
                }
                
                // Check if page was refreshed and auto-scroll
                const navigationEntries = performance.getEntriesByType('navigation');
                if (navigationEntries.length > 0 && navigationEntries[0].type === 'reload') {
                    // Page was refreshed, auto-scroll after a short delay
                    setTimeout(scrollToNearestDateActivity, 500);
                } else {
                    // Also check for URL parameters that might indicate a refresh
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('refresh') || urlParams.has('success') || urlParams.has('updated') || urlParams.has('deleted')) {
                        setTimeout(scrollToNearestDateActivity, 500);
                    }
                }
                
                // Handle reset button click to auto-scroll
                const resetButton = document.getElementById('resetFilters');
                if (resetButton) {
                    resetButton.addEventListener('click', function(e) {
                        // The reset button is a link that redirects to activities.php
                        // We need to set a flag to trigger auto-scroll after the redirect
                        sessionStorage.setItem('autoScrollToNearestDate', 'true');
                    });
                }
                
                // Check for session storage flag and trigger auto-scroll
                if (sessionStorage.getItem('autoScrollToNearestDate') === 'true') {
                    sessionStorage.removeItem('autoScrollToNearestDate'); // Clear the flag
                    setTimeout(scrollToNearestDateActivity, 500);
                }
                
                // Calendar View Functionality
                class CalendarView {
                    constructor() {
                        this.currentDate = new Date();
                        this.currentMonth = this.currentDate.getMonth();
                        this.currentYear = this.currentDate.getFullYear();
                        this.activities = this.parseActivitiesFromTable();
                        this.init();
                    }
                    
                    init() {
                        this.setupEventListeners();
                        this.renderCalendar();
                        // Check URL parameter first, then localStorage, then default to calendar
                        try {
                            const urlParams = new URLSearchParams(window.location.search);
                            const urlView = urlParams.get('view');
                            if (urlView === 'table' || urlView === 'calendar') {
                                if (urlView === 'table') {
                                    this.showTableView(false); // Don't update URL, just show
                                } else {
                                    this.showCalendarView(false); // Don't update URL, just show
                                }
                            } else {
                                // Check localStorage as fallback
                                const savedView = localStorage.getItem('activitiesViewMode');
                                if (savedView === 'table') {
                                    this.showTableView(false);
                                } else {
                                    this.showCalendarView(false);
                                }
                            }
                        } catch (e) {
                            // Fallback to calendar view
                            this.showCalendarView(false);
                        }
                    }
                    
                    setupEventListeners() {
                        // View toggle buttons
                        document.getElementById('tableViewBtn').addEventListener('click', () => {
                            this.showTableView(true); // Update URL when clicked
                        });
                        
                        document.getElementById('calendarViewBtn').addEventListener('click', () => {
                            this.showCalendarView(true); // Update URL when clicked
                        });
                        
                        // Calendar navigation
                        document.getElementById('prevMonth').addEventListener('click', () => {
                            this.previousMonth();
                        });
                        
                        document.getElementById('nextMonth').addEventListener('click', () => {
                            this.nextMonth();
                        });
                    }
                    
                    showTableView(updateUrl = true) {
                        document.getElementById('calendarView').style.display = 'none';
                        // Show the table view (find the table container)
                        const tableCard = document.querySelector('.card:has(.table)');
                        if (tableCard) {
                            tableCard.style.display = 'block';
                        }
                        
                        // Update button states
                        document.getElementById('tableViewBtn').classList.add('active');
                        document.getElementById('calendarViewBtn').classList.remove('active');
                        
                        // Auto-scroll to nearest date activity
                        setTimeout(scrollToNearestDateActivity, 300);

                        // Update URL if requested
                        if (updateUrl) {
                            this.updateViewURL('table');
                        }

                        // Persist view mode
                        try { localStorage.setItem('activitiesViewMode', 'table'); } catch (e) {}
                    }
                    
                    showCalendarView(updateUrl = true) {
                        // Hide the table view
                        const tableCard = document.querySelector('.card:has(.table)');
                        if (tableCard) {
                            tableCard.style.display = 'none';
                        }
                        
                        // Hide the filter section
                        const filterSection = document.getElementById('filterSection');
                        if (filterSection) {
                            filterSection.style.display = 'none';
                        }
                        
                        // Show calendar view
                        document.getElementById('calendarView').style.display = 'block';
                        
                        // Update button states
                        document.getElementById('calendarViewBtn').classList.add('active');
                        document.getElementById('tableViewBtn').classList.remove('active');

                        // Update URL if requested
                        if (updateUrl) {
                            this.updateViewURL('calendar');
                        }

                        // Persist view mode
                        try { localStorage.setItem('activitiesViewMode', 'calendar'); } catch (e) {}
                    }
                    
                    updateViewURL(view) {
                        try {
                            const url = new URL(window.location);
                            url.searchParams.set('view', view);
                            window.history.replaceState({}, '', url);
                        } catch (e) {
                            // Fallback if URL API is not available
                            const currentURL = window.location.pathname + window.location.search;
                            const separator = currentURL.includes('?') ? '&' : '?';
                            const newURL = window.location.pathname + window.location.search.replace(/[\?&]view=[^&]*/, '') + separator + 'view=' + view;
                            window.history.replaceState({}, '', newURL);
                        }
                    }
                    
                    parseActivitiesFromTable() {
                        const activities = [];
                        const tableRows = document.querySelectorAll('tbody tr');
                        
                        tableRows.forEach(row => {
                            const cells = row.cells;
                            if (cells.length >= 6) {
                                const project = cells[0].textContent.trim();
                                const title = cells[1].textContent.trim();
                                const dateText = cells[3].textContent.trim();
                                const statusCell = cells[4].querySelector('.status-badge');
                                const status = statusCell ? statusCell.textContent.trim().toLowerCase() : 'not started';
                                
                                // Extract requirements data and gdrive link from the edit button
                                const editButton = row.querySelector('.edit-activity');
                                let requirements = {};
                                let googleDriveLink = '';
                                let activityId = null;
                                if (editButton) {
                                    try {
                                        requirements = JSON.parse(editButton.getAttribute('data-requirements') || '{}');
                                    } catch (e) {
                                        console.error('Error parsing requirements:', e);
                                    }
                                    googleDriveLink = editButton.getAttribute('data-google-drive-link') || '';
                                    activityId = editButton.getAttribute('data-id') || null;
                                }
                                
                                // Parse date range
                                const dates = this.parseDateRange(dateText);
                                if (dates.start && dates.end) {
                                    activities.push({
                                        title: title,
                                        project: project,
                                        startDate: dates.start,
                                        endDate: dates.end,
                                        status: status,
                                        requirements: requirements,
                                        googleDriveLink: googleDriveLink,
                                        activityId: activityId
                                    });
                                }
                            }
                        });
                        
                        return activities;
                    }
                    
                    parseDateRange(dateText) {
                        // Parse formatted date strings generated by PHP:
                        // Single day: "Monday, January 15, 2024"
                        // Same month: "Mon, Jan 15 - Tue, Jan 16, 2024"
                        // Same year: "Mon, Jan 15 - Tue, Feb 16, 2024"
                        // Different years: "Mon, Jan 15, 2023 - Tue, Feb 16, 2024"
                        let startDate = null;
                        let endDate = null;
                        
                        // Try single date format: "Day, Month Day, Year"
                        const singleDateMatch = dateText.match(/^([A-Za-z]+,\s+[A-Za-z]+\s+\d{1,2},\s+\d{4})$/);
                        if (singleDateMatch) {
                            startDate = new Date(singleDateMatch[1]);
                            endDate = new Date(singleDateMatch[1]);
                            return { start: startDate, end: endDate };
                        }
                        
                        // Try date range formats
                        if (dateText.includes(' - ')) {
                            const parts = dateText.split(' - ');
                            if (parts.length === 2) {
                                const startPart = parts[0].trim();
                                const endPart = parts[1].trim();
                                
                                // Case 1: Same month format: "Mon, Jan 15 - Tue, Jan 16, 2024"
                                // End part has year, start part doesn't
                                if (!startPart.includes(',') || startPart.split(',').length === 2) {
                                    const endYearMatch = endPart.match(/(\d{4})$/);
                                    if (endYearMatch) {
                                        const year = endYearMatch[1];
                                        
                                        // Parse start date (add year from end date)
                                        const startWithYear = startPart + ', ' + year;
                                        startDate = new Date(startWithYear);
                                        
                                        // Parse end date
                                        endDate = new Date(endPart);
                                        
                                        if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                                            return { start: startDate, end: endDate };
                                        }
                                    }
                                }
                                
                                // Case 2: Different years format: "Mon, Jan 15, 2023 - Tue, Feb 16, 2024"
                                // Both parts have years
                                if (startPart.includes(',') && startPart.split(',').length === 3 && 
                                    endPart.includes(',') && endPart.split(',').length === 3) {
                                    startDate = new Date(startPart);
                                    endDate = new Date(endPart);
                                    
                                    if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                                        return { start: startDate, end: endDate };
                                    }
                                }
                                
                                // Case 3: Same year, different months: "Mon, Jan 15 - Tue, Feb 16, 2024"
                                // End part has year, start part doesn't (same as case 1, but let's try a different approach)
                                const endYearMatch2 = endPart.match(/,\s*(\d{4})$/);
                                if (endYearMatch2) {
                                    const year = endYearMatch2[1];
                                    
                                    // Try to parse start date by adding year
                                    const startWithYear2 = startPart.match(/^[A-Za-z]+,\s+[A-Za-z]+\s+\d{1,2}$/) 
                                        ? startPart + ', ' + year 
                                        : startPart;
                                    
                                    startDate = new Date(startWithYear2);
                                    endDate = new Date(endPart);
                                    
                                    if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                                        return { start: startDate, end: endDate };
                                    }
                                }
                            }
                        }
                        
                        // Fallback: Try to extract any valid date from the text
                        const dateMatches = dateText.match(/([A-Za-z]+,\s+[A-Za-z]+\s+\d{1,2},\s+\d{4})/g);
                        if (dateMatches && dateMatches.length > 0) {
                            startDate = new Date(dateMatches[0]);
                            endDate = new Date(dateMatches[dateMatches.length - 1]);
                            
                            if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                                return { start: startDate, end: endDate };
                            }
                        }
                        
                        return { start: null, end: null };
                    }
                    
                    previousMonth() {
                        this.currentMonth--;
                        if (this.currentMonth < 0) {
                            this.currentMonth = 11;
                            this.currentYear--;
                        }
                        this.renderCalendar();
                    }
                    
                    nextMonth() {
                        this.currentMonth++;
                        if (this.currentMonth > 11) {
                            this.currentMonth = 0;
                            this.currentYear++;
                        }
                        this.renderCalendar();
                    }
                    
                    renderCalendar() {
                        this.updateMonthHeader();
                        this.renderCalendarDays();
                    }
                    
                    updateMonthHeader() {
                        const monthNames = [
                            'January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December'
                        ];
                        
                        const monthYear = `${monthNames[this.currentMonth]} ${this.currentYear}`;
                        document.getElementById('currentMonth').textContent = monthYear;
                    }
                    
                    renderCalendarDays() {
                        const calendarDays = document.getElementById('calendarDays');
                        calendarDays.innerHTML = '';
                        
                        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                        const startDate = new Date(firstDay);
                        startDate.setDate(startDate.getDate() - firstDay.getDay());
                        
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        
                        for (let i = 0; i < 42; i++) {
                            const currentDay = new Date(startDate);
                            currentDay.setDate(startDate.getDate() + i);
                            
                            const dayElement = this.createDayElement(currentDay, today);
                            calendarDays.appendChild(dayElement);
                        }
                    }
                    
                    createDayElement(date, today) {
                        const dayDiv = document.createElement('div');
                        dayDiv.className = 'calendar-day';
                        
                        // Add classes for styling
                        if (date.getMonth() !== this.currentMonth) {
                            dayDiv.classList.add('other-month');
                        }
                        
                        if (date.getTime() === today.getTime()) {
                            dayDiv.classList.add('today');
                        }
                        
                        // Day number
                        const dayNumber = document.createElement('div');
                        dayNumber.className = 'day-number';
                        dayNumber.textContent = date.getDate();
                        dayDiv.appendChild(dayNumber);
                        
                        // Activities for this day
                        const activitiesDiv = document.createElement('div');
                        activitiesDiv.className = 'calendar-activities';
                        
                        const dayActivities = this.getActivitiesForDate(date);
                        dayActivities.forEach(activity => {
                            const activityElement = this.createActivityElement(activity);
                            activitiesDiv.appendChild(activityElement);
                        });
                        
                        dayDiv.appendChild(activitiesDiv);
                        return dayDiv;
                    }
                    
                    getActivitiesForDate(date) {
                        return this.activities.filter(activity => {
                            const activityStart = new Date(activity.startDate);
                            const activityEnd = new Date(activity.endDate);
                            activityStart.setHours(0, 0, 0, 0);
                            activityEnd.setHours(0, 0, 0, 0);
                            const checkDate = new Date(date);
                            checkDate.setHours(0, 0, 0, 0);
                            
                            return checkDate >= activityStart && checkDate <= activityEnd;
                        });
                    }
                    
                    createActivityElement(activity) {
                        const activityDiv = document.createElement('div');
                        activityDiv.className = `calendar-activity status-${activity.status.replace(' ', '-')}`;
                        activityDiv.textContent = activity.title;
                        activityDiv.title = `${activity.title} (${activity.project})`;
                        
                        // Add click event to show activity details
                        activityDiv.addEventListener('click', () => {
                            this.showActivityDetails(activity);
                        });
                        
                        return activityDiv;
                    }
                    
                    showActivityDetails(activity) {
                        // Create a simple modal-like display
                        const modal = document.createElement('div');
                        modal.style.cssText = `
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 0, 0, 0.8);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            z-index: 9999;
                        `;
                        
                        const content = document.createElement('div');
                        content.style.cssText = `
                            background: var(--secondary-bg);
                            border: 1px solid var(--border-color);
                            border-radius: 16px;
                            padding: 2rem;
                            max-width: 500px;
                            width: 90%;
                            color: var(--text-white);
                        `;
                        // Ensure long text/URLs wrap properly to avoid overlap
                        content.style.wordBreak = 'break-word';
                        content.style.overflowWrap = 'anywhere';
                        
                        // Prepare TO information (only show when present)
                        let toNumberHtml = '';
                        const hasTO = activity?.requirements && (activity.requirements.to === 1 || activity.requirements.to === '1');
                        let rawTo = activity?.requirements?.to_number ?? '';
                        if (hasTO && rawTo && String(rawTo).trim() !== '') {
                            // Normalize to a single R13- prefix
                            let normalized = String(rawTo).trim().replace(/^R13-?\s*/i, '');
                            const displayTo = `R13-${normalized}`;

                        }

                        // Prepare Published Link (only show when published checked and link exists)
                        let publishedHtml = '';
                        const isPublished = activity?.requirements && (activity.requirements.published === 1 || activity.requirements.published === '1');
                        const publishedLink = activity?.requirements?.published_link || '';
                        if (isPublished && publishedLink && String(publishedLink).trim() !== '') {
                            const safeLink = String(publishedLink).trim();
                            const linkText = 'Open Link';
                            publishedHtml = `\n                            <p><strong>Published Link:</strong> <a href="${safeLink}" target="_blank" title="${safeLink}" class="text-info text-decoration-none">${linkText}</a> <button class="btn btn-sm btn-outline-light ms-2" onclick="copyToClipboard(event, '${safeLink.replace(/'/g, "\\'")}')" title="Copy Published Link"><i class="fas fa-copy"></i></button></p>`;
                        }

                        // Prepare Google Drive Link (only show when present)
                        let gdriveHtml = '';
                        const gdrive = activity?.googleDriveLink || '';
                        if (gdrive && String(gdrive).trim() !== '') {
                            const safeG = String(gdrive).trim();
                            const linkTextG = 'Open Link';
                            gdriveHtml = `\n                            <p><strong>Google Drive Link:</strong> <a href="${safeG}" target="_blank" title="${safeG}" class="text-info text-decoration-none">${linkTextG}</a> <button class="btn btn-sm btn-outline-light ms-2" onclick="copyToClipboard(event, '${safeG.replace(/'/g, "\\'")}')" title="Copy Google Drive Link"><i class="fas fa-copy"></i></button></p>`;
                        }

                        // Build modal content
                        let html = `
                            <h5 style="color: var(--accent-color); margin-bottom: 1rem;">${activity.title} <button class="btn btn-sm btn-outline-light ms-2" onclick="copyToClipboard(event, '${activity.title.replace(/'/g, "\\'")}')" title="Copy Activity Name"><i class="fas fa-copy"></i></button></h5>
                            <p><strong>Project:</strong> ${activity.project}</p>
                            <p><strong>Status:</strong> <span class="status-badge status-${activity.status.replace(' ', '-')}">${activity.status}</span></p>
                            <p><strong>Start Date:</strong> ${activity.startDate.toLocaleDateString()}</p>
                            <p><strong>End Date:</strong> ${activity.endDate.toLocaleDateString()}</p>`;
                        html += toNumberHtml;
                        html += publishedHtml;
                        html += gdriveHtml;
                        let editBtnHtml = '';
                        let deleteBtnHtml = '';
                        if (activity.activityId) {
                            editBtnHtml = ` <button class="btn btn-outline-info mt-3 ms-2 calendar-edit-btn">Edit</button>`;
                            deleteBtnHtml = ` <button class="btn btn-outline-danger mt-3 ms-2 calendar-delete-btn" onclick="deleteActivity(${activity.activityId})">Delete</button>`;
                        }
                        html += `
                            <div class="mt-2">
                                <button class="btn btn-custom mt-3" onclick="this.closest('.calendar-activity-modal').remove()">Close</button>${deleteBtnHtml}${editBtnHtml}
                            </div>
                        `;
                        content.innerHTML = html;
                        
                        modal.className = 'calendar-activity-modal';
                        modal.appendChild(content);
                        document.body.appendChild(modal);

                        // Wire up Edit button to open the existing Edit Activity modal
                        if (activity.activityId) {
                            const calEditBtn = content.querySelector('.calendar-edit-btn');
                            if (calEditBtn) {
                                calEditBtn.addEventListener('click', () => {
                                    // Close overlay
                                    modal.remove();
                                    // Find corresponding edit button in the table and trigger it
                                    const tableEditBtn = document.querySelector(`.edit-activity[data-id="${activity.activityId}"]`);
                                    if (tableEditBtn) {
                                        tableEditBtn.click();
                                    }
                                });
                            }
                        }
                        
                        // Close on background click
                        modal.addEventListener('click', (e) => {
                            if (e.target === modal) {
                                modal.remove();
                            }
                        });
                    }
                }
                
                // Initialize calendar view
                const calendarView = new CalendarView();
            }
        });
        
        // Copy to clipboard function
        function copyToClipboard(e, text) {
            if (!text || text === 'N/A') {
                return;
            }

            const button = (e && (e.currentTarget || (e.target && e.target.closest && e.target.closest('button')))) ? (e.currentTarget || e.target.closest('button')) : null;
            const originalTitle = button ? button.getAttribute('title') : null;

            const setSuccessUi = () => {
                if (!button) return;
                button.setAttribute('title', 'Copied!');
                button.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(function() {
                    if (originalTitle !== null) button.setAttribute('title', originalTitle);
                    button.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            };

            const fallbackCopy = () => {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch (_) {}
                document.body.removeChild(ta);
                setSuccessUi();
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    setSuccessUi();
                }).catch(function() {
                    fallbackCopy();
                });
            } else {
                fallbackCopy();
            }
        }
        
        // Search functionality for table view
        function setupSearch() {
            const searchInput = document.querySelector('input[name="search"]');
            if (!searchInput) return;
            
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();
                
                searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 2 || searchTerm.length === 0) {
                        // Submit form with search term
                        const form = this.closest('form');
                        if (form) {
                            // Create a new form to avoid page refresh issues
                            const newForm = document.createElement('form');
                            newForm.method = 'GET';
                            newForm.action = form.action;
                            
                            // Copy all form fields except empty search
                            Array.from(form.elements).forEach(element => {
                                if (element.name && (element.name !== 'search' || searchTerm)) {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = element.name;
                                    input.value = element.value;
                                    newForm.appendChild(input);
                                }
                            });
                            
                            // Add search term if not empty
                            if (searchTerm) {
                                const searchInput = document.createElement('input');
                                searchInput.type = 'hidden';
                                searchInput.name = 'search';
                                searchInput.value = searchTerm;
                                newForm.appendChild(searchInput);
                            }
                            
                            // Add view parameter to stay in table view
                            const viewInput = document.createElement('input');
                            viewInput.type = 'hidden';
                            viewInput.name = 'view';
                            viewInput.value = 'table';
                            newForm.appendChild(viewInput);
                            
                            document.body.appendChild(newForm);
                            newForm.submit();
                            document.body.removeChild(newForm);
                        }
                    }
                }, 500); // Debounce search
            });
        }
        
        // Initialize search when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupSearch);
        } else {
            setupSearch();
        }
    </script>
</body>
</html> 