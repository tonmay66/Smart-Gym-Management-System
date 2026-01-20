<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth']) || $_SESSION['auth']['role']!=='member') {
  header("Location:/login");
  exit;
}
$userName = htmlspecialchars($_SESSION['auth']['name']??'Member');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/style.css">
  <title>Member Dashboard - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-wide animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Member Dashboard</h2>
      <p class="card-subtitle">Welcome back, <?=$userName?>!</p>
    </div>

    <p class="text-muted mb-5">Access your profile, membership details, workouts, and payment history.</p>

    <nav>
      <ul class="nav-links">
        <li>
          <a href="/member/profile">
            <span>👤</span>
            <div>
              <strong>My Profile</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">View and update your personal information</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/member/membership">
            <span>🎫</span>
            <div>
              <strong>My Membership</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">View membership plan and status</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/member/workouts">
            <span>💪</span>
            <div>
              <strong>My Workouts</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">View assigned workout plans</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/member/payments">
            <span>💳</span>
            <div>
              <strong>Payment History</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">View your transaction history</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/member/password">
            <span>🔒</span>
            <div>
              <strong>Change Password</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">Update your account password</small>
            </div>
          </a>
        </li>
      </ul>
    </nav>

    <div class="flex justify-between items-center mt-6" style="padding-top:var(--space-5);border-top:1px solid var(--divider);">
      <a href="/logout" class="btn btn-ghost">Logout</a>
    </div>
  </div>
</div>
</body>
</html>
