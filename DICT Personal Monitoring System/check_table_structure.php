<?php
// Load database configuration
require_once 'config/database.php';

try {
    // Get table structure
    $stmt = $pdo->query("SHOW CREATE TABLE tev_claims");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Current TEV Claims Table Structure:\n";
    echo "================================\n";
    echo $result['Create Table'] . "\n\n";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'tev_claims'");
    if ($stmt->rowCount() === 0) {
        echo "WARNING: The tev_claims table does not exist.\n";
        echo "Please run the initial migration first.\n";
    } else {
        echo "Table exists. Checking for existing data...\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tev_claims");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Number of existing records: " . $result['count'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
