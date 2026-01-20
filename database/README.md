# Database Setup Guide

This directory contains all SQL files needed to set up the gym management system database.

## Files

1. **schema.sql** - Database structure (tables, indexes, foreign keys)
2. **sample_data.sql** - Sample data for testing
3. **useful_queries.sql** - Common queries for reference

## Setup Instructions

### Method 1: Using phpMyAdmin

1. Open phpMyAdmin in your browser
2. Create a new database named `gym_management`
3. Select the database
4. Go to the "Import" tab
5. Import files in this order:
   - First: `schema.sql`
   - Second: `sample_data.sql`

### Method 2: Using MySQL Command Line

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE gym_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p gym_management < schema.sql

# Import sample data
mysql -u root -p gym_management < sample_data.sql
```

### Method 3: Using XAMPP/WAMP

1. Start Apache and MySQL from XAMPP/WAMP control panel
2. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
3. Follow Method 1 instructions above

## Database Configuration

Update your database connection file: `app/config/db_connect.php`

```php
<?php
function db_connect(): PDO {
    $host = 'localhost';
    $dbname = 'gym_management';
    $username = 'root';
    $password = '';  // Your MySQL password
    
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
```

## Database Structure

### Tables Overview

| Table | Description |
|-------|-------------|
| `users` | All system users (admin, trainers, members) |
| `user_profiles` | Extended user information |
| `membership_plans` | Available membership plans |
| `memberships` | Active/past member subscriptions |
| `payments` | Payment transactions |
| `workouts` | Workout plans created by trainers |
| `workout_assignments` | Workouts assigned to members |
| `trainer_schedules` | Trainer availability schedules |

### Entity Relationships

```
users (1) ----< (N) user_profiles
users (1) ----< (N) memberships
users (1) ----< (N) payments
users (1) ----< (N) workouts (as trainer)
users (1) ----< (N) workout_assignments (as member)
users (1) ----< (N) trainer_schedules

membership_plans (1) ----< (N) memberships
memberships (1) ----< (N) payments
workouts (1) ----< (N) workout_assignments
```

## Sample Login Credentials

After importing sample data, you can login with:

### Admin
- Email: `admin@gym.com`
- Password: `password123`

### Trainers
- Email: `mike@gym.com` | Password: `password123`
- Email: `sarah@gym.com` | Password: `password123`
- Email: `david@gym.com` | Password: `password123`

### Members
- Email: `alice@example.com` | Password: `password123`
- Email: `bob@example.com` | Password: `password123`
- Email: `carol@example.com` | Password: `password123`

## Common Operations

### Check Database Connection

```php
<?php
require_once 'app/config/db_connect.php';
try {
    $pdo = db_connect();
    echo "Database connected successfully!";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
```

### Verify Tables

```sql
SHOW TABLES;
```

### Check Sample Data

```sql
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_memberships FROM memberships;
SELECT COUNT(*) as total_payments FROM payments;
```

## Troubleshooting

### Error: "Table doesn't exist"
- Make sure you imported `schema.sql` first
- Check if you selected the correct database

### Error: "Access denied"
- Verify your MySQL username and password
- Check if the user has proper permissions

### Error: "Foreign key constraint fails"
- Import files in the correct order (schema first, then data)
- Make sure all referenced tables exist

### Error: "Duplicate entry"
- Drop all tables and reimport
- Or use `TRUNCATE TABLE` to clear existing data

## Backup Database

```bash
# Backup entire database
mysqldump -u root -p gym_management > backup.sql

# Backup only structure
mysqldump -u root -p --no-data gym_management > structure_only.sql

# Backup only data
mysqldump -u root -p --no-create-info gym_management > data_only.sql
```

## Reset Database

```sql
DROP DATABASE IF EXISTS gym_management;
CREATE DATABASE gym_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gym_management;
SOURCE schema.sql;
SOURCE sample_data.sql;
```
