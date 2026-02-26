<?php
// Database configuration
require_once 'config/database.php';

header('Content-Type: text/plain');

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=localhost;dbname=dict_monitoring", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting collation fix...\n\n";
    
    // List of tables to update
    $tables = ['activities', 'projects', 'users', 'tev_claims'];
    
    foreach ($tables as $table) {
        echo "Processing table: $table\n";
        
        // Update table collation
        $pdo->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "- Updated table collation to utf8mb4_unicode_ci\n";
        
        // Get all string columns in the table
        $columns = $pdo->query("SHOW FULL COLUMNS FROM `$table` WHERE Type LIKE 'varchar%' OR Type LIKE 'char%' OR Type LIKE 'text%' OR Type LIKE 'enum%'");
        
        foreach ($columns as $col) {
            $column = $col['Field'];
            $type = $col['Type'];
            
            // Skip if not a string type
            if (!preg_match('/(varchar|char|text|enum)/i', $type)) {
                continue;
            }
            
            // Update column collation with proper syntax for all MySQL versions
            $sql = "ALTER TABLE `$table` 
                    MODIFY `$column` $type 
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            
            // Add NULL/NOT NULL
            $sql .= ($col['Null'] === 'NO') ? " NOT NULL" : " NULL";
            
            // Add DEFAULT if exists
            if (isset($col['Default']) && $col['Default'] !== null) {
                $default = $col['Default'];
                if (strtoupper($default) !== 'NULL') {
                    $sql .= " DEFAULT '" . addslashes($default) . "'";
                } else {
                    $sql .= " DEFAULT NULL";
                }
            }
            
            $pdo->exec($sql);
            echo "- Updated column `$column` collation to utf8mb4_unicode_ci\n";
        }
        
        echo "\n";
    }
    
    // Update database default collation
    $pdo->exec("ALTER DATABASE dict_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Updated database default collation to utf8mb4_unicode_ci\n\n";
    
    echo "Collation fix completed successfully!\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}

echo "\nIMPORTANT: After verifying everything works, please delete this script for security reasons.";
?>
