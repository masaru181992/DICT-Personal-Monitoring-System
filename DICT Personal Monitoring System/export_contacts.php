<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Set headers for Excel download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=contacts_export_' . date('Y-m-d') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, [
    'Type',
    'Title',
    'Description',
    'Phone',
    'Email',
    'Officer Name',
    'Officer Position',
    'Officer Phone',
    'Alternate Focal Name',
    'Alternate Focal Position',
    'Alternate Focal Phone',
    'Created At',
    'Updated At'
]);

try {
    // Fetch all contacts
    $stmt = $pdo->query("SELECT * FROM point_of_contacts ORDER BY type, title");
    
    // Output each row of the data
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            ucfirst($row['type']),
            $row['title'],
            $row['description'],
            $row['phone'],
            $row['email'],
            $row['officer_name'],
            $row['officer_position'],
            $row['officer_phone'],
            $row['alt_focal_name'],
            $row['alt_focal_position'],
            $row['alt_focal_phone'],
            $row['created_at'],
            $row['updated_at']
        ]);
    }
} catch (Exception $e) {
    // Log error
    error_log('Export error: ' . $e->getMessage());
    
    // Output error message
    fputcsv($output, ['Error', 'Failed to export contacts. Please try again.']);
}

fclose($output);
?>
