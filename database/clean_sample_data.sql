-- ============================================
-- Clean Sample Data - Remove All Test Data
-- ============================================
-- This script removes all sample data from the database
-- while preserving the schema and any real user-created data

-- Delete all workout assignments
DELETE FROM workout_assignments;

-- Delete all workouts
DELETE FROM workouts;

-- Delete all payments
DELETE FROM payments;

-- Delete all memberships
DELETE FROM memberships;

-- Delete all membership plans
DELETE FROM membership_plans;

-- Delete all trainer schedules
DELETE FROM trainer_schedules;

-- Delete all user profiles
DELETE FROM user_profiles;

-- Delete all users
DELETE FROM users;

-- Reset auto-increment counters
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE user_profiles AUTO_INCREMENT = 1;
ALTER TABLE membership_plans AUTO_INCREMENT = 1;
ALTER TABLE memberships AUTO_INCREMENT = 1;
ALTER TABLE payments AUTO_INCREMENT = 1;
ALTER TABLE workouts AUTO_INCREMENT = 1;
ALTER TABLE workout_assignments AUTO_INCREMENT = 1;
ALTER TABLE trainer_schedules AUTO_INCREMENT = 1;

-- Insert only the admin user for system access
INSERT INTO users (role, full_name, email, phone, password_hash, is_active) VALUES
('admin', 'System Admin', 'admin@gym.com', '01711111111', 'password123', 1);

-- Insert admin profile
INSERT INTO user_profiles (user_id, gender, date_of_birth, address, emergency_name, emergency_phone) VALUES
(1, 'male', '1985-05-15', 'Gym Headquarters', 'Emergency Contact', '01611111111');

-- Success message
SELECT 'Database cleaned successfully! All sample data removed. Only admin account remains.' AS Status;
