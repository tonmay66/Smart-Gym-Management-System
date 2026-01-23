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
  <title>My Workouts - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">My Workout Plans</h2>
      <p class="card-subtitle">Create and manage workout plans for members</p>
    </div>

    <div id="alertContainer"></div>

    <div class="table-container">
      <table id="workoutsTable">
        <thead>
          <tr>
            <th>Workout Name</th>
            <th>Difficulty</th>
            <th>Duration</th>
            <th>Created</th>
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
      <button class="btn btn-primary" onclick="openCreateModal()">+ Create Workout</button>
    </div>
  </div>
</div>

<!-- Create Workout Modal -->
<div id="createWorkoutModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create New Workout</h3>
      <button class="modal-close" onclick="closeCreateModal()">&times;</button>
    </div>
    
    <div id="createModalError" class="alert alert-error hide"></div>
    
    <form id="createWorkoutForm">
      <div class="form-group">
        <label for="create_name">Workout Name *</label>
        <input type="text" id="create_name" name="name" placeholder="e.g., Full Body Strength" required>
      </div>

      <div class="form-group">
        <label for="create_description">Description</label>
        <textarea id="create_description" name="description" placeholder="Workout description" rows="3"></textarea>
      </div>

      <div class="form-group">
        <label for="create_difficulty">Difficulty Level *</label>
        <select id="create_difficulty" name="difficulty_level" required>
          <option value="beginner">Beginner</option>
          <option value="intermediate">Intermediate</option>
          <option value="advanced">Advanced</option>
        </select>
      </div>

      <div class="form-group">
        <label for="create_duration">Duration (minutes)</label>
        <input type="number" id="create_duration" name="duration_minutes" min="1" placeholder="45">
      </div>

      <div class="form-group">
        <label for="create_members">Assign to Members (Optional)</label>
        <select id="create_members" name="member_ids" multiple style="height:120px;">
          <!-- Loaded via JS -->
        </select>
        <small style="color:var(--text-muted);display:block;margin-top:var(--space-1);">Hold Ctrl/Cmd to select multiple members</small>
      </div>

      <div class="form-group">
        <label for="create_exercises">Exercises</label>
        <textarea id="create_exercises" name="exercises" placeholder="List exercises (one per line)" rows="5"></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeCreateModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Workout</button>
      </div>
    </form>
  </div>
</div>

<!-- View Workout Details Modal -->
<div id="viewWorkoutModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Workout Details</h3>
      <button class="modal-close" onclick="closeViewModal()">&times;</button>
    </div>
    
    <div id="workoutDetails">
      <!-- Loaded via JS -->
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-ghost" onclick="closeViewModal()">Close</button>
    </div>
  </div>
</div>

<script src="/assets/js/trainer_workouts.js"></script>
</body>
</html>
