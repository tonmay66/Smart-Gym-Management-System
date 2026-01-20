<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class PasswordModel {
  private PDO $db;
  public function __construct(){ $this->db = db_connect(); }

  public function getPasswordByUserId(int $userId): ?string {
    $st = $this->db->prepare("SELECT password_hash FROM users WHERE id=:id LIMIT 1");
    $st->execute([':id'=>$userId]);
    $row = $st->fetch();
    return $row ? (string)$row['password_hash'] : null;
  }

  public function updatePassword(int $userId, string $newPlainPassword): void {
    $st = $this->db->prepare("UPDATE users SET password_hash=:p WHERE id=:id");
    $st->execute([':p'=>$newPlainPassword, ':id'=>$userId]);
  }
}
