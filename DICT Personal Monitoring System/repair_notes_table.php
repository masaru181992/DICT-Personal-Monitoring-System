<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/config/database.php';

// Function to execute SQL queries with error handling
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare query: " . print_r($pdo->errorInfo(), true));
        }
        
        $result = $stmt->execute($params);
        if ($result === false) {
            $error = $stmt->errorInfo();
            throw new Exception("Query execution failed: [" . $error[0] . "] " . ($error[2] ?? 'Unknown error'));
        }
        
        return $stmt;
    } catch (PDOException $e) {
        throw new Exception("Database error: " . $e->getMessage());
    }
}

try {
    // Start HTML output
    echo "<html><head><title>Notes Table Repair</title>
          <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel="
          ."stylesheet">
          <style>.success { color: green; } .error { color: red; } .warning { color: orange; }</style>
          </head><body class='container mt-5'>
          <h1>Notes Table Repair Utility</h1>";

    // Check if table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'notes'")->rowCount() > 0;
    
    if (!$tableExists) {
        throw new Exception("The 'notes' table does not exist in the database.");
    }

    // Disable foreign key checks
    echo "<h3>1. Disabling foreign key checks...</h3>";
    executeQuery($pdo, "SET FOREIGN_KEY_CHECKS = 0");
    echo "<p class='success'>✓ Foreign key checks disabled</p>";

    // Create backup table
    echo "<h3>2. Creating backup of notes table...</h3>";
    executeQuery($pdo, "DROP TABLE IF EXISTS notes_backup");
    executeQuery($pdo, "CREATE TABLE notes_backup LIKE notes");
    executeQuery($pdo, "INSERT INTO notes_backup SELECT * FROM notes");
    $backupCount = $pdo->query("SELECT COUNT(*) FROM notes_backup")->fetchColumn();
    echo "<p class='success'>✓ Backup created with $backupCount records</p>";

    // Repair table
    echo "<h3>3. Repairing notes table...</h3>";
    $repairResult = $pdo->query("REPAIR TABLE notes");
    $repairStatus = $repairResult->fetch(PDO::FETCH_ASSOC);
    echo "<p class='success'>✓ Repair completed: " . htmlspecialchars($repairStatus['Msg_text']) . "</p>";

    // Optimize table
    echo "<h3>4. Optimizing notes table...</h3>";
    $optimizeResult = $pdo->query("OPTIMIZE TABLE notes");
    $optimizeStatus = $optimizeResult->fetch(PDO::FETCH_ASSOC);
    echo "<p class='success'>✓ Optimization completed: " . htmlspecialstr($optimizeStatus['Msg_text']) . "</p>";

    // Update character set and collation
    echo "<h3>5. Updating character set and collation...</h3>";
    executeQuery($pdo, "ALTER TABLE notes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='success'>✓ Character set updated to utf8mb4</p>";

    // Rebuild table
    echo "<h3>6. Rebuilding table...</h3>";
    executeQuery($pdo, "ALTER TABLE notes ENGINE=InnoDB");
    echo "<p class='success'>✓ Table rebuilt with InnoDB engine</p>";

    // Re-enable foreign key checks
    echo "<h3>7. Finalizing...</h3>";
    executeQuery($pdo, "SET FOREIGN_KEY_CHECKS = 1");
    
    // Verify table status
    $checkResult = $pdo->query("CHECK TABLE notes");
    $checkStatus = $checkResult->fetch(PDO::FETCH_ASSOC);
    
    if ($checkStatus['Msg_text'] === 'OK') {
        echo "<div class='alert alert-success mt-4'><h4>✓ Notes table repair completed successfully!</h4>";
    } else {
        echo "<div class='alert alert-warning mt-4'><h4>⚠ Notes table repair completed with warnings:</h4>";
        echo "<p>" . htmlspecialchars($checkStatus['Msg_text']) . "</p>";
    }
    
    // Show record count
    $recordCount = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
    echo "<p>Total records in notes table: $recordCount</p>";
    
    // Show backup status
    $backupCount = $pdo->query("SELECT COUNT(*) FROM notes_backup")->fetchColumn();
    echo "<p>Backup created in table: notes_backup ($backupCount records)</p>";
    
    echo "<p class='mt-3'><a href='notes.php' class='btn btn-primary'>Go to Notes</a></p>";
    
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
