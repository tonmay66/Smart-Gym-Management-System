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
  <title>Trainer Schedules - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Trainer Schedules</h2>
      <p class="card-subtitle">Manage trainer schedules and assign members</p>
    </div>

    <div id="alertContainer"></div>

    <!-- Schedules Table -->
    <div class="table-container">
      <table id="schedulesTable">
        <thead>
          <tr>
            <th>Trainer</th>
            <th>Day</th>
            <th>Time</th>
            <th>Activity</th>
            <th>Location</th>
            <th>Capacity</th>
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
      <button class="btn btn-primary" onclick="openCreateModal()">+ Create Schedule</button>
    </div>
  </div>
</div>

<!-- Create Schedule Modal -->
<div id="createScheduleModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Trainer Schedule</h3>
      <button class="modal-close" onclick="closeCreateModal()">&times;</button>
    </div>
    
    <div id="createModalError" class="alert alert-error hide"></div>
    
    <form id="createScheduleForm">
      <div class="form-group">
        <label for="create_trainer">Trainer *</label>
        <select id="create_trainer" name="trainer_id" required>
          <option value="">Select Trainer</option>
        </select>
      </div>

      <div class="form-group">
        <label for="create_day">Day *</label>
        <select id="create_day" name="day_of_week" required>
          <option value="">Select Day</option>
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
          <option value="Saturday">Saturday</option>
          <option value="Sunday">Sunday</option>
        </select>
      </div>

      <div class="form-group">
        <label for="create_start_time">Start Time *</label>
        <input type="time" id="create_start_time" name="start_time" required>
      </div>

      <div class="form-group">
        <label for="create_end_time">End Time *</label>
        <input type="time" id="create_end_time" name="end_time" required>
      </div>

      <div class="form-group">
        <label for="create_activity">Activity *</label>
        <input type="text" id="create_activity" name="activity" placeholder="e.g., Personal Training" required>
      </div>

      <div class="form-group">
        <label for="create_location">Location</label>
        <input type="text" id="create_location" name="location" placeholder="e.g., Training Room 1">
      </div>

      <div class="form-group">
        <label for="create_capacity">Max Capacity</label>
        <input type="number" id="create_capacity" name="max_capacity" min="1" value="1">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeCreateModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Schedule</button>
      </div>
    </form>
  </div>
</div>

<!-- Assign Member Modal -->
<div id="assignMemberModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Assign Member to Trainer</h3>
      <button class="modal-close" onclick="closeAssignModal()">&times;</button>
    </div>
    
    <div id="assignModalError" class="alert alert-error hide"></div>
    
    <form id="assignMemberForm">
      <div class="form-group">
        <label for="assign_member">Member *</label>
        <select id="assign_member" name="member_id" required>
          <option value="">Select Member</option>
        </select>
      </div>

      <div class="form-group">
        <label for="assign_trainer">Trainer *</label>
        <select id="assign_trainer" name="trainer_id" required>
          <option value="">Select Trainer</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeAssignModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Assign Member</button>
      </div>
    </form>
  </div>
</div>

<script src="/assets/js/admin_schedules.js"></script>
</body>
</html>
