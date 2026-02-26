<?php
require_once 'config/database.php';

try {
    echo "Starting migration...\n";
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Add quantity columns to ipcr_entries table
    echo "Adding success_indicators_quantity column...\n";
    $pdo->exec("ALTER TABLE `ipcr_entries` ADD COLUMN `success_indicators_quantity` INT DEFAULT 1 AFTER `success_indicators`");
    
    echo "Adding actual_accomplishments_quantity column...\n";
    $pdo->exec("ALTER TABLE `ipcr_entries` ADD COLUMN `actual_accomplishments_quantity` INT DEFAULT 1 AFTER `actual_accomplishments`");
    
    // Update existing entries to have default quantity of 1
    echo "Updating existing entries with default quantity values...\n";
    $pdo->exec("UPDATE `ipcr_entries` SET `success_indicators_quantity` = 1, `actual_accomplishments_quantity` = 1");
    
    // Make the columns NOT NULL after setting default values
    echo "Setting columns to NOT NULL...\n";
    $pdo->exec("ALTER TABLE `ipcr_entries` MODIFY COLUMN `success_indicators_quantity` INT NOT NULL DEFAULT 1");
    $pdo->exec("ALTER TABLE `ipcr_entries` MODIFY COLUMN `actual_accomplishments_quantity` INT NOT NULL DEFAULT 1");
    
    // Commit the transaction
    $pdo->commit();
    
    echo "Migration completed successfully!\n";
    
    // Show the updated table structure
    echo "\nUpdated table structure:\n";
    $stmt = $pdo->query("DESCRIBE ipcr_entries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo str_pad("Field", 30) . str_pad("Type", 20) . "Null\tKey\tDefault\t\tExtra\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($columns as $column) {
        echo str_pad($column['Field'], 30) . 
             str_pad($column['Type'], 20) . 
             $column['Null'] . "\t" . 
             ($column['Key'] ?: '') . "\t" . 
             ($column['Default'] !== null ? $column['Default'] : 'NULL') . "\t" . 
             $column['Extra'] . "\n";
    }
    
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error during migration: " . $e->getMessage() . "\n");
}
?>
