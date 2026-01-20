<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class MembershipModel {
  private PDO $db;
  public function __construct(){ $this->db=db_connect(); }

  public function getAllMemberships(): array {
    $st=$this->db->query("
      SELECT m.*, u.full_name as member_name, u.email, mp.name as plan_name, mp.price,
             t.full_name as trainer_name
      FROM memberships m
      JOIN users u ON m.member_id = u.id
      JOIN membership_plans mp ON m.plan_id = mp.id
      LEFT JOIN users t ON m.trainer_id = t.id
      ORDER BY m.created_at DESC
    ");
    return $st->fetchAll();
  }

  public function getMembershipById(int $id): ?array {
    $st=$this->db->prepare("
      SELECT m.*, u.full_name as member_name, mp.name as plan_name
      FROM memberships m
      JOIN users u ON m.member_id = u.id
      JOIN membership_plans mp ON m.plan_id = mp.id
      WHERE m.id = :id
    ");
    $st->execute([':id'=>$id]);
    $membership=$st->fetch();
    return $membership ?: null;
  }

  public function assignTrainerToMember(int $memberId, int $trainerId): bool {
    // First, check if member has an active membership
    $checkStmt = $this->db->prepare("
      SELECT id FROM memberships WHERE member_id = :member_id AND status = 'active' LIMIT 1
    ");
    $checkStmt->execute([':member_id' => $memberId]);
    $existing = $checkStmt->fetch();
    
    if ($existing) {
      // Update existing membership
      $stmt = $this->db->prepare("
        UPDATE memberships SET trainer_id = :trainer_id WHERE member_id = :member_id AND status = 'active'
      ");
      return $stmt->execute([':member_id' => $memberId, ':trainer_id' => $trainerId]);
    } else {
      // Create a basic membership record for trainer assignment
      // Get a default plan (first active plan) or create without plan
      $planStmt = $this->db->prepare("SELECT id FROM membership_plans WHERE is_active = 1 LIMIT 1");
      $planStmt->execute();
      $plan = $planStmt->fetch();
      
      if (!$plan) {
        // No plans available, cannot create membership
        return false;
      }
      
      $stmt = $this->db->prepare("
        INSERT INTO memberships (member_id, plan_id, trainer_id, start_date, end_date, status)
        VALUES (:member_id, :plan_id, :trainer_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'active')
      ");
      return $stmt->execute([
        ':member_id' => $memberId,
        ':plan_id' => $plan['id'],
        ':trainer_id' => $trainerId
      ]);
    }
  }

  public function getTrainerMembers(int $trainerId): array {
    $stmt=$this->db->prepare("
      SELECT u.id, u.full_name, u.email, u.phone, 
             m.start_date, m.end_date, mp.name as plan_name
      FROM memberships m
      JOIN users u ON m.member_id = u.id
      JOIN membership_plans mp ON m.plan_id = mp.id
      WHERE m.trainer_id = :trainer_id AND m.status = 'active'
      ORDER BY u.full_name
    ");
    $stmt->execute([':trainer_id'=>$trainerId]);
    return $stmt->fetchAll();
  }

  public function getMemberTrainer(int $memberId): ?array {
    $stmt = $this->db->prepare("
      SELECT m.trainer_id, u.full_name as trainer_name
      FROM memberships m
      JOIN users u ON m.trainer_id = u.id
      WHERE m.member_id = :member_id AND m.status = 'active'
      LIMIT 1
    ");
    $stmt->execute([':member_id'=>$memberId]);
    $trainer=$stmt->fetch();
    return $trainer ?: null;
  }

  public function createMembership(array $data): int {
    $st=$this->db->prepare("
      INSERT INTO memberships(member_id, plan_id, trainer_id, start_date, end_date, status) 
      VALUES(:member_id, :plan_id, :trainer_id, :start_date, :end_date, :status)
    ");
    $st->execute([
      ':member_id'=>(int)$data['member_id'],
      ':plan_id'=>(int)$data['plan_id'],
      ':trainer_id'=>isset($data['trainer_id']) ? (int)$data['trainer_id'] : null,
      ':start_date'=>$data['start_date'],
      ':end_date'=>$data['end_date'],
      ':status'=>$data['status'] ?? 'active'
    ]);
    return (int)$this->db->lastInsertId();
  }

  public function createOrExtendMembership(int $memberId, int $planId, ?int $trainerId = null): int {
    // Get plan details
    $planStmt = $this->db->prepare("SELECT duration_months FROM membership_plans WHERE id = :plan_id");
    $planStmt->execute([':plan_id' => $planId]);
    $plan = $planStmt->fetch();
    
    if (!$plan) {
      throw new Exception("Plan not found");
    }
    
    // Check for existing active membership
    $checkStmt = $this->db->prepare("
      SELECT id, end_date FROM memberships 
      WHERE member_id = :member_id AND status = 'active' 
      ORDER BY end_date DESC LIMIT 1
    ");
    $checkStmt->execute([':member_id' => $memberId]);
    $existing = $checkStmt->fetch();
    
    if ($existing) {
      // Extend existing membership
      $currentEndDate = new DateTime($existing['end_date']);
      $today = new DateTime();
      
      // If membership hasn't expired, extend from end date, otherwise from today
      $startDate = ($currentEndDate > $today) ? $currentEndDate : $today;
      $endDate = clone $startDate;
      $endDate->modify("+{$plan['duration_months']} months");
      
      $updateStmt = $this->db->prepare("
        UPDATE memberships 
        SET plan_id = :plan_id, 
            end_date = :end_date,
            trainer_id = :trainer_id,
            updated_at = NOW()
        WHERE id = :id
      ");
      
      $updateStmt->execute([
        ':id' => $existing['id'],
        ':plan_id' => $planId,
        ':end_date' => $endDate->format('Y-m-d'),
        ':trainer_id' => $trainerId
      ]);
      
      return $existing['id'];
    } else {
      // Create new membership
      $startDate = new DateTime();
      $endDate = clone $startDate;
      $endDate->modify("+{$plan['duration_months']} months");
      
      return $this->createMembership([
        'member_id' => $memberId,
        'plan_id' => $planId,
        'trainer_id' => $trainerId,
        'start_date' => $startDate->format('Y-m-d'),
        'end_date' => $endDate->format('Y-m-d'),
        'status' => 'active'
      ]);
    }
  }

  public function getMemberActiveMembership(int $memberId): ?array {
    $stmt = $this->db->prepare("
      SELECT m.*, mp.name as plan_name, mp.price, mp.duration_months,
             t.full_name as trainer_name, t.email as trainer_email
      FROM memberships m
      JOIN membership_plans mp ON m.plan_id = mp.id
      LEFT JOIN users t ON m.trainer_id = t.id
      WHERE m.member_id = :member_id AND m.status = 'active'
      ORDER BY m.end_date DESC
      LIMIT 1
    ");
    $stmt->execute([':member_id' => $memberId]);
    $result = $stmt->fetch();
    return $result ?: null;
  }
}
