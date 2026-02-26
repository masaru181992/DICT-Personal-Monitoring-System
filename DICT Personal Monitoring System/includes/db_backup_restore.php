<?php
// Disable output buffering
while (ob_get_level()) ob_end_clean();

// Set error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

// Set JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

// Function to send JSON response and exit
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit();
}

try {
    // Start session
    session_start();
    
    // Check if user is logged in and is an admin (checking by username)
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        sendJsonResponse([
            'status' => 'error',
            'message' => 'Not authenticated. Please log in.'
        ], 401);
    }
    
    // Check if the user is an admin (assuming 'admin' is the admin username)
    if ($_SESSION['username'] !== 'admin') {
        sendJsonResponse([
            'status' => 'error',
            'message' => 'Access denied. Admin privileges required.'
        ], 403);
    }
    
    // Database configuration
    $host = 'localhost';
    $dbname = 'dict_monitoring';
    $username = 'root';
    $password = '';
    
    try {
        // Create PDO connection
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );
        
        // Create mysqli connection for compatibility with existing code
        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            throw new Exception("MySQLi Connection failed: " . $conn->connect_error);
        }
        
    } catch(PDOException $e) {
        throw new Exception("Database connection failed: " . $e->getMessage());
    }

    // Handle backup request
    if (isset($_GET['action']) && $_GET['action'] === 'backup') {
        // Verify database connection
        if (!$conn) {
            throw new Exception('Failed to connect to database');
        }
        
        // Get all tables including views
        $tables = [];
        $views = [];
        
        // Get regular tables
        $result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        
        // Get views
        $result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
        while ($row = $result->fetch_row()) {
            $views[] = $row[0];
        }
        
        $return = '';
        
        // Add backup header with metadata
        $backupTime = date('Y-m-d H:i:s');
        $backupUser = $_SESSION['username'] ?? 'unknown';
        $return .= "-- DICT Personal Monitoring System Database Backup\n";
        $return .= "-- Backup Date: $backupTime\n";
        $return .= "-- Backup User: $backupUser\n";
        $return .= "-- Database: $dbname\n";
        $return .= "-- Host: $host\n";
        $return .= "-- MySQL Version: " . $conn->server_info . "\n";
        $return .= "-- Tables: " . count($tables) . "\n";
        $return .= "-- Views: " . count($views) . "\n";
        $return .= "-- \n";
        
        // Add SQL commands to disable foreign key checks and set session variables
        $return .= 'SET FOREIGN_KEY_CHECKS=0;' . "\n";
        $return .= 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";' . "\n";
        $return .= 'SET AUTOCOMMIT = 0;' . "\n";
        $return .= 'START TRANSACTION;' . "\n";
        $return .= 'SET time_zone = "+00:00";' . "\n";
        $return .= 'SET NAMES utf8mb4;' . "\n";
        $return .= 'SET CHARACTER SET utf8mb4;' . "\n\n";
        
        // Backup all tables
        foreach ($tables as $table) {
            // Drop table if exists
            $return .= 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n";
            
            // Get create table statement with detailed information
            $createTable = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
            if ($createTable && isset($createTable[1])) {
                $return .= "\n" . $createTable[1] . ";\n\n";
                
                // Get table data with better error handling
                try {
                    $result = $conn->query("SELECT * FROM `$table`");
                    if ($result) {
                        $numFields = $result->field_count;
                        $rowCount = 0;
                        
                        while ($row = $result->fetch_row()) {
                            $rowCount++;
                            $return .= "INSERT INTO `$table` VALUES(";
                            for ($i = 0; $i < $numFields; $i++) {
                                if ($row[$i] === null) {
                                    $return .= 'NULL';
                                } else {
                                    // Better escaping for special characters
                                    $escaped = $conn->real_escape_string($row[$i]);
                                    $escaped = str_replace(["\n", "\r", "\t"], ["\\n", "\\r", "\\t"], $escaped);
                                    $return .= "'" . $escaped . "'";
                                }
                                if ($i < ($numFields - 1)) {
                                    $return .= ',';
                                }
                            }
                            $return .= ");\n";
                        }
                        
                        if ($rowCount > 0) {
                            $return .= "\n";
                        }
                    }
                } catch (Exception $e) {
                    // Log error but continue with other tables
                    $return .= "-- Error processing table $table: " . $e->getMessage() . "\n\n";
                }
            }
        }
        
        // Backup all views
        foreach ($views as $view) {
            // Drop view if exists
            $return .= 'DROP VIEW IF EXISTS `' . $view . '`;' . "\n";
            
            // Get create view statement
            $createView = $conn->query("SHOW CREATE VIEW `$view`")->fetch_row();
            if ($createView && isset($createView[1])) {
                $return .= "\n" . $createView[1] . ";\n\n";
            }
        }
        
        // Backup stored procedures, functions, and triggers
        try {
            // Backup stored procedures
            $result = $conn->query("SHOW PROCEDURE STATUS WHERE Db = '$dbname'");
            while ($row = $result->fetch_assoc()) {
                $procName = $row['Name'];
                $procCreate = $conn->query("SHOW CREATE PROCEDURE `$procName`")->fetch_row();
                if ($procCreate && isset($procCreate[2])) {
                    $return .= "DROP PROCEDURE IF EXISTS `$procName`;\n";
                    $return .= "DELIMITER $$\n" . $procCreate[2] . "$$\nDELIMITER ;\n\n";
                }
            }
            
            // Backup functions
            $result = $conn->query("SHOW FUNCTION STATUS WHERE Db = '$dbname'");
            while ($row = $result->fetch_assoc()) {
                $funcName = $row['Name'];
                $funcCreate = $conn->query("SHOW CREATE FUNCTION `$funcName`")->fetch_row();
                if ($funcCreate && isset($funcCreate[2])) {
                    $return .= "DROP FUNCTION IF EXISTS `$funcName`;\n";
                    $return .= "DELIMITER $$\n" . $funcCreate[2] . "$$\nDELIMITER ;\n\n";
                }
            }
            
            // Backup triggers
            $result = $conn->query("SHOW TRIGGERS");
            while ($row = $result->fetch_assoc()) {
                $triggerName = $row['Trigger'];
                $triggerCreate = $conn->query("SHOW CREATE TRIGGER `$triggerName`")->fetch_row();
                if ($triggerCreate && isset($triggerCreate[2])) {
                    $return .= "DROP TRIGGER IF EXISTS `$triggerName`;\n";
                    $return .= "DELIMITER $$\n" . $triggerCreate[2] . "$$\nDELIMITER ;\n\n";
                }
            }
        } catch (Exception $e) {
            // Log error but continue
            $return .= "-- Error backing up routines/triggers: " . $e->getMessage() . "\n\n";
        }
        
        // Enable foreign key checks and commit
        $return .= 'SET FOREIGN_KEY_CHECKS=1;' . "\n";
        $return .= 'COMMIT;' . "\n";
        $return .= 'SET AUTOCOMMIT = 1;' . "\n";
        
        // Add backup footer
        $return .= "-- Backup completed successfully at: " . date('Y-m-d H:i:s') . "\n";
        
        // Save to file
        $backupDir = __DIR__ . '/../backups/';
        if (!file_exists($backupDir)) {
            if (!mkdir($backupDir, 0755, true)) {
                throw new Exception('Failed to create backup directory');
            }
        }
        
        if (!is_writable($backupDir)) {
            throw new Exception('Backup directory is not writable');
        }
        
        $backupFile = $backupDir . 'backup_' . date('Y-m-d_His') . '.sql';
        
        // Write to file
        if (file_put_contents($backupFile, $return) === false) {
            throw new Exception('Failed to write backup file');
        }
        
        // Get file size for logging
        $fileSize = filesize($backupFile);
        $fileSizeFormatted = number_format($fileSize / 1024, 2) . ' KB';
        
        // Log backup information
        $logMessage = sprintf(
            "Backup created: %s, Size: %s, User: %s, Tables: %d, Views: %d",
            basename($backupFile),
            $fileSizeFormatted,
            $backupUser,
            count($tables),
            count($views)
        );
        error_log($logMessage);
        
        // Get relative path for download
        $relativePath = 'backups/' . basename($backupFile);
        
        sendJsonResponse([
            'status' => 'success',
            'message' => 'Database backup created successfully!',
            'file' => $relativePath,
            'size' => $fileSizeFormatted,
            'tables' => count($tables),
            'views' => count($views),
            'timestamp' => $backupTime
        ]);
    } 
    // Handle restore request
    elseif (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
        // Verify file type
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $_FILES['backup_file']['tmp_name']);
        finfo_close($fileInfo);
        
        if (!in_array($mimeType, ['text/plain', 'application/sql', 'application/octet-stream'])) {
            throw new Exception('Invalid file type. Please upload a valid SQL file.');
        }
        
        $file = $_FILES['backup_file']['tmp_name'];
        $fileContent = file_get_contents($file);
        
        if (empty($fileContent)) {
            throw new Exception('Uploaded file is empty');
        }
        
        // Get file information
        $fileName = $_FILES['backup_file']['name'];
        $fileSize = $_FILES['backup_file']['size'];
        $fileSizeFormatted = number_format($fileSize / 1024, 2) . ' KB';
        $restoreUser = $_SESSION['username'] ?? 'unknown';
        $restoreTime = date('Y-m-d H:i:s');
        
        // Log restore start
        $logMessage = sprintf(
            "Restore started: %s, Size: %s, User: %s",
            $fileName,
            $fileSizeFormatted,
            $restoreUser
        );
        error_log($logMessage);
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Set session variables for restore
            $conn->query('SET FOREIGN_KEY_CHECKS=0');
            $conn->query('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"');
            $conn->query('SET AUTOCOMMIT = 0');
            $conn->query('SET time_zone = "+00:00"');
            $conn->query('SET NAMES utf8mb4');
            $conn->query('SET CHARACTER SET utf8mb4');
            
            // Parse SQL file with better delimiter handling
            $queries = [];
            $currentQuery = '';
            $delimiter = ';';
            $lines = explode("\n", $fileContent);
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Skip empty lines and comments
                if (empty($line) || strpos($line, '--') === 0) {
                    continue;
                }
                
                // Handle delimiter changes
                if (preg_match('/^DELIMITER\s+(\S+)$/i', $line, $matches)) {
                    $delimiter = $matches[1];
                    continue;
                }
                
                // Add line to current query
                $currentQuery .= $line . "\n";
                
                // Check if line ends with delimiter
                if (substr($line, -strlen($delimiter)) === $delimiter) {
                    // Remove delimiter from query
                    $query = substr($currentQuery, 0, -strlen($delimiter) - 1);
                    
                    if (!empty(trim($query))) {
                        $queries[] = trim($query);
                    }
                    
                    $currentQuery = '';
                    // Reset delimiter to default
                    $delimiter = ';';
                }
            }
            
            // Add any remaining query
            if (!empty(trim($currentQuery))) {
                $queries[] = trim($currentQuery);
            }
            
            $executedQueries = 0;
            $failedQueries = [];
            
            // Execute queries with better error handling
            foreach ($queries as $index => $query) {
                if (!empty(trim($query))) {
                    try {
                        // Skip SET statements that were already executed
                        if (preg_match('/^SET\s+/i', $query)) {
                            continue;
                        }
                        
                        if (!$conn->query($query)) {
                            $error = $conn->error;
                            $failedQueries[] = [
                                'query' => substr($query, 0, 200) . (strlen($query) > 200 ? '...' : ''),
                                'error' => $error
                            ];
                            error_log("Restore query failed: " . $error . " - Query: " . substr($query, 0, 500));
                        } else {
                            $executedQueries++;
                        }
                    } catch (Exception $e) {
                        $failedQueries[] = [
                            'query' => substr($query, 0, 200) . (strlen($query) > 200 ? '...' : ''),
                            'error' => $e->getMessage()
                        ];
                        error_log("Restore query exception: " . $e->getMessage() . " - Query: " . substr($query, 0, 500));
                    }
                }
            }
            
            // Re-enable foreign key checks
            $conn->query('SET FOREIGN_KEY_CHECKS=1');
            $conn->query('SET AUTOCOMMIT = 1');
            
            // Commit transaction
            $conn->commit();
            
            // Log restore completion
            $logMessage = sprintf(
                "Restore completed: %s, Executed: %d, Failed: %d, User: %s",
                $fileName,
                $executedQueries,
                count($failedQueries),
                $restoreUser
            );
            error_log($logMessage);
            
            // Prepare response
            $response = [
                'status' => 'success',
                'message' => 'Database restored successfully!',
                'file' => $fileName,
                'size' => $fileSizeFormatted,
                'executed_queries' => $executedQueries,
                'failed_queries' => count($failedQueries),
                'timestamp' => $restoreTime
            ];
            
            // Add failed queries details if any (only in development)
            if (!empty($failedQueries) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['SERVER_ADDR'] === '127.0.0.1')) {
                $response['failed_queries_details'] = $failedQueries;
            }
            
            sendJsonResponse($response);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $conn->query('SET FOREIGN_KEY_CHECKS=1');
            $conn->query('SET AUTOCOMMIT = 1');
            
            // Log restore failure
            $logMessage = sprintf(
                "Restore failed: %s, User: %s, Error: %s",
                $fileName,
                $restoreUser,
                $e->getMessage()
            );
            error_log($logMessage);
            
            throw $e;
        }
    }
    // No valid action specified
    else {
        $error = 'No file uploaded or upload error occurred';
        if (isset($_FILES['backup_file']['error'])) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
            ];
            $error = $uploadErrors[$_FILES['backup_file']['error']] ?? 'Unknown upload error';
        }
        
        sendJsonResponse([
            'status' => 'error',
            'message' => $error
        ], 400);
    }
    
} catch (Exception $e) {
    error_log('Database Operation Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    // Don't expose system errors in production
    $errorMessage = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['SERVER_ADDR'] === '127.0.0.1')
        ? 'Error: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ' on line ' . $e->getLine()
        : 'An error occurred while processing your request. Please try again later.';
    
    sendJsonResponse([
        'status' => 'error',
        'message' => $errorMessage
    ], 500);
}
?>
