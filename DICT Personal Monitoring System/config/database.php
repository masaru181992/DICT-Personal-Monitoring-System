<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'dict_monitoring';
$username = 'root';
$password = '';

// Custom PDO statement class to handle unknown types
class CustomPDOStatement extends PDOStatement {
    protected function __construct() {
        $this->setFetchMode(PDO::FETCH_ASSOC);
    }
    
    #[\ReturnTypeWillChange]
    public function execute($params = null) {
        try {
            return parent::execute($params);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown type') !== false) {
                // If we get an unknown type error, try to fetch all rows and filter them manually
                $this->executeUnbuffered($params);
                return true;
            }
            throw $e;
        }
    }
    
    protected function executeUnbuffered($params) {
        // This is a fallback method that will be used if we encounter unknown types
        $conn = $this->getConnection();
        $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        
        try {
            parent::execute($params);
            $rows = [];
            while ($row = $this->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $row;
            }
            $this->rowCountValue = count($rows);
            $this->rows = $rows;
            $this->currentRow = 0;
            return true;
        } finally {
            $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }
    }
    
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$fetchModeArgs): array {
        if (isset($this->rows)) {
            return $this->rows;
        }
        if (!empty($fetchModeArgs)) {
            return parent::fetchAll($mode, ...$fetchModeArgs);
        }
        return parent::fetchAll($mode);
    }
    
    #\[\ReturnTypeWillChange\]
    public function rowCount(): int {
        return isset($this->rowCountValue) ? (int)$this->rowCountValue : parent::rowCount();
    }
    
    protected function getConnection() {
        return $this->queryString ? $this : $this->dbh;
    }
}

// Set PDO options with buffered queries and type handling
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,  // Disable emulation for better type handling
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, sql_mode='NO_ENGINE_SUBSTITUTION'",
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::MYSQL_ATTR_FOUND_ROWS   => true
];

// Disable custom statement class temporarily to isolate the issue
// PDO::ATTR_STATEMENT_CLASS    => ['CustomPDOStatement']

// Try to connect with error handling
try {
    // First try with default options
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        $options
    );
    
    // Set session variables to handle unknown types
    $pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION,NO_AUTO_CREATE_USER'");
    
} catch(PDOException $e) {
    // If connection fails, try with emulation mode off
    $options[PDO::ATTR_EMULATE_PREPARES] = false;
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            $options
        );
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

/**
 * Safely execute a database query with error handling and type conversion
 * 
 * @param PDO $pdo Database connection
 * @param string $sql SQL query
 * @param array $params Query parameters
 * @param bool $closeCursor Whether to close any open cursor before executing the query
 * @return PDOStatement|false Returns statement on success, false on failure
 */
function safeQuery($pdo, $sql, $params = [], $closeCursor = true) {
    static $retryCount = 0;
    $maxRetries = 1;
    
    try {
        // Close any open cursor if requested
        if ($closeCursor) {
            try {
                @$pdo->query('SELECT 1'); // This will close any open cursor
            } catch (PDOException $e) {
                // Ignore any errors from this query
            }
        }

        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare query: " . print_r($pdo->errorInfo(), true));
        }
        
        // Execute with parameters
        $result = $stmt->execute($params);
        
        if ($result === false) {
            $error = $stmt->errorInfo();
            throw new Exception("Query execution failed: [" . $error[0] . "] " . ($error[2] ?? 'Unknown error'));
        }
        
        return $stmt;
        
    } catch (PDOException $e) {
        // Log the error
        error_log("PDO Error [" . $e->getCode() . "]: " . $e->getMessage());
        error_log("Failed SQL: " . $sql);
        
        // If we get a 'commands out of sync' error, try again with cursor closed
        if (strpos($e->getMessage(), 'commands out of sync') !== false && $retryCount < $maxRetries) {
            $retryCount++;
            error_log("Retrying query (attempt {$retryCount}) after cursor reset");
            return safeQuery($pdo, $sql, $params, true);
        }
        
        // If we get an unknown type error, try with a simpler query
        if (strpos($e->getMessage(), 'Unknown type') !== false && $retryCount < $maxRetries) {
            $retryCount++;
            error_log("Retrying query (attempt {$retryCount}) with simplified SQL");
            
            // Simplify the query to avoid complex column types
            $simplifiedSql = $sql;
            
            // Remove ORDER BY clauses
            $simplifiedSql = preg_replace('/\s+ORDER\s+BY\s+[^;]+/i', '', $simplifiedSql);
            
            // Remove GROUP BY clauses
            $simplifiedSql = preg_replace('/\s+GROUP\s+BY\s+[^;]+/i', '', $simplifiedSql);
            
            // Try with simplified query
            if ($simplifiedSql !== $sql) {
                return safeQuery($pdo, $simplifiedSql, $params, $closeCursor);
            }
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("General Error in safeQuery: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    } finally {
        $retryCount = 0; // Reset retry counter when done
    }
}
?> 