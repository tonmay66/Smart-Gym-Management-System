-- Check workout assignments to see what's stored
SELECT 
    wa.id,
    wa.workout_id,
    wa.member_id,
    wa.assigned_by,
    member.full_name as member_name,
    member.email as member_email,
    assigner.full_name as assigned_by_name
FROM workout_assignments wa
JOIN users member ON wa.member_id = member.id
JOIN users assigner ON wa.assigned_by = assigner.id
ORDER BY wa.id DESC
LIMIT 10;
