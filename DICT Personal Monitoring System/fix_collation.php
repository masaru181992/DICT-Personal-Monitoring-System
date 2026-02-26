<?php
// Database configuration
require_once 'config/database.php';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=localhost;dbname=DICT_Personal_Monitoring_System", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL to fix collation for relevant tables
    $sql = [
        "ALTER TABLE `tev_claims` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        "ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        "ALTER TABLE `activities` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        "ALTER TABLE `projects` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    // Execute each SQL statement
    foreach ($sql as $query) {
        try {
            $pdo->exec($query);
            echo "Successfully executed: " . substr($query, 0, 50) . "...<br>";
        } catch (PDOException $e) {
            echo "Error executing '$query': " . $e->getMessage() . "<br>";
        }
    }
    
    echo "Collation fix completed successfully!";
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
