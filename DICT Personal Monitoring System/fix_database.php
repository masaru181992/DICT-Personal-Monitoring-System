<?php
require_once 'config/database.php';

echo "<h2>Database Fix Utility</h2>";

try {
    // Check and create required tables if they don't exist
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'projects' => "CREATE TABLE IF NOT EXISTS projects (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            start_date DATE,
            end_date DATE,
            status ENUM('Not Started', 'In Progress', 'Completed', 'On Hold') DEFAULT 'Not Started',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'activities' => "CREATE TABLE IF NOT EXISTS activities (
            id INT PRIMARY KEY AUTO_INCREMENT,
            project_id INT,
            user_id INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            target_date DATE,
            end_date DATE,
            status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'notes' => "CREATE TABLE IF NOT EXISTS notes (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            project_id INT,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            status ENUM('active', 'completed', 'archived') DEFAULT 'active',
            reminder_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'overtime_requests' => "CREATE TABLE IF NOT EXISTS overtime_requests (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            date DATE NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            total_hours DECIMAL(5,2) NOT NULL,
            total_days DECIMAL(5,2) NOT NULL,
            used_days DECIMAL(5,2) DEFAULT 0,
            reason TEXT NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    // Create tables if they don't exist
    foreach ($tables as $table => $sql) {
        $pdo->exec($sql);
        echo "<p>✅ Table '$table' checked/created successfully</p>";
    }

    // Check if admin user exists, if not create one
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
    $adminExists = $stmt->fetch()['count'] > 0;
    
    if (!$adminExists) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, password, full_name) VALUES ('admin', '$hashedPassword', 'System Administrator')");
        echo "<p>✅ Created default admin user (username: admin, password: admin123)</p>";
    } else {
        echo "<p>✅ Admin user already exists</p>";
    }

    echo "<h3>Database setup completed successfully!</h3>";
    echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";

} catch (PDOException $e) {
    die("<div style='color: red;'><h3>Error:</h3><p>" . $e->getMessage() . "</p></div>");
}
?>
