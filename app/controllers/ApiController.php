<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/helpers/auth.php';
require_once dirname(__DIR__).'/models/UserModel.php';
require_once dirname(__DIR__).'/models/ProfileModel.php';

class ApiController {
  
  private function json($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
  }

  // Profile API
  public function getProfile(): void {
    ensure_logged_in();
    $userId = $_SESSION['auth']['id'];
    $model = new ProfileModel();
    $profile = $model->getProfile($userId);
    
    if ($profile) {
      $this->json(['success' => true, 'data' => $profile]);
    } else {
      $this->json(['success' => false, 'message' => 'Profile not found'], 404);
    }
  }

  public function updateProfile(): void {
    ensure_logged_in();
    $userId = $_SESSION['auth']['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    
    $model = new ProfileModel();
    $result = $model->updateProfile($userId, $data);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to update profile'], 400);
    }
  }

  // User Management API (Admin)
  public function getUsers(): void {
    ensure_role('admin');
    $model = new UserModel();
    $users = $model->getAllUsers();
    $this->json(['success' => true, 'data' => $users]);
  }

  public function getUserById(int $userId): void {
    ensure_role('admin');
    $model = new UserModel();
    $user = $model->findById($userId);
    
    if ($user) {
      // Don't send password hash to client
      unset($user['password_hash']);
      $this->json(['success' => true, 'data' => $user]);
    } else {
      $this->json(['success' => false, 'message' => 'User not found'], 404);
    }
  }

  public function createUser(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (empty($data['full_name'])) {
      $this->json(['success' => false, 'message' => 'Full name is required'], 400);
      return;
    }
    
    if (empty($data['email'])) {
      $this->json(['success' => false, 'message' => 'Email is required'], 400);
      return;
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $this->json(['success' => false, 'message' => 'Invalid email format'], 400);
      return;
    }
    
    if (empty($data['password'])) {
      $this->json(['success' => false, 'message' => 'Password is required'], 400);
      return;
    }
    
    if (strlen($data['password']) < 4) {
      $this->json(['success' => false, 'message' => 'Password must be at least 4 characters'], 400);
      return;
    }
    
    // Check for duplicate email
    $model = new UserModel();
    $existing = $model->findByEmail($data['email']);
    if ($existing) {
      $this->json(['success' => false, 'message' => 'Email already exists'], 400);
      return;
    }
    
    $userId = $model->createUser(
      $data['role'] ?? 'member',
      $data['full_name'],
      $data['email'],
      $data['phone'] ?? '',
      $data['password']
    );
    
    if ($userId) {
      $this->json(['success' => true, 'message' => 'User created successfully', 'id' => $userId]);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to create user'], 500);
    }
  }

