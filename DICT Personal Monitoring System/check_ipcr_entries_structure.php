<?php
require_once 'config/database.php';

try {
    // Check if the table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'ipcr_entries'");
    if ($stmt->rowCount() === 0) {
        die("The 'ipcr_entries' table does not exist in the database.\n");
    }
    
    // Get the table structure
    $stmt = $pdo->query("DESCRIBE ipcr_entries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "ipcr_entries table structure:\n";
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
    
    // Check if the quantity columns exist
    $hasSuccessIndicatorsQty = false;
    $hasActualAccomplishmentsQty = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'success_indicators_quantity') {
            $hasSuccessIndicatorsQty = true;
        }
        if ($column['Field'] === 'actual_accomplishments_quantity') {
            $hasActualAccomplishmentsQty = true;
        }
    }
    
    echo "\n";
    
    if (!$hasSuccessIndicatorsQty || !$hasActualAccomplishmentsQty) {
        echo "\nWARNING: Some required columns are missing. You may need to run the migration.\n";
        
        if (!$hasSuccessIndicatorsQty) {
            echo "- success_indicators_quantity column is missing\n";
        }
        if (!$hasActualAccomplishmentsQty) {
            echo "- actual_accomplishments_quantity column is missing\n";
        }
        
        echo "\nTo fix this, you need to run the following SQL commands:\n";
        echo file_get_contents('database/migrations/add_quantity_to_ipcr_entries.sql');
    } else {
        echo "\nAll required columns exist in the ipcr_entries table.\n";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
