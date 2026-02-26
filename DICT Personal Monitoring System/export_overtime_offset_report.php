<?php
session_start();
require_once 'config/database.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo 'Unauthorized';
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch overtime requests with activity details for the current user
    $stmt = $pdo->prepare(
        "SELECT 
            o.id AS overtime_id,
            o.activity_id,
            o.total_days,
            o.used_days,
            o.status AS overtime_status,
            o.created_at AS overtime_created_at,
            a.title AS activity_title,
            a.start_date AS activity_start_date,
            a.end_date AS activity_end_date
         FROM overtime_requests o
         LEFT JOIN activities a ON a.id = o.activity_id
         WHERE o.user_id = ?
         ORDER BY o.created_at DESC, o.id DESC"
    );
    $stmt->execute([$user_id]);
    $overtimes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pre-fetch offsets grouped by activity for the current user
    $offsetStmt = $pdo->prepare(
        "SELECT 
            activity_id,
            COUNT(*) AS offset_count,
            GROUP_CONCAT(DATE_FORMAT(offset_date, '%Y-%m-%d') ORDER BY offset_date SEPARATOR ', ') AS offset_dates,
            GROUP_CONCAT(CONCAT(DATE_FORMAT(offset_date, '%Y-%m-%d'), ' - ', COALESCE(reason,''), ' (', status, ')') ORDER BY offset_date SEPARATOR ' | ') AS offset_details
         FROM offset_requests
         WHERE user_id = ?
         GROUP BY activity_id"
    );
    $offsetStmt->execute([$user_id]);
    $offsetsByActivity = [];
    foreach ($offsetStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $offsetsByActivity[$row['activity_id']] = $row;
    }

    // Prepare CSV output
    $filename = 'overtime_offset_report_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output UTF-8 BOM for Excel compatibility
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');

    // CSV Header
    fputcsv($out, [
        'Overtime ID',
        'Activity Title',
        'Activity Start',
        'Activity End',
        'Overtime Total Days',
        'Used Days',
        'Remaining Days',
        'Overtime Status',
        'Overtime Created At',
        'Offset Count',
        'Offset Dates',
        'Offset Details'
    ]);

    // Rows
    foreach ($overtimes as $ot) {
        $activityId = $ot['activity_id'];
        $offsetInfo = $offsetsByActivity[$activityId] ?? [
            'offset_count' => 0,
            'offset_dates' => '',
            'offset_details' => ''
        ];

        $total = (int)($ot['total_days'] ?? 0);
        $used = (int)($ot['used_days'] ?? 0);
        $remaining = max(0, $total - $used);

        fputcsv($out, [
            $ot['overtime_id'],
            $ot['activity_title'],
            $ot['activity_start_date'],
            $ot['activity_end_date'],
            $total,
            $used,
            $remaining,
            $ot['overtime_status'],
            $ot['overtime_created_at'],
            $offsetInfo['offset_count'],
            $offsetInfo['offset_dates'],
            $offsetInfo['offset_details']
        ]);
    }

    fclose($out);
    exit();
} catch (Exception $e) {
    http_response_code(500);
    echo 'Failed to generate report: ' . $e->getMessage();
    exit();
}
