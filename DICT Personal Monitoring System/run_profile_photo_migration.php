<?php
require_once 'config/database.php';

try {
    // Check if columns already exist
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_photo'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        // Run the migration
        $sql = "
            ALTER TABLE users 
            ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL,
            ADD COLUMN profile_photo_updated_at TIMESTAMP NULL DEFAULT NULL;
            
            CREATE INDEX idx_users_profile_photo ON users(profile_photo);
        ";
        
        $pdo->exec($sql);
        echo "Migration completed successfully! Profile photo columns added to users table.\n";
    } else {
        echo "Migration already completed. Profile photo columns exist.\n";
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/uploads/profile_photos';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "Created upload directory: $uploadDir\n";
    } else {
        echo "Upload directory already exists.\n";
    }
    
    echo "Profile photo setup is complete!\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
