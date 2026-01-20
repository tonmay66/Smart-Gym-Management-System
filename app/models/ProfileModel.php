<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class ProfileModel {
  private PDO $db;
  public function __construct(){ $this->db = db_connect(); }

  public function getByUserId(int $userId): ?array {
    $sql = "SELECT 
              u.id, u.role, u.full_name, u.email, u.phone,
              p.gender, p.date_of_birth, p.address, p.emergency_name, p.emergency_phone
            FROM users u
            LEFT JOIN user_profiles p ON p.user_id = u.id
            WHERE u.id = :id
            LIMIT 1";
    $st = $this->db->prepare($sql);
    $st->execute([':id'=>$userId]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public function upsert(
    int $userId,
    ?string $gender,
    ?string $dob,
    ?string $address,
    ?string $emName,
    ?string $emPhone
  ): void {
    $exists = $this->db->prepare("SELECT id FROM user_profiles WHERE user_id=:id LIMIT 1");
    $exists->execute([':id'=>$userId]);
    $has = $exists->fetchColumn();

    if ($has) {
      $sql="UPDATE user_profiles
            SET gender=:g, date_of_birth=:dob, address=:addr,
                emergency_name=:en, emergency_phone=:ep
            WHERE user_id=:id";
    } else {
      $sql="INSERT INTO user_profiles(user_id, gender, date_of_birth, address, emergency_name, emergency_phone, created_at)
            VALUES(:id, :g, :dob, :addr, :en, :ep, NOW())";
    }

    $st=$this->db->prepare($sql);
    $st->execute([
      ':id'=>$userId,
      ':g'=>$gender,
      ':dob'=>$dob,
      ':addr'=>$address,
      ':en'=>$emName,
      ':ep'=>$emPhone
    ]);
  }

  public function deleteByUserId(int $userId): void {
    $st = $this->db->prepare("DELETE FROM user_profiles WHERE user_id=:id");
    $st->execute([':id'=>$userId]);
  }

  public function getProfile(int $userId): ?array {
    return $this->getByUserId($userId);
  }

  public function updateProfile(int $userId, array $data): bool {
    $this->upsert(
      $userId,
      $data['gender'] ?? null,
      $data['date_of_birth'] ?? null,
      $data['address'] ?? null,
      $data['emergency_name'] ?? null,
      $data['emergency_phone'] ?? null
    );
    return true;
  }
}
