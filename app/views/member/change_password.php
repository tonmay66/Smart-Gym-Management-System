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
  <title>Change Password - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Change Password</h2>
      <p class="card-subtitle">Update your account password</p>
    </div>

    <div id="msg" class="alert alert-success hide"></div>
    <div id="err" class="alert alert-error hide"></div>

    <form id="passForm">
      <div class="form-group">
        <label for="current_password">Current Password</label>
        <input 
          id="current_password" 
          type="password" 
          name="current_password"
          placeholder="Enter your current password"
          autocomplete="current-password"
          required
        >
      </div>

      <div class="form-group">
        <label for="new_password">New Password</label>
        <input 
          id="new_password" 
          type="password" 
          name="new_password"
          placeholder="Enter new password (min 6 characters)"
          autocomplete="new-password"
          required
        >
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm New Password</label>
        <input 
          id="confirm_password" 
          type="password" 
          name="confirm_password"
          placeholder="Re-enter new password"
          autocomplete="new-password"
          required
        >
      </div>

      <button type="submit" class="btn btn-primary btn-block">
        Update Password
      </button>
    </form>

    <div class="flex justify-between items-center mt-6" style="padding-top:var(--space-5);border-top:1px solid var(--divider);">
      <a href="/dashboard" class="btn btn-ghost">← Back to Dashboard</a>
    </div>
  </div>
</div>
<script src="/assets/js/change_password.js"></script>
</body>
</html>
