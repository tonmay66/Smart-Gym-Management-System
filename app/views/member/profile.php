<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth'])) {
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
  <title>My Profile - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">My Profile</h2>
      <p class="card-subtitle">View and update your profile information</p>
    </div>

    <div id="alertContainer"></div>

    <form id="profileForm">
      <div class="form-group">
        <label for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" required placeholder="Enter your full name">
      </div>

      <div class="form-group">
        <label for="email">Email Address *</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" readonly style="background:var(--card-bg);cursor:not-allowed;">
        <small style="color:var(--text-muted);">Phone number cannot be changed</small>
      </div>

      <div class="form-group">
        <label>Account Status</label>
        <div id="accountStatus" style="padding:var(--space-3);background:var(--card-bg);border-radius:var(--radius);"></div>
      </div>

      <div class="flex gap-3" style="display:flex;gap:var(--space-3);padding-top:var(--space-4);border-top:1px solid var(--divider);">
        <a href="/dashboard" class="btn btn-ghost">← Back to Dashboard</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
<script src="/assets/js/member_profile.js"></script>
</body>
</html>
