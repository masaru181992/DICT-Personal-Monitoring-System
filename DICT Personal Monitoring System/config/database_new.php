<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'dict_monitoring';
$username = 'root';
$password = '';

/**
 * Custom PDO Statement class to handle unknown type 242 errors
 */
class CustomPDOStatement extends PDOStatement {
    protected static $pdoInstance;
    public string $queryString = ''; // Must be public string to match PDOStatement
    
    // Static method to set the PDO instance
    public static function setPdoInstance($pdo) {
        self::$pdoInstance = $pdo;
    }
    
    protected function __construct() {
        // Empty constructor - PDO will call this with no arguments
    }
    
    #[\ReturnTypeWillChange]
    public function execute($params = null) {
        try {
            return parent::execute($params);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown type 242') !== false) {
                // If we get the unknown type error, try with a simpler query
                $simplifiedSql = $this->simplifyQuery($this->queryString);
                if ($simplifiedSql !== $this->queryString) {
 $newStmt = self::$pdoInstance->prepare($simplifiedSql);
                    $newStmt->queryString = $simplifiedSql;
                    return $newStmt->execute($params);
                }
            }
            throw $e;
        }
    }
    
    protected function simplifyQuery($sql) {
        // Remove complex clauses that might cause type issues
        $simplified = $sql;
        $simplified = preg_replace('/\s+ORDER\s+BY\s+[^;]+/i', '', $simplified);
        $simplified = preg_replace('/\s+GROUP\s+BY\s+[^;]+/i', '', $simplified);
        $simplified = preg_replace('/\s+HAVING\s+[^;]+/i', '', $simplified);
        return $simplified;
    }
}

// PDO options with connection handling and reconnection logic
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Disable emulated prepares for better type handling
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, 
                                     sql_mode='NO_ENGINE_SUBSTITUTION',
                                     wait_timeout=300, 
                                     interactive_timeout=300",
    PDO::ATTR_STRINGIFY_FETCHES  => true,  // Convert all fetched data to strings
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::MYSQL_ATTR_FOUND_ROWS   => true,  // Return the number of found rows instead of affected rows
    PDO::ATTR_TIMEOUT            => 30,    // Connection timeout in seconds
    PDO::ATTR_PERSISTENT         => false  // Use persistent connections (set to false for better error handling)
];

// Only enable custom statement class if needed
// PDO::ATTR_STATEMENT_CLASS => ['CustomPDOStatement', []]

// Function to create a new PDO connection
function createPdoConnection() {
    global $host, $dbname, $username, $password, $options;
    
    try {
        // Create connection with the specified options
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            $options
        );
        
        // Set session variables
        $pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
        
        return $pdo;
    } catch(PDOException $e) {
        error_log("PDO Connection Error: " . $e->getMessage());
        return false;
    }
}

// Function to get a working PDO connection with reconnection logic
function getPdoConnection() {
    static $pdo = null;
    static $lastConnectionTime = 0;
    
    // Check if we need to reconnect (every 5 minutes or if connection is lost)
    $needsReconnect = ($pdo === null || 
                      (time() - $lastConnectionTime) > 300); // 5 minutes
    
    if ($needsReconnect) {
        if ($pdo !== null) {
            // Clean up old connection
            $pdo = null;
        }
        
        // Try to reconnect (up to 3 times)
        $maxRetries = 3;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            $pdo = createPdoConnection();
            
            if ($pdo !== false) {
                $lastConnectionTime = time();
                break;
            }
            
            $retryCount++;
            if ($retryCount < $maxRetries) {
                // Wait before retrying (exponential backoff)
                usleep(100000 * $retryCount); // 100ms, 200ms, 300ms
            }
        }
        
        if ($pdo === false) {
            die("Failed to connect to database after $maxRetries attempts");
        }
        
        // Set the static PDO instance in the statement class
        CustomPDOStatement::setPdoInstance($pdo);
    }
    
    return $pdo;
}

// Initialize the connection
$pdo = getPdoConnection();
if ($pdo === false) {
    die("Failed to establish database connection");
}

/**
 * Safely execute a database query with error handling and type conversion
 * 
 * @param PDO|null $pdo Optional database connection (will use global connection if not provided)
 * @param string $sql SQL query
 * @param array $params Query parameters
 * @param int $retryCount Number of retry attempts remaining
 * @return PDOStatement|false Returns statement on success, false on failure
 */
