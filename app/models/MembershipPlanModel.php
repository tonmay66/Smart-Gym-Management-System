<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class MembershipPlanModel {
  private PDO $db;
  public function __construct(){ $this->db=db_connect(); }

  public function getAllPlans(): array {
    $st=$this->db->query("SELECT * FROM membership_plans ORDER BY created_at DESC");
    return $st->fetchAll();
  }

  public function getActivePlans(): array {
    $st=$this->db->query("SELECT * FROM membership_plans WHERE is_active=1 ORDER BY price ASC");
    return $st->fetchAll();
  }

  public function getPlanById(int $id): ?array {
    $st=$this->db->prepare("SELECT * FROM membership_plans WHERE id=:id LIMIT 1");
    $st->execute([':id'=>$id]);
    $plan=$st->fetch();
    return $plan ?: null;
  }

  public function createPlan(array $data): int {
    $st=$this->db->prepare("
      INSERT INTO membership_plans(name, description, duration_months, price, features, is_active) 
      VALUES(:name, :desc, :duration, :price, :features, :active)
    ");
    $st->execute([
      ':name'=>$data['name'],
      ':desc'=>$data['description'] ?? '',
      ':duration'=>(int)$data['duration_months'],
      ':price'=>(float)$data['price'],
      ':features'=>$data['features'] ?? '',
      ':active'=>isset($data['is_active']) ? (int)$data['is_active'] : 1
    ]);
    return (int)$this->db->lastInsertId();
  }

  public function updatePlan(int $id, array $data): bool {
    $fields = [];
    $params = [':id'=>$id];
    
    if (isset($data['name'])) {
      $fields[] = "name=:name";
      $params[':name'] = $data['name'];
    }
    if (isset($data['description'])) {
      $fields[] = "description=:desc";
      $params[':desc'] = $data['description'];
    }
    if (isset($data['duration_months'])) {
      $fields[] = "duration_months=:duration";
      $params[':duration'] = (int)$data['duration_months'];
    }
    if (isset($data['price'])) {
      $fields[] = "price=:price";
      $params[':price'] = (float)$data['price'];
    }
    if (isset($data['features'])) {
      $fields[] = "features=:features";
      $params[':features'] = $data['features'];
    }
    if (isset($data['is_active'])) {
      $fields[] = "is_active=:active";
      $params[':active'] = (int)$data['is_active'];
    }
    
    if (empty($fields)) return false;
    
    $sql = "UPDATE membership_plans SET " . implode(', ', $fields) . " WHERE id=:id";
    $st=$this->db->prepare($sql);
    return $st->execute($params);
  }

  public function deletePlan(int $id): bool {
    $st=$this->db->prepare("DELETE FROM membership_plans WHERE id=:id");
    return $st->execute([':id'=>$id]);
  }

  public function toggleStatus(int $id, int $isActive): bool {
    $st=$this->db->prepare("UPDATE membership_plans SET is_active=:active WHERE id=:id");
    return $st->execute([':id'=>$id, ':active'=>$isActive]);
  }
}
