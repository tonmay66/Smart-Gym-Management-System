<?php
declare(strict_types=1);

/**
 * Clean Sample Data Script
 * Removes all sample data from the database while keeping the schema
 */

require_once __DIR__ . '/app/config/db_connect.php';

try {
    $db = db_connect();
    
    echo "Starting database cleanup...\n\n";
    
    // Disable foreign key checks temporarily
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Delete all data from tables
    $tables = [
        'workout_assignments',
        'workouts',
        'payments',
        'memberships',
        'membership_plans',
        'trainer_schedules',
        'user_profiles',
        'users'
    ];
    
    foreach ($tables as $table) {
        $db->exec("DELETE FROM $table");
        echo "✓ Cleared table: $table\n";
    }
    
    // Reset auto-increment counters
    foreach ($tables as $table) {
        $db->exec("ALTER TABLE $table AUTO_INCREMENT = 1");
    }
    echo "\n✓ Reset all auto-increment counters\n\n";
    
    // Re-enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Insert only the admin user
    $db->exec("
        INSERT INTO users (role, full_name, email, phone, password_hash, is_active) VALUES
        ('admin', 'System Admin', 'admin@gym.com', '01711111111', 'password123', 1)
    ");
    echo "✓ Created admin user (email: admin@gym.com, password: password123)\n";
    
    // Insert admin profile
    $db->exec("
        INSERT INTO user_profiles (user_id, gender, date_of_birth, address, emergency_name, emergency_phone) VALUES
        (1, 'male', '1985-05-15', 'Gym Headquarters', 'Emergency Contact', '01611111111')
    ");
    echo "✓ Created admin profile\n\n";
    
    echo "========================================\n";
    echo "DATABASE CLEANUP COMPLETED SUCCESSFULLY!\n";
    echo "========================================\n\n";
    echo "Summary:\n";
    echo "- All sample data removed\n";
    echo "- All tables reset\n";
    echo "- Admin account created\n\n";
    echo "Login credentials:\n";
    echo "  Email: admin@gym.com\n";
    echo "  Password: password123\n\n";
    echo "You can now add real users through the admin panel.\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