  public function updateUser(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = (int)($data['id'] ?? 0);
    
    if ($userId <= 0) {
      $this->json(['success' => false, 'message' => 'Invalid user ID'], 400);
      return;
    }
    
    // Validation
    if (empty($data['full_name'])) {
      $this->json(['success' => false, 'message' => 'Full name is required'], 400);
      return;
    }
    
    if (empty($data['email'])) {
      $this->json(['success' => false, 'message' => 'Email is required'], 400);
      return;
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $this->json(['success' => false, 'message' => 'Invalid email format'], 400);
      return;
    }
    
    // Check for duplicate email (excluding current user)
    $model = new UserModel();
    $existing = $model->findByEmail($data['email']);
    if ($existing && $existing['id'] != $userId) {
      $this->json(['success' => false, 'message' => 'Email already exists'], 400);
      return;
    }
    
    // Handle password update if provided
    if (!empty($data['password'])) {
      if (strlen($data['password']) < 4) {
        $this->json(['success' => false, 'message' => 'Password must be at least 4 characters'], 400);
        return;
      }
      $model->updatePassword($userId, $data['password']);
    }
    
    $result = $model->updateUser($userId, $data);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'User updated successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to update user'], 500);
    }
  }

  public function toggleUserStatus(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = (int)($data['id'] ?? 0);
    $isActive = (int)($data['is_active'] ?? 1);
    
    if ($userId <= 0) {
      $this->json(['success' => false, 'message' => 'Invalid user ID'], 400);
      return;
    }
    
    $model = new UserModel();
    $result = $model->toggleStatus($userId, $isActive);
    
    if ($result) {
      $statusText = $isActive ? 'activated' : 'deactivated';
      $this->json(['success' => true, 'message' => "User $statusText successfully"]);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to update status'], 500);
    }
  }

  public function deleteUser(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = (int)($data['id'] ?? 0);
    
    if ($userId <= 0) {
      $this->json(['success' => false, 'message' => 'Invalid user ID'], 400);
      return;
    }
    
    // Prevent deleting yourself
    if ($userId === $_SESSION['auth']['id']) {
      $this->json(['success' => false, 'message' => 'Cannot delete your own account'], 400);
      return;
    }
    
    $model = new UserModel();
    $result = $model->deleteUser($userId);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'User deleted successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to delete user'], 500);
    }
  }

  // Membership Plans API (Admin)
  public function getPlans(): void {
    ensure_logged_in();
    require_once dirname(__DIR__).'/models/MembershipPlanModel.php';
    $model = new MembershipPlanModel();
    
    // Admins see all plans, others see only active plans
    if ($_SESSION['auth']['role'] === 'admin') {
      $plans = $model->getAllPlans();
    } else {
      $plans = $model->getActivePlans();
    }
    
    $this->json(['success' => true, 'data' => $plans]);
  }

  public function getPlanById(int $planId): void {
    ensure_logged_in();
    require_once dirname(__DIR__).'/models/MembershipPlanModel.php';
    $model = new MembershipPlanModel();
    $plan = $model->getPlanById($planId);
    
    if ($plan) {
      $this->json(['success' => true, 'data' => $plan]);
    } else {
      $this->json(['success' => false, 'message' => 'Plan not found'], 404);
    }
  }

  public function createPlan(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (empty($data['name'])) {
      $this->json(['success' => false, 'message' => 'Plan name is required'], 400);
      return;
    }
    if (empty($data['duration_months']) || $data['duration_months'] < 1) {
      $this->json(['success' => false, 'message' => 'Valid duration is required'], 400);
      return;
    }
    if (empty($data['price']) || $data['price'] < 0) {
      $this->json(['success' => false, 'message' => 'Valid price is required'], 400);
      return;
    }
    
    require_once dirname(__DIR__).'/models/MembershipPlanModel.php';
    $model = new MembershipPlanModel();
    $planId = $model->createPlan($data);
    
    if ($planId) {
      $this->json(['success' => true, 'message' => 'Plan created successfully', 'id' => $planId]);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to create plan'], 500);
    }
  }

  public function updatePlan(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    $planId = (int)($data['id'] ?? 0);
    
    if ($planId <= 0) {
      $this->json(['success' => false, 'message' => 'Invalid plan ID'], 400);
      return;
    }
    
    require_once dirname(__DIR__).'/models/MembershipPlanModel.php';
    $model = new MembershipPlanModel();
    $result = $model->updatePlan($planId, $data);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'Plan updated successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to update plan'], 500);
    }
  }

  public function deletePlan(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    $planId = (int)($data['id'] ?? 0);
    
    if ($planId <= 0) {
      $this->json(['success' => false, 'message' => 'Invalid plan ID'], 400);
      return;
    }
    
    require_once dirname(__DIR__).'/models/MembershipPlanModel.php';
    $model = new MembershipPlanModel();
    $result = $model->deletePlan($planId);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'Plan deleted successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to delete plan'], 500);
    }
  }

  // Payments API (Admin)
  public function getPayments(): void {
    require_once dirname(__DIR__).'/models/PaymentModel.php';
    $model = new PaymentModel();
    
    // Check if user is admin or member
    $role = $_SESSION['auth']['role'] ?? '';
    
    if ($role === 'admin') {
      // Admin sees all payments
      $payments = $model->getAllPayments();
    } elseif ($role === 'member') {
      // Member sees only their own payments
      $memberId = $_SESSION['auth']['id'];
      $payments = $model->getMemberPayments($memberId);
    } else {
      $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
      return;
    }
    
    $this->json(['success' => true, 'data' => $payments]);
  }

  public function getPaymentStats(): void {
    ensure_role('admin');
    require_once dirname(__DIR__).'/models/PaymentModel.php';
    $model = new PaymentModel();
    $stats = $model->getPaymentStats();
    $this->json(['success' => true, 'data' => $stats]);
  }

  public function getMemberDues(): void {
    ensure_role('admin');
    require_once dirname(__DIR__).'/models/PaymentModel.php';
    $model = new PaymentModel();
    $dues = $model->getMemberDues();
    $this->json(['success' => true, 'data' => $dues]);
  }

  // Workouts API
  public function getWorkouts(): void {
    ensure_logged_in();
    require_once dirname(__DIR__).'/models/WorkoutModel.php';
    
    $role = $_SESSION['auth']['role'];
    $model = new WorkoutModel();
    
    if ($role === 'trainer') {
      $trainerId = $_SESSION['auth']['id'];
      $workouts = $model->getWorkoutsByTrainer($trainerId);
    } else {
      $workouts = $model->getAllWorkouts();
    }
    
    $this->json(['success' => true, 'data' => $workouts]);
  }

  public function createWorkout(): void {
    ensure_role('trainer');
    require_once dirname(__DIR__).'/models/WorkoutModel.php';
    
    $data = json_decode(file_get_contents('php://input'), true);
    $trainerId = $_SESSION['auth']['id'];
    
    // Validation
    if (empty($data['name'])) {
      $this->json(['success' => false, 'message' => 'Workout name is required'], 400);
      return;
    }
    
    $name = $data['name'];
    $description = $data['description'] ?? '';
    $difficultyLevel = $data['difficulty_level'] ?? 'beginner';
    $durationMinutes = isset($data['duration_minutes']) ? (int)$data['duration_minutes'] : null;
    $exercises = $data['exercises'] ?? '';
    
    // Validate difficulty level
    if (!in_array($difficultyLevel, ['beginner', 'intermediate', 'advanced'])) {
      $this->json(['success' => false, 'message' => 'Invalid difficulty level'], 400);
      return;
    }
    
    $model = new WorkoutModel();
    $workoutId = $model->createWorkout($trainerId, $name, $description, $difficultyLevel, $durationMinutes, $exercises);
    
    if ($workoutId) {
      // Assign workout to members if provided
      if (!empty($data['member_ids']) && is_array($data['member_ids'])) {
        $model->assignWorkoutToMembers($workoutId, $data['member_ids']);
      }
      
      $this->json(['success' => true, 'message' => 'Workout created successfully', 'id' => $workoutId]);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to create workout'], 500);
    }
  }

  public function deleteWorkout(): void {
    ensure_role('trainer');
    require_once dirname(__DIR__).'/models/WorkoutModel.php';
    
    $data = json_decode(file_get_contents('php://input'), true);
    $workoutId = (int)($data['id'] ?? 0);
    $trainerId = $_SESSION['auth']['id'];
    
    if ($workoutId <= 0) {
      $this->json(['success' => false, 'message' => 'Invalid workout ID'], 400);
      return;
    }
    
    $model = new WorkoutModel();
    $result = $model->deleteWorkout($workoutId, $trainerId);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'Workout deleted successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to delete workout or workout not found'], 500);
    }
  }

  public function getWorkoutAssignments(int $workoutId): void {
    ensure_logged_in();
    require_once dirname(__DIR__).'/models/WorkoutModel.php';
    
    $model = new WorkoutModel();
    $assignments = $model->getWorkoutAssignments($workoutId);
    
    $this->json(['success' => true, 'data' => $assignments]);
  }

  // Member Membership API
  public function getMemberMembership(): void {
    ensure_role('member');
    require_once dirname(__DIR__).'/models/MembershipModel.php';
    
    $memberId = $_SESSION['auth']['id'];
    $model = new MembershipModel();
    $membership = $model->getMemberActiveMembership($memberId);
    
    $this->json(['success' => true, 'data' => $membership]);
  }

  public function createPayment(): void {
    ensure_role('member');
    require_once dirname(__DIR__).'/models/PaymentModel.php';
    require_once dirname(__DIR__).'/models/MembershipModel.php';
    require_once dirname(__DIR__).'/models/MembershipPlanModel.php';
    
    $data = json_decode(file_get_contents('php://input'), true);
    $memberId = $_SESSION['auth']['id'];
    $planId = (int)($data['plan_id'] ?? 0);
    $paymentMethod = $data['payment_method'] ?? '';
    $transactionId = $data['transaction_id'] ?? null;
    $notes = $data['notes'] ?? null;
    
    // Validate inputs
    if ($planId <= 0) {
      $this->json(['success' => false, 'message' => 'Please select a membership plan'], 400);
      return;
    }
    
    if (!in_array($paymentMethod, ['cash', 'card', 'online', 'bank_transfer'])) {
      $this->json(['success' => false, 'message' => 'Invalid payment method'], 400);
      return;
    }
    
    // Get plan details
    $planModel = new MembershipPlanModel();
    $plan = $planModel->getPlanById($planId);
    
    if (!$plan) {
      $this->json(['success' => false, 'message' => 'Plan not found'], 404);
      return;
    }
    
    try {
      // Create or extend membership
      $membershipModel = new MembershipModel();
      $membershipId = $membershipModel->createOrExtendMembership($memberId, $planId);
      
      // Create payment record
      $paymentModel = new PaymentModel();
      $paymentId = $paymentModel->createPayment([
        'membership_id' => $membershipId,
        'user_id' => $memberId,
        'amount' => $plan['price'],
        'payment_method' => $paymentMethod,
        'transaction_id' => $transactionId,
        'status' => 'completed',
        'notes' => $notes
      ]);
      
      $this->json([
        'success' => true, 
        'message' => 'Payment successful! Your membership has been activated.',
        'payment_id' => $paymentId,
        'membership_id' => $membershipId
      ]);
    } catch (Exception $e) {
      $this->json(['success' => false, 'message' => 'Payment failed: ' . $e->getMessage()], 500);
    }
  }

  // Trainer Members API
  public function getTrainerMembers(): void {
    ensure_role('trainer');
    require_once dirname(__DIR__).'/models/MembershipModel.php';
    
    $trainerId = $_SESSION['auth']['id'];
    $model = new MembershipModel();
    $members = $model->getTrainerMembers($trainerId);
    
    $this->json(['success' => true, 'data' => $members]);
  }

  // Schedule API
  public function getSchedule(): void {
    ensure_role('trainer');
    require_once dirname(__DIR__).'/models/ScheduleModel.php';
    
    $trainerId = $_SESSION['auth']['id'];
    $model = new ScheduleModel();
    $schedule = $model->getTrainerSchedules($trainerId);
    
    $this->json(['success' => true, 'data' => $schedule]);
  }

  // Membership Details API
  public function getMembership(): void {
    ensure_role('member');
    $userId = $_SESSION['auth']['id'];
    
    // Mock data - replace with actual database queries
    $membership = [
      'plan' => 'Premium',
      'start_date' => '2026-01-01',
      'end_date' => '2026-02-01',
      'status' => 'active',
      'trainer' => 'Mike Johnson'
    ];
    
    $this->json(['success' => true, 'data' => $membership]);
  }

  // Change Password API
  public function changePassword(): void {
    ensure_logged_in();
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['auth']['id'];
    
    $currentPassword = $data['current_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    
    if ($newPassword !== $confirmPassword) {
      $this->json(['success' => false, 'message' => 'Passwords do not match'], 400);
      return;
    }
    
    if (strlen($newPassword) < 4) {
      $this->json(['success' => false, 'message' => 'Password must be at least 4 characters'], 400);
      return;
    }
    
    $model = new UserModel();
    $user = $model->findById($userId);
    
    if (!$user || $user['password_hash'] !== $currentPassword) {
      $this->json(['success' => false, 'message' => 'Current password is incorrect'], 400);
      return;
    }
    
    $result = $model->updatePassword($userId, $newPassword);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'Password changed successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to change password'], 400);
    }
  }

  // Schedule Management API (Admin)
  public function getSchedules(): void {
    ensure_logged_in();
    require_once dirname(__DIR__).'/models/ScheduleModel.php';
    $model = new ScheduleModel();
    $schedules = $model->getAllSchedules();
    $this->json(['success' => true, 'data' => $schedules]);
  }

  public function createSchedule(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['trainer_id']) || empty($data['day_of_week']) || empty($data['start_time']) || empty($data['end_time'])) {
      $this->json(['success' => false, 'message' => 'All fields are required'], 400);
      return;
    }
    
    require_once dirname(__DIR__).'/models/ScheduleModel.php';
    $model = new ScheduleModel();
    $scheduleId = $model->createSchedule($data);
    
    if ($scheduleId) {
      $this->json(['success' => true, 'message' => 'Schedule created successfully', 'id' => $scheduleId]);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to create schedule'], 500);
    }
  }

  public function deleteSchedule(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    $scheduleId = (int)($data['id'] ?? 0);
    
    if ($scheduleId <= 0) {
      $this->json(['success' => false, 'message' => 'Invalid schedule ID'], 400);
      return;
    }
    
    require_once dirname(__DIR__).'/models/ScheduleModel.php';
    $model = new ScheduleModel();
    $result = $model->deleteSchedule($scheduleId);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'Schedule deleted successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to delete schedule'], 500);
    }
  }

  public function getMemberTrainer(int $memberId): void {
    ensure_role('admin');
    require_once dirname(__DIR__).'/models/MembershipModel.php';
    
    $model = new MembershipModel();
    $trainer = $model->getMemberTrainer($memberId);
    
    $this->json(['success' => true, 'data' => $trainer]);
  }

  // Membership Assignment API (Admin)
  public function assignMemberToTrainer(): void {
    ensure_role('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    $memberId = (int)($data['member_id'] ?? 0);
    $trainerId = (int)($data['trainer_id'] ?? 0);
    
    if ($memberId <= 0 || $trainerId <= 0) {
      $this->json(['success' => false, 'message' => 'Valid member and trainer IDs required'], 400);
      return;
    }
    
    require_once dirname(__DIR__).'/models/MembershipModel.php';
    $model = new MembershipModel();
    $result = $model->assignTrainerToMember($memberId, $trainerId);
    
    if ($result) {
      $this->json(['success' => true, 'message' => 'Member assigned to trainer successfully']);
    } else {
      $this->json(['success' => false, 'message' => 'Failed to assign member'], 500);
    }
  }

  public function getAssignedMembers(int $trainerId): void {
    ensure_logged_in();
    require_once dirname(__DIR__).'/models/MembershipModel.php';
    $model = new MembershipModel();
    $members = $model->getTrainerMembers($trainerId);
    $this->json(['success' => true, 'data' => $members]);
  }
}
