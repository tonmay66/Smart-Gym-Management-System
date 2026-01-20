<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db_connect.php';

class ScheduleModel {
  private PDO $db;
  public function __construct(){ $this->db=db_connect(); }

  public function getAllSchedules(): array {
    $st=$this->db->query("
      SELECT s.*, u.full_name as trainer_name
      FROM trainer_schedules s
      JOIN users u ON s.trainer_id = u.id
      ORDER BY s.day_of_week, s.start_time
    ");
    return $st->fetchAll();
  }

  public function getTrainerSchedules(int $trainerId): array {
    $st=$this->db->prepare("
      SELECT * FROM trainer_schedules 
      WHERE trainer_id = :trainer_id 
      ORDER BY day_of_week, start_time
    ");
    $st->execute([':trainer_id'=>$trainerId]);
    return $st->fetchAll();
  }

  public function getScheduleById(int $id): ?array {
    $st=$this->db->prepare("SELECT * FROM trainer_schedules WHERE id=:id LIMIT 1");
    $st->execute([':id'=>$id]);
    $schedule=$st->fetch();
    return $schedule ?: null;
  }

  public function createSchedule(array $data): int {
    $st=$this->db->prepare("
      INSERT INTO trainer_schedules(trainer_id, day_of_week, start_time, end_time, activity, location, max_capacity, is_active) 
      VALUES(:trainer_id, :day, :start, :end, :activity, :location, :capacity, :active)
    ");
    $st->execute([
      ':trainer_id'=>(int)$data['trainer_id'],
      ':day'=>$data['day_of_week'],
      ':start'=>$data['start_time'],
      ':end'=>$data['end_time'],
      ':activity'=>$data['activity'],
      ':location'=>$data['location'] ?? '',
      ':capacity'=>isset($data['max_capacity']) ? (int)$data['max_capacity'] : 1,
      ':active'=>isset($data['is_active']) ? (int)$data['is_active'] : 1
    ]);
    return (int)$this->db->lastInsertId();
  }

  public function updateSchedule(int $id, array $data): bool {
    $fields = [];
    $params = [':id'=>$id];
    
    if (isset($data['day_of_week'])) {
      $fields[] = "day_of_week=:day";
      $params[':day'] = $data['day_of_week'];
    }
    if (isset($data['start_time'])) {
      $fields[] = "start_time=:start";
      $params[':start'] = $data['start_time'];
    }
    if (isset($data['end_time'])) {
      $fields[] = "end_time=:end";
      $params[':end'] = $data['end_time'];
    }
    if (isset($data['activity'])) {
      $fields[] = "activity=:activity";
      $params[':activity'] = $data['activity'];
    }
    if (isset($data['location'])) {
      $fields[] = "location=:location";
      $params[':location'] = $data['location'];
    }
    if (isset($data['max_capacity'])) {
      $fields[] = "max_capacity=:capacity";
      $params[':capacity'] = (int)$data['max_capacity'];
    }
    if (isset($data['is_active'])) {
      $fields[] = "is_active=:active";
      $params[':active'] = (int)$data['is_active'];
    }
    
    if (empty($fields)) return false;
    
    $sql = "UPDATE trainer_schedules SET " . implode(', ', $fields) . " WHERE id=:id";
    $st=$this->db->prepare($sql);
    return $st->execute($params);
  }

  public function deleteSchedule(int $id): bool {
    $st=$this->db->prepare("DELETE FROM trainer_schedules WHERE id=:id");
    return $st->execute([':id'=>$id]);
  }
}
