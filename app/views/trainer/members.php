<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth']) || $_SESSION['auth']['role']!=='trainer') {
  header("Location:/login");
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/style.css">
  <title>My Members - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">My Assigned Members</h2>
      <p class="card-subtitle">Members assigned to you by admin</p>
    </div>

    <div class="table-container">
      <table id="membersTable">
        <thead>
          <tr>
            <th>Member Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Plan</th>
            <th>Membership Period</th>
          </tr>
        </thead>
        <tbody>
          <!-- AJAX loaded -->
        </tbody>
      </table>
    </div>

    <div class="flex justify-between items-center mt-6" style="padding-top:var(--space-5);border-top:1px solid var(--divider);">
      <a href="/dashboard" class="btn btn-ghost">← Back to Dashboard</a>
    </div>
  </div>
</div>
<script src="/assets/js/trainer_members.js"></script>
</body>
</html>
