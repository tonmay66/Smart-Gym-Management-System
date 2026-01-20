<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class WorkoutModel {
  private PDO $db;

  public function __construct() {
    $this->db = db_connect();
  }

  /**
   * Create a new workout
   */
  public function createWorkout(int $trainerId, string $name, string $description, string $difficultyLevel, ?int $durationMinutes, ?string $exercises): ?int {
    $stmt = $this->db->prepare("
      INSERT INTO workouts (trainer_id, name, description, difficulty_level, duration_minutes, exercises)
      VALUES (:trainer_id, :name, :description, :difficulty_level, :duration_minutes, :exercises)
    ");
    
    $stmt->execute([
      ':trainer_id' => $trainerId,
      ':name' => $name,
      ':description' => $description,
      ':difficulty_level' => $difficultyLevel,
      ':duration_minutes' => $durationMinutes,
      ':exercises' => $exercises
    ]);
    
    return (int)$this->db->lastInsertId();
  }

  /**
   * Get all workouts by a specific trainer
   */
  public function getWorkoutsByTrainer(int $trainerId): array {
    $stmt = $this->db->prepare("
      SELECT id, trainer_id, name, description, difficulty_level, duration_minutes, exercises, created_at, updated_at
      FROM workouts
      WHERE trainer_id = :trainer_id
      ORDER BY created_at DESC
    ");
    
    $stmt->execute([':trainer_id' => $trainerId]);
    
    return $stmt->fetchAll();
  }

  /**
   * Get all workouts (for admin or general listing)
   */
  public function getAllWorkouts(): array {
    $stmt = $this->db->query("
      SELECT w.id, w.trainer_id, w.name, w.description, w.difficulty_level, w.duration_minutes, w.exercises, w.created_at, w.updated_at,
             u.full_name as trainer_name
      FROM workouts w
      LEFT JOIN users u ON w.trainer_id = u.id
      ORDER BY w.created_at DESC
    ");
    
    return $stmt->fetchAll();
  }

  /**
   * Get a single workout by ID
   */
  public function getWorkoutById(int $workoutId): ?array {
    $stmt = $this->db->prepare("
      SELECT w.id, w.trainer_id, w.name, w.description, w.difficulty_level, w.duration_minutes, w.exercises, w.created_at, w.updated_at,
             u.full_name as trainer_name
      FROM workouts w
      LEFT JOIN users u ON w.trainer_id = u.id
      WHERE w.id = :id
    ");
    
    $stmt->execute([':id' => $workoutId]);
    $result = $stmt->fetch();
    
    return $result ?: null;
  }

  /**
   * Update an existing workout
   */
  public function updateWorkout(int $workoutId, int $trainerId, array $data): bool {
    // Verify the workout belongs to this trainer
    $stmt = $this->db->prepare("SELECT id FROM workouts WHERE id = :id AND trainer_id = :trainer_id");
    $stmt->execute([':id' => $workoutId, ':trainer_id' => $trainerId]);
    
    if (!$stmt->fetch()) {
      return false; // Workout doesn't exist or doesn't belong to this trainer
    }

    $stmt = $this->db->prepare("
      UPDATE workouts
      SET name = :name, description = :description, difficulty_level = :difficulty_level, 
          duration_minutes = :duration_minutes, exercises = :exercises
      WHERE id = :id AND trainer_id = :trainer_id
    ");
    
    return $stmt->execute([
      ':name' => $data['name'],
      ':description' => $data['description'],
      ':difficulty_level' => $data['difficulty_level'],
      ':duration_minutes' => $data['duration_minutes'],
      ':exercises' => $data['exercises'],
      ':id' => $workoutId,
      ':trainer_id' => $trainerId
    ]);
  }

  /**
   * Delete a workout
   */
  public function deleteWorkout(int $workoutId, int $trainerId): bool {
    $stmt = $this->db->prepare("DELETE FROM workouts WHERE id = :id AND trainer_id = :trainer_id");
    
    return $stmt->execute([':id' => $workoutId, ':trainer_id' => $trainerId]);
  }

  /**
   * Get members assigned to a workout
   */
  public function getWorkoutAssignments(int $workoutId): array {
    $stmt = $this->db->prepare("
      SELECT wa.*, u.full_name, u.email, u.phone, m.start_date, m.end_date, mp.name as plan_name
      FROM workout_assignments wa
      JOIN users u ON wa.member_id = u.id
      LEFT JOIN memberships m ON u.id = m.member_id AND m.status = 'active'
      LEFT JOIN membership_plans mp ON m.plan_id = mp.id
      WHERE wa.workout_id = :workout_id
      ORDER BY u.full_name
    ");
    $stmt->execute([':workout_id' => $workoutId]);
    return $stmt->fetchAll();
  }

  /**
   * Assign workout to specific members
   */
  public function assignWorkoutToMembers(int $workoutId, array $memberIds): bool {
    if (empty($memberIds)) return true;
    
    // Get the trainer ID from the workout
    $trainerStmt = $this->db->prepare("SELECT trainer_id FROM workouts WHERE id = :workout_id");
    $trainerStmt->execute([':workout_id' => $workoutId]);
    $workout = $trainerStmt->fetch();
    
    if (!$workout) return false;
    
    $trainerId = $workout['trainer_id'];
    
    // First, clear existing assignments for this workout
    $stmt = $this->db->prepare("DELETE FROM workout_assignments WHERE workout_id = :workout_id");
    $stmt->execute([':workout_id' => $workoutId]);
    
    // Insert new assignments
    $stmt = $this->db->prepare("
      INSERT INTO workout_assignments (workout_id, member_id, assigned_by, assigned_date)
      VALUES (:workout_id, :member_id, :assigned_by, CURDATE())
    ");
    
    foreach ($memberIds as $memberId) {
      $stmt->execute([
        ':workout_id' => $workoutId,
        ':member_id' => (int)$memberId,
        ':assigned_by' => $trainerId
      ]);
    }
    
    return true;
  }
}
