<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class UserModel {
  private PDO $db;
  public function __construct(){ $this->db=db_connect(); }

  public function findByEmail(string $email): ?array {
    $st=$this->db->prepare("SELECT * FROM users WHERE email=:e LIMIT 1");
    $st->execute([':e'=>$email]);
    $u=$st->fetch();
    return $u ?: null;
  }

  public function createUser(string $role,string $full,string $email,string $phone,string $plainPass): int {
    $st=$this->db->prepare("INSERT INTO users(role,full_name,email,phone,password_hash,is_active,created_at) VALUES(:r,:n,:e,:p,:pw,1,NOW())");
    $st->execute([':r'=>$role,':n'=>$full,':e'=>$email,':p'=>$phone,':pw'=>$plainPass]);
    return (int)$this->db->lastInsertId();
  }

  public function updateLastLogin(int $id): void {
    $st=$this->db->prepare("UPDATE users SET last_login_at=NOW() WHERE id=:id");
    $st->execute([':id'=>$id]);
  }

  public function getAllUsers(): array {
    $st=$this->db->query("SELECT id, role, full_name, email, phone, is_active, created_at, last_login_at FROM users ORDER BY created_at DESC");
    return $st->fetchAll();
  }

  public function findById(int $id): ?array {
    $st=$this->db->prepare("SELECT * FROM users WHERE id=:id LIMIT 1");
    $st->execute([':id'=>$id]);
    $u=$st->fetch();
    return $u ?: null;
  }

  public function updateUser(int $id, array $data): bool {
    $fields = [];
    $params = [':id'=>$id];
    
    if (isset($data['full_name'])) {
      $fields[] = "full_name=:name";
      $params[':name'] = $data['full_name'];
    }
    if (isset($data['email'])) {
      $fields[] = "email=:email";
      $params[':email'] = $data['email'];
    }
    if (isset($data['phone'])) {
      $fields[] = "phone=:phone";
      $params[':phone'] = $data['phone'];
    }
    if (isset($data['role'])) {
      $fields[] = "role=:role";
      $params[':role'] = $data['role'];
    }
    
    if (empty($fields)) return false;
    
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id=:id";
    $st=$this->db->prepare($sql);
    return $st->execute($params);
  }

  public function toggleStatus(int $id, int $isActive): bool {
    $st=$this->db->prepare("UPDATE users SET is_active=:active WHERE id=:id");
    return $st->execute([':id'=>$id, ':active'=>$isActive]);
  }

  public function updatePassword(int $id, string $newPassword): bool {
    $st=$this->db->prepare("UPDATE users SET password_hash=:pw WHERE id=:id");
    return $st->execute([':id'=>$id, ':pw'=>$newPassword]);
  }

  public function deleteUser(int $id): bool {
    $st=$this->db->prepare("DELETE FROM users WHERE id=:id");
    return $st->execute([':id'=>$id]);
  }
}
