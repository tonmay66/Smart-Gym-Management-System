<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/helpers/auth.php';

class AdminController {
  public function usersPage(): void {
    ensure_role('admin');
    require dirname(__DIR__).'/views/admin/users.php';
  }
  
  public function plansPage(): void {
    ensure_role('admin');
    require dirname(__DIR__).'/views/admin/plans.php';
  }
  
  public function paymentsPage(): void {
    ensure_role('admin');
    require dirname(__DIR__).'/views/admin/payments.php';
  }
  
  public function schedulesPage(): void {
    ensure_role('admin');
    require dirname(__DIR__).'/views/admin/schedules.php';
  }
  
  public function assignmentsPage(): void {
    ensure_role('admin');
    require dirname(__DIR__).'/views/admin/assignments.php';
  }
}
