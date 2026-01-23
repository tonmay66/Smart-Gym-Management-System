<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/helpers/auth.php';

class MemberController {
  public function profilePage(): void {
    ensure_logged_in();
    require dirname(__DIR__).'/views/member/profile.php';
  }
  
  public function changePasswordPage(): void {
    ensure_logged_in();
    require dirname(__DIR__).'/views/member/change_password.php';
  }
  
  public function membershipPage(): void {
    ensure_role('member');
    require dirname(__DIR__).'/views/member/membership.php';
  }
  
  public function workoutsPage(): void {
    ensure_role('member');
    require dirname(__DIR__).'/views/member/workouts.php';
  }
  
  public function paymentsPage(): void {
    ensure_role('member');
    require dirname(__DIR__).'/views/member/payments.php';
  }
}
