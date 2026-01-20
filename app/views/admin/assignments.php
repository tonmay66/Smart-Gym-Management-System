<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth']) || $_SESSION['auth']['role']!=='admin') {
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
  <title>Member Assignments - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Member-Trainer Assignments</h2>
      <p class="card-subtitle">Assign members to trainers</p>
    </div>

    <div id="alertContainer"></div>

    <div class="table-container">
      <table id="assignmentsTable">
        <thead>
          <tr>
            <th>Member</th>
            <th>Email</th>
            <th>Current Trainer</th>
            <th>Actions</th>
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

<!-- Assign Trainer Modal -->
<div id="assignModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Assign Trainer to Member</h3>
      <button class="modal-close" onclick="closeAssignModal()">&times;</button>
    </div>
    
    <div id="assignModalError" class="alert alert-error hide"></div>
    
    <form id="assignForm">
      <input type="hidden" id="assign_member_id">
      
      <div class="form-group">
        <label>Member</label>
        <div id="assign_member_name" style="padding:var(--space-2);background:var(--card-bg);border-radius:var(--radius);"></div>
      </div>

      <div class="form-group">
        <label for="assign_trainer">Select Trainer *</label>
        <select id="assign_trainer" required>
          <option value="">Choose a trainer</option>
          <!-- Loaded via JS -->
        </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeAssignModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Assign Trainer</button>
      </div>
    </form>
  </div>
</div>

<script src="/assets/js/admin_assignments.js"></script>
</body>
</html>
