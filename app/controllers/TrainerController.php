<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/helpers/auth.php';

class TrainerController {
  public function membersPage(): void {
    ensure_role('trainer');
    require dirname(__DIR__).'/views/trainer/members.php';
  }
  
  public function workoutsPage(): void {
    ensure_role('trainer');
    require dirname(__DIR__).'/views/trainer/workouts.php';
  }
  
  public function schedulePage(): void {
    ensure_role('trainer');
    require dirname(__DIR__).'/views/trainer/schedule.php';
  }
}
