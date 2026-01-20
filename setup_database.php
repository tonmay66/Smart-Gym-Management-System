<?php
/**
 * Database Setup Script
 * Run this file once to create and populate the database
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'smart_gym';

echo "=== Gym Management System - Database Setup ===\n\n";

try {
    // Connect to MySQL server (without database)
    echo "1. Connecting to MySQL server...\n";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ Connected successfully\n\n";

    // Create database if not exists
    echo "2. Creating database '$dbname'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "   ✓ Database created/verified\n\n";

    // Use the database
    $pdo->exec("USE $dbname");

    // Read and execute schema.sql
    echo "3. Creating tables from schema.sql...\n";
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    if ($schema === false) {
        throw new Exception("Could not read schema.sql file");
    }
    $pdo->exec($schema);
    echo "   ✓ Tables created successfully\n\n";

    // Read and execute sample_data.sql
    echo "4. Inserting sample data...\n";
    $sampleData = file_get_contents(__DIR__ . '/database/sample_data.sql');
    if ($sampleData === false) {
        throw new Exception("Could not read sample_data.sql file");
    }
    $pdo->exec($sampleData);
    echo "   ✓ Sample data inserted successfully\n\n";

    // Verify setup
    echo "5. Verifying setup...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   ✓ Users table: $userCount records\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM membership_plans");
    $planCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   ✓ Membership plans: $planCount records\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM memberships");
    $membershipCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   ✓ Memberships: $membershipCount records\n\n";

    echo "=== Setup Complete! ===\n\n";
    echo "You can now login with:\n";
    echo "Admin: admin@gym.com / password123\n";
    echo "Trainer: mike@gym.com / password123\n";
    echo "Member: alice@example.com / password123\n\n";
    echo "Access the application at: http://localhost/gymm/public/login\n";
    echo "Or run: php -S localhost:8000 -t public\n";
    echo "Then visit: http://localhost:8000/login\n";

} catch (PDOException $e) {
    echo "\n❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
