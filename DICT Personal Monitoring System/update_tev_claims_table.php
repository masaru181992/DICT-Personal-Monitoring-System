<?php
// Database configuration
require_once 'config/database.php';

try {
    // Check if payment_date column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM tev_claims LIKE 'payment_date'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        // Add payment_date column
        $pdo->exec("ALTER TABLE tev_claims ADD COLUMN payment_date DATE NULL AFTER status");
        echo "Successfully added 'payment_date' column to tev_claims table.\n";
    } else {
        echo "The 'payment_date' column already exists in the tev_claims table.\n";
    }
    
    echo "Database structure is up to date.\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
