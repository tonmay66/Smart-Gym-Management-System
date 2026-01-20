<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth']) || $_SESSION['auth']['role']!=='trainer') {
  header("Location:/login");
  exit;
}
$userName = htmlspecialchars($_SESSION['auth']['name']??'Trainer');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/style.css">
  <title>Trainer Dashboard - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-wide animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Trainer Dashboard</h2>
      <p class="card-subtitle">Welcome back, <?=$userName?>!</p>
    </div>

    <p class="text-muted mb-5">Manage your members, create workout plans, and view your schedule.</p>

    <nav>
      <ul class="nav-links">
        <li>
          <a href="/trainer/members">
            <span>👥</span>
            <div>
              <strong>My Members</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">View and manage assigned members</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/trainer/workouts">
            <span>💪</span>
            <div>
              <strong>Workout Plans</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">Create and assign workout routines</small>
            </div>
          </a>
        </li>
        <li>
          <a href="/trainer/schedule">
            <span>📅</span>
            <div>
              <strong>My Schedule</strong>
              <small class="text-muted" style="display:block;font-size:0.75rem;">View your weekly training schedule</small>
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
