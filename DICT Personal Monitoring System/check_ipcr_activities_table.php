<?php
// Database configuration
$host = 'localhost';
$dbname = 'dict_monitoring';
$username = 'root';
$password = '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Checking ipcr_activities table structure</h2>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'ipcr_activities'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ ipcr_activities table exists</p>";
        
        // Show table structure
        echo "<h3>Table Structure:</h3>";
        $structure = $pdo->query("DESCRIBE ipcr_activities");
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        echo "<h3>Sample Data (first 10 rows):</h3>";
        $data = $pdo->query("SELECT * FROM ipcr_activities LIMIT 10");
        if ($data->rowCount() > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            $first = true;
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
                if ($first) {
                    echo "<tr>";
                    foreach (array_keys($row) as $key) {
                        echo "<th>" . htmlspecialchars($key) . "</th>";
                    }
                    echo "</tr>";
                    $first = false;
                }
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data found in ipcr_activities table</p>";
        }
        
        // Check foreign key constraints
        echo "<h3>Foreign Key Constraints:</h3>";
        $constraints = $pdo->query("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE 
                TABLE_SCHEMA = '$dbname' AND
                TABLE_NAME = 'ipcr_activities' AND
                REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if ($constraints->rowCount() > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>Column</th><th>References</th><th>Referenced Table</th><th>Referenced Column</th></tr>";
            while ($row = $constraints->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['COLUMN_NAME']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CONSTRAINT_NAME']) . "</td>";
                echo "<td>" . htmlspecialchars($row['REFERENCED_TABLE_NAME']) . "</td>";
                echo "<td>" . htmlspecialchars($row['REFERENCED_COLUMN_NAME']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No foreign key constraints found on ipcr_activities table</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ ipcr_activities table does not exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "<h3>Database Error:</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

// Check if activities table exists
$stmt = $pdo->query("SHOW TABLES LIKE 'activities'");
if ($stmt->rowCount() > 0) {
    echo "<h3>Activities Table Sample (first 5 rows):</h3>";
    $activities = $pdo->query("SELECT id, title FROM activities LIMIT 5");
    if ($activities->rowCount() > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th></tr>";
        while ($row = $activities->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No activities found in the activities table</p>";
    }
} else {
    echo "<p style='color: red;'>Activities table does not exist</p>";
}
?>
