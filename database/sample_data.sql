-- ============================================
-- Sample Data for Gym Management System
-- ============================================

-- ============================================
-- 1. INSERT USERS
-- ============================================
-- Note: In production, passwords should be hashed properly
-- For demo purposes, using plain text (password: "password123")

INSERT INTO users (role, full_name, email, phone, password_hash, is_active) VALUES
-- Admin
('admin', 'John Admin', 'admin@gym.com', '01711111111', 'password123', 1),

-- Trainers
('trainer', 'Mike Johnson', 'mike@gym.com', '01722222222', 'password123', 1),
('trainer', 'Sarah Williams', 'sarah@gym.com', '01733333333', 'password123', 1),
('trainer', 'David Brown', 'david@gym.com', '01744444444', 'password123', 1),

-- Members
('member', 'Alice Smith', 'alice@example.com', '01755555555', 'password123', 1),
('member', 'Bob Jones', 'bob@example.com', '01766666666', 'password123', 1),
('member', 'Carol White', 'carol@example.com', '01777777777', 'password123', 1),
('member', 'Daniel Green', 'daniel@example.com', '01788888888', 'password123', 1),
('member', 'Emma Davis', 'emma@example.com', '01799999999', 'password123', 1),
('member', 'Frank Miller', 'frank@example.com', '01700000000', 'password123', 1);

-- ============================================
-- 2. INSERT USER PROFILES
-- ============================================
INSERT INTO user_profiles (user_id, gender, date_of_birth, address, emergency_name, emergency_phone) VALUES
-- Admin profile
(1, 'male', '1985-05-15', '123 Admin Street, Dhaka', 'Jane Admin', '01611111111'),

-- Trainer profiles
(2, 'male', '1990-03-20', '456 Trainer Ave, Dhaka', 'Lisa Johnson', '01622222222'),
(3, 'female', '1988-07-12', '789 Fitness Road, Dhaka', 'Tom Williams', '01633333333'),
(4, 'male', '1992-11-08', '321 Gym Lane, Dhaka', 'Mary Brown', '01644444444'),

-- Member profiles
(5, 'female', '1995-01-25', '111 Member St, Dhaka', 'John Smith', '01655555555'),
(6, 'male', '1993-06-18', '222 Health Blvd, Dhaka', 'Mary Jones', '01666666666'),
(7, 'female', '1991-09-30', '333 Wellness Dr, Dhaka', 'Steve White', '01677777777'),
(8, 'male', '1994-12-05', '444 Fitness Way, Dhaka', 'Anna Green', '01688888888'),
(9, 'female', '1996-04-22', '555 Gym Plaza, Dhaka', 'Robert Davis', '01699999999'),
(10, 'male', '1989-08-14', '666 Sport Center, Dhaka', 'Linda Miller', '01600000000');

-- ============================================
-- 3. INSERT MEMBERSHIP PLANS
-- ============================================
INSERT INTO membership_plans (name, description, duration_months, price, features, is_active) VALUES
('Basic', 'Access to gym equipment and basic facilities', 1, 1500.00, 'Gym access, Locker facility, Basic equipment', 1),
('Premium', 'Includes personal training sessions', 1, 3000.00, 'All Basic features, 4 PT sessions/month, Nutrition guidance', 1),
('Elite', 'Full access with unlimited training', 3, 8000.00, 'All Premium features, Unlimited PT sessions, Diet plan, Group classes', 1),
('Annual Basic', 'Annual basic membership with discount', 12, 15000.00, 'All Basic features for 12 months, 2 months free', 1),
('Annual Premium', 'Annual premium membership', 12, 30000.00, 'All Premium features for 12 months, 2 months free', 1);

-- ============================================
-- 4. INSERT MEMBERSHIPS
-- ============================================
INSERT INTO memberships (member_id, plan_id, trainer_id, start_date, end_date, status) VALUES
-- Active memberships
(5, 2, 2, '2026-01-01', '2026-02-01', 'active'),
(6, 1, NULL, '2026-01-05', '2026-02-05', 'active'),
(7, 3, 3, '2025-12-15', '2026-03-15', 'active'),
(8, 2, 2, '2026-01-10', '2026-02-10', 'active'),
(9, 1, NULL, '2026-01-15', '2026-02-15', 'active'),
(10, 2, 4, '2025-12-20', '2026-01-20', 'active');

