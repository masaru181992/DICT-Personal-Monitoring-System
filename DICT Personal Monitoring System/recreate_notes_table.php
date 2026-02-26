<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/config/database.php';

try {
    // Start HTML output
    echo "<html><head><title>Recreate Notes Table</title>
          <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel="
          ."stylesheet">
          <style>.success { color: green; } .error { color: red; } .warning { color: orange; }</style>
          </head><body class='container mt-5'>
          <h1>Notes Table Recreation Utility</h1>";

    // Disable foreign key checks
    echo "<h3>1. Disabling foreign key checks...</h3>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "<p class='success'>✓ Foreign key checks disabled</p>";

    // Check if table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'notes'")->rowCount() > 0;
    
    if (!$tableExists) {
        echo "<p class='warning'>ℹ Notes table does not exist. Will create a new one.</p>";
    } else {
        // Create backup of existing data
        echo "<h3>2. Backing up existing notes data...</h3>";
        $pdo->exec("DROP TABLE IF EXISTS notes_backup");
        $pdo->exec("CREATE TABLE notes_backup LIKE notes");
        $pdo->exec("INSERT INTO notes_backup SELECT * FROM notes");
        $backupCount = $pdo->query("SELECT COUNT(*) FROM notes_backup")->fetchColumn();
        echo "<p class='success'>✓ Backup created with $backupCount records in 'notes_backup' table</p>";
        
        // Drop existing table
        echo "<h3>3. Dropping existing notes table...</h3>";
        $pdo->exec("DROP TABLE IF EXISTS notes");
        echo "<p class='success'>✓ Old notes table dropped</p>";
    }

    // Create new table
    echo "<h3>4. Creating new notes table...</h3>";
    $createTableSQL = "
    CREATE TABLE `notes` (
      `id` int NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `content` text NOT NULL,
      `priority` enum('high','medium','low') NOT NULL DEFAULT 'medium',
      `status` enum('pending','in_progress','completed','archived') NOT NULL DEFAULT 'pending',
      `project_id` int DEFAULT NULL,
      `user_id` int NOT NULL,
      `reminder_date` date DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `fk_notes_project` (`project_id`),
      KEY `fk_notes_user` (`user_id`),
      CONSTRAINT `fk_notes_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
      CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($createTableSQL);
    echo "<p class='success'>✓ New notes table created successfully</p>";
    
    // Restore data if backup exists
    if (isset($backupCount) && $backupCount > 0) {
        echo "<h3>5. Restoring data from backup...</h3>";
        try {
            // Get column names from the new table
            $columns = [];
            $stmt = $pdo->query("SHOW COLUMNS FROM notes");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[] = $row['Field'];
            }
            $columnsStr = '`' . implode('`, `', $columns) . '`';
            
            // Insert data from backup
            $pdo->exec("INSERT INTO notes ($columnsStr) SELECT $columnsStr FROM notes_backup");
            $restoredCount = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
            
            if ($restoredCount == $backupCount) {
                echo "<p class='success'>✓ Successfully restored $restoredCount records</p>";
            } else {
                echo "<p class='warning'>⚠ Restored $restoredCount of $backupCount records. Some data may not have been restored.</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>⚠ Error restoring data: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p class='warning'>Note: The table structure has been recreated, but data restoration failed. 
                You can manually restore data from the 'notes_backup' table if needed.</p>";
        }
    }
    
    // Re-enable foreign key checks
    echo "<h3>6. Finalizing...</h3>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Verify table status
    $checkResult = $pdo->query("CHECK TABLE notes");
    if ($checkResult) {
        $checkStatus = $checkResult->fetch(PDO::FETCH_ASSOC);
        if ($checkStatus['Msg_text'] === 'OK') {
            echo "<div class='alert alert-success mt-4'><h4>✓ Notes table recreation completed successfully!</h4>";
        } else {
            echo "<div class='alert alert-warning mt-4'><h4>⚠ Notes table recreated with warnings:</h4>";
            echo "<p>" . htmlspecialchars($checkStatus['Msg_text']) . "</p>";
        }
    } else {
        echo "<div class='alert alert-warning mt-4'><h4>⚠ Could not verify table status</h4>";
    }
    
    // Show record count
    $recordCount = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
    echo "<p>Total records in notes table: $recordCount</p>";
    
    if (isset($backupCount)) {
        echo "<p>Backup table 'notes_backup' contains: $backupCount records (you may drop this table when no longer needed)</p>";
    }
    
    echo "<p class='mt-3'><a href='notes.php' class='btn btn-primary'>Go to Notes</a> ";
    echo "<a href='dashboard.php' class='btn btn-secondary'>Go to Dashboard</a></p>";
    
} catch (Exception $e) {
    // Re-enable foreign key checks in case of error
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    } catch (Exception $e) {
        // Ignore errors in the error handler
    }
    
    echo "<div class='alert alert-danger mt-4'><h4>❌ Error:</h4>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p class='mt-3'><a href='javascript:history.back()' class='btn btn-secondary'>Go Back</a></p>";
}

echo "</div></body></html>";