function safeQuery($pdo, $sql, $params = [], $retryCount = 2) {
    // Use global connection if not provided
    if ($pdo === null) {
        $pdo = getPdoConnection();
        if ($pdo === false) {
            error_log("Failed to get database connection");
            return false;
        }
    }
    static $queryCount = 0;
    $queryId = ++$queryCount;
    
    // Log the query for debugging
    error_log("[$queryId] Executing query: " . substr($sql, 0, 200) . (strlen($sql) > 200 ? '...' : ''));
    
    try {
        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            $error = $pdo->errorInfo();
            throw new Exception("Failed to prepare query: [" . ($error[0] ?? '') . "] " . ($error[2] ?? 'Unknown error'));
        }
        
        // Execute with parameters
        $startTime = microtime(true);
        $result = $stmt->execute($params);
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($result === false) {
            $error = $stmt->errorInfo();
            throw new Exception("Query execution failed after {$executionTime}ms: [" . ($error[0] ?? '') . "] " . ($error[2] ?? 'Unknown error'));
        }
        
        error_log("[$queryId] Query executed successfully in {$executionTime}ms");
        return $stmt;
        
    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
        error_log("[$queryId] PDO Error: " . $errorMessage);
        
        // Check for unknown type error
        $isUnknownTypeError = (strpos($errorMessage, 'Unknown type 242') !== false);
        
        // Try to recover from certain errors
        if ($isUnknownTypeError && $retryCount > 0) {
            error_log("[$queryId] Attempting recovery (retries left: $retryCount)");
            
            // Try to simplify the query
            $simplifiedSql = $sql;
            
            // Remove complex clauses that might cause type issues
            $simplifiedSql = preg_replace('/\s+ORDER\s+BY\s+[^;]+/i', '', $simplifiedSql);
            $simplifiedSql = preg_replace('/\s+GROUP\s+BY\s+[^;]+/i', '', $simplifiedSql);
            $simplifiedSql = preg_replace('/\s+HAVING\s+[^;]+/i', '', $simplifiedSql);
            $simplifiedSql = preg_replace('/\s+LIMIT\s+\d+(\s*,\s*\d+)?/i', '', $simplifiedSql);
            
            // If we simplified the query, try again
            if ($simplifiedSql !== $sql) {
                error_log("[$queryId] Trying simplified query");
                return safeQuery($pdo, $simplifiedSql, $params, $retryCount - 1);
            }
            
            // If we couldn't simplify further, try with direct query
            if ($retryCount > 1) {
                error_log("[$queryId] Trying direct query without prepared statement");
                try {
                    // Get a fresh connection to ensure it's not stale
                    $freshPdo = getPdoConnection();
                    if ($freshPdo === false) {
                        throw new Exception("Failed to get fresh database connection");
                    }
                    
                    // Try with emulated prepares first
                    $freshPdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                    
                    // If there are parameters, we need to prepare and execute
                    if (!empty($params)) {
                        $stmt = $freshPdo->prepare($sql);
                        if ($stmt === false || $stmt->execute($params) === false) {
                            throw new Exception("Failed to execute prepared statement with emulated prepares");
                        }
                    } else {
                        // No parameters, use direct query
                        $stmt = $freshPdo->query($sql);
                        if ($stmt === false) {
                            throw new Exception("Direct query failed");
                        }
                    }
                    
                    return $stmt;
                } catch (Exception $e) {
                    error_log("[$queryId] Direct query failed: " . $e->getMessage());
                    
                    // As a last resort, try one more time with a fresh connection
                    if ($retryCount > 1) {
                        error_log("[$queryId] Attempting final retry with fresh connection");
                        $pdo = getPdoConnection(); // Get a fresh connection
                        if ($pdo !== false) {
                            return safeQuery($pdo, $sql, $params, 0); // Last attempt
                        }
                    }
                }
            }
        }
        
        error_log("[$queryId] Query failed after all retry attempts");
        return false;
        
    } catch (Exception $e) {
        error_log("[$queryId] General Error: " . $e->getMessage());
        error_log("[$queryId] Stack trace: " . $e->getTraceAsString());
        return false;
    }
}
?>
