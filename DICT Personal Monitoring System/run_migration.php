<?php
// Database configuration
$host = 'localhost';
$dbname = 'dict_monitoring';
$username = 'root';
$password = ''; // Leave empty for default WAMP configuration

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL to add the google_drive_link column
    $sql = "ALTER TABLE `activities` 
            ADD COLUMN `google_drive_link` VARCHAR(512) NULL 
            COMMENT 'Google Drive link for activity documents' 
            AFTER `description`";
    
    // Execute the SQL
    $pdo->exec($sql);
    
    echo "Migration completed successfully. The 'google_drive_link' column has been added to the 'activities' table.";
    
} catch(PDOException $e) {
    // Check if the error is because the column already exists
    if (strpos($e->getMessage(), 'duplicate column name') !== false) {
        echo "The 'google_drive_link' column already exists in the 'activities' table. No changes were made.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
