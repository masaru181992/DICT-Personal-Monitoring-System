<?php
// Database configuration
$host = 'localhost';
$dbname = 'dict_pms';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get table structure
    $stmt = $pdo->query("SHOW CREATE TABLE tev_claims");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>TEV Claims Table Structure:</h2>";
    echo "<pre>" . htmlspecialchars($result['Create Table']) . "</pre>";
    
    // Check if payment_date column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM tev_claims LIKE 'payment_date'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        echo "<p style='color: red;'>The 'payment_date' column does not exist in the tev_claims table.</p>";
        
        // Ask if user wants to add the column
        if (isset($_POST['add_column'])) {
            try {
                $pdo->exec("ALTER TABLE tev_claims ADD COLUMN payment_date DATE NULL AFTER status");
                echo "<p style='color: green;'>Successfully added 'payment_date' column to tev_claims table.</p>";
                // Refresh the page to show the updated structure
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Error adding column: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<form method='post'>";
            echo "<input type='submit' name='add_column' value='Add payment_date Column' class='btn btn-primary'>";
            echo "</form>";
        }
    } else {
        echo "<p style='color: green;'>The 'payment_date' column already exists in the tev_claims table.</p>";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
