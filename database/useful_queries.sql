-- ============================================
-- Useful Queries for Gym Management System
-- ============================================

-- ============================================
-- USER QUERIES
-- ============================================

-- Get all active users by role
SELECT * FROM users WHERE role = 'member' AND is_active = 1;
SELECT * FROM users WHERE role = 'trainer' AND is_active = 1;
SELECT * FROM users WHERE role = 'admin' AND is_active = 1;

-- Get user with profile information
SELECT 
    u.id, u.role, u.full_name, u.email, u.phone, u.is_active,
    p.gender, p.date_of_birth, p.address, p.emergency_name, p.emergency_phone
FROM users u
LEFT JOIN user_profiles p ON u.id = p.user_id
WHERE u.id = 5;

-- Count users by role
SELECT role, COUNT(*) as total 
FROM users 
GROUP BY role;

-- ============================================
-- MEMBERSHIP QUERIES
-- ============================================

-- Get active memberships with member and plan details
SELECT 
    m.id, m.start_date, m.end_date, m.status,
    u.full_name as member_name, u.email,
    mp.name as plan_name, mp.price,
    t.full_name as trainer_name
FROM memberships m
JOIN users u ON m.member_id = u.id
JOIN membership_plans mp ON m.plan_id = mp.id
LEFT JOIN users t ON m.trainer_id = t.id
WHERE m.status = 'active'
ORDER BY m.start_date DESC;

-- Get membership details for a specific member
SELECT 
    m.id, m.start_date, m.end_date, m.status,
    mp.name as plan_name, mp.price, mp.duration_months,
    t.full_name as trainer_name
FROM memberships m
JOIN membership_plans mp ON m.plan_id = mp.id
LEFT JOIN users t ON m.trainer_id = t.id
WHERE m.member_id = 5
ORDER BY m.created_at DESC
LIMIT 1;

-- Get expiring memberships (within 7 days)
SELECT 
    u.full_name, u.email, u.phone,
    m.end_date, mp.name as plan_name
FROM memberships m
JOIN users u ON m.member_id = u.id
JOIN membership_plans mp ON m.plan_id = mp.id
WHERE m.status = 'active' 
AND m.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY m.end_date;

-- ============================================
-- PAYMENT QUERIES
-- ============================================

-- Get all payments with member details
SELECT 
    p.id, p.amount, p.payment_method, p.status, p.payment_date,
    u.full_name as member_name, u.email,
    mp.name as plan_name
FROM payments p
JOIN users u ON p.user_id = u.id
JOIN memberships m ON p.membership_id = m.id
JOIN membership_plans mp ON m.plan_id = mp.id
ORDER BY p.payment_date DESC;

-- Get payment history for a specific member
SELECT 
    p.id, p.amount, p.payment_method, p.status, p.payment_date,
    mp.name as plan_name
FROM payments p
JOIN memberships m ON p.membership_id = m.id
JOIN membership_plans mp ON m.plan_id = mp.id
WHERE p.user_id = 5
ORDER BY p.payment_date DESC;

-- Get total revenue by month
SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') as month,
    COUNT(*) as total_payments,
    SUM(amount) as total_revenue
FROM payments
WHERE status = 'completed'
GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
ORDER BY month DESC;

-- Get revenue by payment method
SELECT 
    payment_method,
    COUNT(*) as transaction_count,
    SUM(amount) as total_amount
FROM payments
WHERE status = 'completed'
GROUP BY payment_method;

-- ============================================
-- TRAINER QUERIES
-- ============================================

-- Get members assigned to a specific trainer
SELECT 
    u.id, u.full_name, u.email, u.phone,
    mp.name as plan_name,
    m.start_date, m.end_date, m.status
FROM memberships m
JOIN users u ON m.member_id = u.id
JOIN membership_plans mp ON m.plan_id = mp.id
WHERE m.trainer_id = 2 AND m.status = 'active';

-- Get trainer's schedule for a specific day
SELECT 
    ts.day_of_week, ts.start_time, ts.end_time,
    ts.activity, ts.location, ts.max_capacity,
    u.full_name as trainer_name
FROM trainer_schedules ts
JOIN users u ON ts.trainer_id = u.id
WHERE ts.trainer_id = 2 AND ts.day_of_week = 'Monday' AND ts.is_active = 1
ORDER BY ts.start_time;

-- Get all schedules for a trainer
SELECT 
    day_of_week, start_time, end_time, activity, location, max_capacity
FROM trainer_schedules
WHERE trainer_id = 2 AND is_active = 1
ORDER BY 
    FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
    start_time;

