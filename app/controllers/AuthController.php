<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/models/UserModel.php';

class AuthController {

  public function showLogin(): void {
    $errors = [];
    $old = ['email'=>''];
    require dirname(__DIR__).'/views/auth/login.php';
  }

  public function showRegister(): void {
    $errors = [];
    $old = ['role'=>'member','full_name'=>'','email'=>'','phone'=>''];
    require dirname(__DIR__).'/views/auth/register.php';
  }

  public function register(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $role = trim($_POST['role'] ?? 'member');
    $full = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass = (string)($_POST['password'] ?? '');
    $cpass = (string)($_POST['confirm_password'] ?? '');

    $errors = []; $old = compact('role','full','email','phone');
    if (!in_array($role,['admin','trainer','member'],true)) $errors['role']="Invalid role";
    if ($full==='' || !preg_match('/^[a-zA-Z\s]{2,120}$/',$full)) $errors['full_name']="Name invalid";
    if ($email==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors['email']="Email invalid";
    if ($pass==='' || strlen($pass)<4) $errors['password']="Password min 4";
    if ($pass!==$cpass) $errors['confirm_password']="Passwords do not match";

    $m = new UserModel();
    if (!$errors && $m->findByEmail($email)) $errors['email']="Email already exists";

    if ($errors) { $old=['role'=>$role,'full_name'=>$full,'email'=>$email,'phone'=>$phone]; require dirname(__DIR__).'/views/auth/register.php'; return; }

    $m->createUser($role,$full,$email,$phone,$pass);
    header("Location: /gymm/public/login?registered=1");
    exit;
  }

  public function login(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    $errors=[]; $old=['email'=>$email];
    if ($email==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors['email']="Valid email required";
    if ($password==='') $errors['password']="Password required";
    if ($errors) { require dirname(__DIR__).'/views/auth/login.php'; return; }

    $m = new UserModel();
    $u = $m->findByEmail($email);

    if (!$u || (int)$u['is_active']!==1 || $password !== $u['password_hash']) {
      $errors['general']="Invalid login credentials.";
      require dirname(__DIR__).'/views/auth/login.php';
      return;
    }

    session_regenerate_id(true);
    $_SESSION['auth']=['id'=>(int)$u['id'],'role'=>$u['role'],'name'=>$u['full_name'],'email'=>$u['email']];
    $m->updateLastLogin((int)$u['id']);
    header("Location: /gymm/public/dashboard");
    exit;
  }

  public function dashboard(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['auth'])) { header("Location:/gymm/public/login"); exit; }
    $role = $_SESSION['auth']['role'];

    if ($role==='admin') require dirname(__DIR__).'/views/dashboard/admin.php';
    elseif ($role==='trainer') require dirname(__DIR__).'/views/dashboard/trainer.php';
    else require dirname(__DIR__).'/views/dashboard/member.php';
  }

  public function logout(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION = [];
    session_destroy();
    header("Location: /gymm/public/login");
    exit;
  }
}
