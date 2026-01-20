<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class PaymentModel {
  private PDO $db;
  public function __construct(){ $this->db=db_connect(); }

  public function getAllPayments(): array {
    $st=$this->db->query("
      SELECT p.*, u.full_name, u.email, mp.name as plan_name, m.start_date, m.end_date
      FROM payments p
      JOIN users u ON p.user_id = u.id
      JOIN memberships m ON p.membership_id = m.id
      JOIN membership_plans mp ON m.plan_id = mp.id
      ORDER BY p.payment_date DESC
    ");
    return $st->fetchAll();
  }

  public function getMemberPayments(int $memberId): array {
    $st=$this->db->prepare("
      SELECT p.*, mp.name as plan_name, m.start_date, m.end_date
      FROM payments p
      JOIN memberships m ON p.membership_id = m.id
      JOIN membership_plans mp ON m.plan_id = mp.id
      WHERE p.user_id = :member_id
      ORDER BY p.payment_date DESC
    ");
    $st->execute([':member_id'=>$memberId]);
    return $st->fetchAll();
  }

  public function getPaymentStats(): array {
    $st=$this->db->query("
      SELECT 
        COUNT(*) as total_payments,
        SUM(amount) as total_revenue,
        SUM(CASE WHEN status='completed' THEN amount ELSE 0 END) as completed_revenue,
        SUM(CASE WHEN status='pending' THEN amount ELSE 0 END) as pending_revenue
      FROM payments
    ");
    return $st->fetch() ?: [];
  }

  public function getMemberDues(): array {
    $st=$this->db->query("
      SELECT 
        u.id,
        u.full_name,
        u.email,
        mp.price as plan_price,
        mp.name as plan_name,
        m.start_date,
        m.end_date,
        m.status as membership_status,
        COALESCE(SUM(p.amount), 0) as total_paid,
        (mp.price - COALESCE(SUM(p.amount), 0)) as dues
      FROM users u
      JOIN memberships m ON u.id = m.member_id
      JOIN membership_plans mp ON m.plan_id = mp.id
      LEFT JOIN payments p ON m.id = p.membership_id AND p.status='completed'
      WHERE u.role = 'member'
      GROUP BY u.id, mp.price, mp.name, m.start_date, m.end_date, m.status
      HAVING dues > 0
      ORDER BY dues DESC
    ");
    return $st->fetchAll();
  }

  public function createPayment(array $data): int {
    $st=$this->db->prepare("
      INSERT INTO payments(membership_id, user_id, amount, payment_method, transaction_id, status, notes) 
      VALUES(:membership_id, :user_id, :amount, :method, :txn_id, :status, :notes)
    ");
    $st->execute([
      ':membership_id'=>(int)$data['membership_id'],
      ':user_id'=>(int)$data['user_id'],
      ':amount'=>(float)$data['amount'],
      ':method'=>$data['payment_method'],
      ':txn_id'=>$data['transaction_id'] ?? null,
      ':status'=>$data['status'] ?? 'completed',
      ':notes'=>$data['notes'] ?? null
    ]);
    return (int)$this->db->lastInsertId();
  }
}