-- Count members per trainer
SELECT 
    t.id, t.full_name as trainer_name,
    COUNT(m.id) as active_members
FROM users t
LEFT JOIN memberships m ON t.id = m.trainer_id AND m.status = 'active'
WHERE t.role = 'trainer' AND t.is_active = 1
GROUP BY t.id, t.full_name;

-- ============================================
-- WORKOUT QUERIES
-- ============================================

-- Get all workouts created by a trainer
SELECT 
    w.id, w.name, w.description, w.difficulty_level, w.duration_minutes,
    COUNT(wa.id) as assigned_count
FROM workouts w
LEFT JOIN workout_assignments wa ON w.id = wa.workout_id
WHERE w.trainer_id = 2
GROUP BY w.id
ORDER BY w.created_at DESC;

-- Get workouts assigned to a member
SELECT 
    w.id, w.name, w.description, w.difficulty_level, w.duration_minutes,
    wa.assigned_date, wa.status, wa.notes,
    t.full_name as trainer_name
FROM workout_assignments wa
JOIN workouts w ON wa.workout_id = w.id
JOIN users t ON wa.assigned_by = t.id
WHERE wa.member_id = 5 AND wa.status = 'active'
ORDER BY wa.assigned_date DESC;

-- Get workout statistics by difficulty level
SELECT 
    difficulty_level,
    COUNT(*) as total_workouts,
    AVG(duration_minutes) as avg_duration
FROM workouts
GROUP BY difficulty_level;

-- ============================================
-- DASHBOARD STATISTICS
-- ============================================

-- Admin Dashboard Stats
SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'member' AND is_active = 1) as total_members,
    (SELECT COUNT(*) FROM users WHERE role = 'trainer' AND is_active = 1) as total_trainers,
    (SELECT COUNT(*) FROM memberships WHERE status = 'active') as active_memberships,
    (SELECT SUM(amount) FROM payments WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURDATE())) as monthly_revenue;

-- Member Dashboard Stats
SELECT 
    (SELECT COUNT(*) FROM workout_assignments WHERE member_id = 5 AND status = 'active') as active_workouts,
    (SELECT COUNT(*) FROM payments WHERE user_id = 5) as total_payments,
    (SELECT SUM(amount) FROM payments WHERE user_id = 5 AND status = 'completed') as total_spent;

-- Trainer Dashboard Stats
SELECT 
    (SELECT COUNT(*) FROM memberships WHERE trainer_id = 2 AND status = 'active') as active_members,
    (SELECT COUNT(*) FROM workouts WHERE trainer_id = 2) as total_workouts,
    (SELECT COUNT(*) FROM workout_assignments WHERE assigned_by = 2 AND status = 'active') as active_assignments;

-- ============================================
-- SEARCH QUERIES
-- ============================================

-- Search users by name or email
SELECT id, role, full_name, email, phone, is_active
FROM users
WHERE (full_name LIKE '%john%' OR email LIKE '%john%')
AND is_active = 1;

-- Search workouts by name or description
SELECT id, name, description, difficulty_level, duration_minutes
FROM workouts
WHERE name LIKE '%strength%' OR description LIKE '%strength%';

-- ============================================
-- REPORT QUERIES
-- ============================================

-- Monthly membership report
SELECT 
    DATE_FORMAT(m.start_date, '%Y-%m') as month,
    COUNT(*) as new_memberships,
    SUM(mp.price) as total_value
FROM memberships m
JOIN membership_plans mp ON m.plan_id = mp.id
GROUP BY DATE_FORMAT(m.start_date, '%Y-%m')
ORDER BY month DESC;

-- Popular membership plans
SELECT 
    mp.name, mp.price,
    COUNT(m.id) as total_subscriptions,
    SUM(mp.price) as total_revenue
FROM membership_plans mp
LEFT JOIN memberships m ON mp.id = m.plan_id
GROUP BY mp.id
ORDER BY total_subscriptions DESC;

-- Trainer performance report
SELECT 
    t.full_name as trainer_name,
    COUNT(DISTINCT m.member_id) as total_members,
    COUNT(DISTINCT w.id) as total_workouts,
    COUNT(DISTINCT wa.id) as total_assignments
FROM users t
LEFT JOIN memberships m ON t.id = m.trainer_id AND m.status = 'active'
LEFT JOIN workouts w ON t.id = w.trainer_id
LEFT JOIN workout_assignments wa ON t.id = wa.assigned_by
WHERE t.role = 'trainer' AND t.is_active = 1
GROUP BY t.id
ORDER BY total_members DESC;
