<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'dict_monitoring',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Function to test database connection
function testDatabaseConnection($config) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        
        // Test the connection
        $pdo->query('SELECT 1');
        
        return [
            'success' => true,
            'message' => 'Successfully connected to the database.',
            'pdo' => $pdo
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database connection failed: ' . $e->getMessage(),
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ];
    }
}

// Function to check if table exists and show its structure
function checkTableStructure($pdo, $tableName) {
    try {
        // Check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() === 0) {
            return [
                'exists' => false,
                'message' => "Table '$tableName' does not exist."
            ];
        }
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE `$tableName`");
        $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get table creation SQL
        $stmt = $pdo->query("SHOW CREATE TABLE `$tableName`");
        $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'exists' => true,
            'structure' => $structure,
            'create_sql' => $createTable["Create Table"] ?? null
        ];
    } catch (PDOException $e) {
        return [
            'error' => true,
            'message' => 'Error checking table structure: ' . $e->getMessage()
        ];
    }
}

// Main execution
echo "<h2>Database Connection Test</h2>";
$connectionTest = testDatabaseConnection($config);

if ($connectionTest['success']) {
    echo "<div style='color: green;'>{$connectionTest['message']}</div>";
    
    // Check tev_claims table
    echo "<h3>Checking tev_claims table</h3>";
    $tableCheck = checkTableStructure($connectionTest['pdo'], 'tev_claims');
    
    if (isset($tableCheck['error'])) {
        echo "<div style='color: red;'>{$tableCheck['message']}</div>";
    } elseif (!$tableCheck['exists']) {
        echo "<div style='color: orange;'>{$tableCheck['message']}</div>";
    } else {
        echo "<h4>Table Structure:</h4>";
        echo "<pre>";
        print_r($tableCheck['structure']);
        echo "</pre>";
        
        echo "<h4>Create Table SQL:</h4>";
        echo "<pre>";
        echo htmlspecialchars($tableCheck['create_sql']);
        echo "</pre>";
    }
    
    // Check projects table (foreign key reference)
    echo "<h3>Checking projects table</h3>";
    $projectsCheck = checkTableStructure($connectionTest['pdo'], 'projects');
    if (isset($projectsCheck['error'])) {
        echo "<div style='color: red;'>{$projectsCheck['message']}</div>";
    } else {
        echo "<div>Table exists: " . ($projectsCheck['exists'] ? 'Yes' : 'No') . "</div>";
    }
    
    // Check activities table (foreign key reference)
    echo "<h3>Checking activities table</h3>";
    $activitiesCheck = checkTableStructure($connectionTest['pdo'], 'activities');
    if (isset($activitiesCheck['error'])) {
        echo "<div style='color: red;'>{$activitiesCheck['message']}</div>";
    } else {
        echo "<div>Table exists: " . ($activitiesCheck['exists'] ? 'Yes' : 'No') . "</div>";
    }
    
} else {
    echo "<div style='color: red;'>{$connectionTest['message']}</div>";
    
    // Additional diagnostics for connection issues
    echo "<h3>Connection Details:</h3>";
    echo "<pre>";
    echo "Host: {$config['host']}\n";
    echo "Database: {$config['dbname']}\n";
    echo "Username: {$config['username']}\n";
    echo "</pre>";
    
    // Try to connect without database to check if MySQL is running
    try {
        $dsn = "mysql:host={$config['host']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        echo "<div style='color: green;'>✓ Successfully connected to MySQL server</div>";
        
        // List available databases
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h4>Available Databases:</h4>";
        echo "<ul>";
        foreach ($databases as $db) {
            echo "<li>$db" . ($db === $config['dbname'] ? ' (Selected)' : '') . "</li>";
        }
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<div style='color: red;'>✗ Could not connect to MySQL server: " . $e->getMessage() . "</div>";
    }
}

echo "<h3>PHP Info:</h3>";
echo "<div>PHP Version: " . phpversion() . "</div>";
echo "<div>PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "</div>";
echo "<div>PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "</div>";

// Display any PHP errors that might have occurred
if (error_get_last()) {
    echo "<h3>PHP Errors:</h3>";
    echo "<pre>";
    print_r(error_get_last());
    echo "</pre>";
}
?>