-- ============================================
-- 5. INSERT PAYMENTS
-- ============================================
INSERT INTO payments (membership_id, user_id, amount, payment_method, transaction_id, status, payment_date) VALUES
(1, 5, 3000.00, 'card', 'TXN001', 'completed', '2026-01-01 10:30:00'),
(2, 6, 1500.00, 'cash', NULL, 'completed', '2026-01-05 14:15:00'),
(3, 7, 8000.00, 'online', 'TXN002', 'completed', '2025-12-15 09:45:00'),
(4, 8, 3000.00, 'bank_transfer', 'TXN003', 'completed', '2026-01-10 11:20:00'),
(5, 9, 1500.00, 'card', 'TXN004', 'completed', '2026-01-15 16:00:00'),
(6, 10, 3000.00, 'online', 'TXN005', 'completed', '2025-12-20 13:30:00');

-- ============================================
-- 6. INSERT WORKOUTS
-- ============================================
INSERT INTO workouts (trainer_id, name, description, difficulty_level, duration_minutes, exercises) VALUES
(2, 'Full Body Strength', 'Complete body workout focusing on major muscle groups', 'intermediate', 60, 'Squats, Bench Press, Deadlifts, Rows, Shoulder Press'),
(2, 'Cardio Blast', 'High intensity cardio workout', 'beginner', 45, 'Running, Jumping Jacks, Burpees, Mountain Climbers'),
(3, 'Core Power', 'Intensive core strengthening routine', 'intermediate', 30, 'Planks, Crunches, Russian Twists, Leg Raises'),
(3, 'Yoga Flow', 'Relaxing yoga session for flexibility', 'beginner', 60, 'Sun Salutation, Warrior Poses, Tree Pose, Savasana'),
(4, 'HIIT Training', 'High Intensity Interval Training', 'advanced', 45, 'Sprints, Box Jumps, Kettlebell Swings, Battle Ropes'),
(4, 'Upper Body Focus', 'Concentrated upper body workout', 'intermediate', 50, 'Pull-ups, Dips, Bicep Curls, Tricep Extensions');

-- ============================================
-- 7. INSERT WORKOUT ASSIGNMENTS
-- ============================================
INSERT INTO workout_assignments (workout_id, member_id, assigned_by, assigned_date, notes, status) VALUES
(1, 5, 2, '2026-01-02', 'Start with lighter weights', 'active'),
(2, 5, 2, '2026-01-03', 'Focus on form', 'active'),
(3, 7, 3, '2025-12-16', 'Great progress so far', 'active'),
(4, 7, 3, '2025-12-17', 'Flexibility improving', 'completed'),
(5, 8, 2, '2026-01-11', 'Advanced member, push hard', 'active'),
(6, 10, 4, '2025-12-21', 'Building upper body strength', 'active');

-- ============================================
-- 8. INSERT TRAINER SCHEDULES
-- ============================================
INSERT INTO trainer_schedules (trainer_id, day_of_week, start_time, end_time, activity, location, max_capacity, is_active) VALUES
-- Mike Johnson's schedule
(2, 'Monday', '09:00:00', '10:00:00', 'Personal Training', 'Training Room 1', 1, 1),
(2, 'Monday', '14:00:00', '15:00:00', 'Group Class - Strength', 'Main Hall', 15, 1),
(2, 'Wednesday', '09:00:00', '10:00:00', 'Personal Training', 'Training Room 1', 1, 1),
(2, 'Wednesday', '16:00:00', '17:00:00', 'Cardio Class', 'Cardio Zone', 20, 1),
(2, 'Friday', '10:00:00', '11:00:00', 'Personal Training', 'Training Room 1', 1, 1),

-- Sarah Williams's schedule
(3, 'Tuesday', '08:00:00', '09:00:00', 'Yoga Class', 'Yoga Studio', 12, 1),
(3, 'Tuesday', '15:00:00', '16:00:00', 'Core Training', 'Training Room 2', 10, 1),
(3, 'Thursday', '08:00:00', '09:00:00', 'Yoga Class', 'Yoga Studio', 12, 1),
(3, 'Thursday', '17:00:00', '18:00:00', 'Flexibility Session', 'Yoga Studio', 8, 1),
(3, 'Saturday', '09:00:00', '10:30:00', 'Weekend Yoga', 'Yoga Studio', 15, 1),

-- David Brown's schedule
(4, 'Monday', '11:00:00', '12:00:00', 'HIIT Training', 'Main Hall', 12, 1),
(4, 'Tuesday', '10:00:00', '11:00:00', 'Personal Training', 'Training Room 3', 1, 1),
(4, 'Wednesday', '11:00:00', '12:00:00', 'Upper Body Class', 'Weight Room', 10, 1),
(4, 'Thursday', '10:00:00', '11:00:00', 'Personal Training', 'Training Room 3', 1, 1),
(4, 'Friday', '14:00:00', '15:00:00', 'HIIT Training', 'Main Hall', 12, 1);
