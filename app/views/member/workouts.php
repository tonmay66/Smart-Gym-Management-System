<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth']) || $_SESSION['auth']['role']!=='member') {
  http_response_code(403);
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/style.css">
  <title>My Workouts - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">My Workout Plans</h2>
      <p class="card-subtitle">View workout plans assigned to you by your trainer</p>
    </div>

    <div id="err" class="alert alert-error hide"></div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Workout</th>
            <th>Level</th>
            <th>Duration</th>
            <th>Assigned By</th>
            <th>Status</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody id="tbody">
          <!-- AJAX loaded -->
        </tbody>
      </table>
    </div>

    <div class="flex justify-between items-center mt-6" style="padding-top:var(--space-5);border-top:1px solid var(--divider);">
      <a href="/dashboard" class="btn btn-ghost">← Back to Dashboard</a>
    </div>
  </div>
</div>
<script src="/assets/js/member_workouts.js"></script>
</body>
</html>
