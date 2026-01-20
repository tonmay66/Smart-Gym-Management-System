<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth']) || $_SESSION['auth']['role']!=='admin') {
  header("Location:/login");
  exit;
}
$userName = htmlspecialchars($_SESSION['auth']['name']??'Admin');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/style.css">
  <title>Admin Dashboard - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-wide animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Admin Dashboard</h2>
      <p class="card-subtitle">Welcome back, <?=$userName?>!</p>
    </div>

    <p class="text-muted mb-5">Manage users, membership plans, payments & reports from your central dashboard.</p>

    <nav>
      <ul class="nav-links">
        <li>
          <a href="/admin/users">
            <span>👥</span>
            <div>
              <strong>Manage Users</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">Create, update, and manage user accounts</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/admin/plans">
            <span>💳</span>
            <div>
              <strong>Membership Plans</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">Configure pricing and plan features</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/admin/payments">
            <span>💰</span>
            <div>
              <strong>Payments & Reports</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">View transactions and financial reports</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/admin/schedules">
            <span>📅</span>
            <div>
              <strong>Trainer Schedules</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">Manage schedules and assign members</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/admin/assignments">
            <span>🔗</span>
            <div>
              <strong>Member Assignments</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">Assign members to trainers</small>
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
