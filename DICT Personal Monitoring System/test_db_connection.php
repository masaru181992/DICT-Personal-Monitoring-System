<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config/database.php';

// Test database connection
try {
    // Test connection
    $pdo->query('SELECT 1');
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test users table
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>User data: " . print_r($user, true) . "</p>";
    
    // Test tev_claims table
    $stmt = $pdo->query("SHOW CREATE TABLE tev_claims");
    $table = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>TEV Claims table structure: <pre>" . htmlspecialchars($table['Create Table'] ?? 'Table not found') . "</pre></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
    
    // Show connection details (without password)
    echo "<h3>Connection Details:</h3>";
    echo "<pre>";
    echo "Host: localhost\n";
    echo "Database: dict_monitoring\n";
    echo "Username: root\n";
    echo "Password: [hidden]\n";
    echo "</pre>";
}

// Test session
session_start();
echo "<h3>Session Info:</h3>";
echo "<pre>";
print_r([
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Not set',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Not set'
]);
echo "</pre>";
