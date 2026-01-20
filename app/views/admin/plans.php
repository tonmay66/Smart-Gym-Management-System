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
  <title>Membership Plans - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Membership Plans</h2>
      <p class="card-subtitle">Manage pricing plans and packages</p>
    </div>

    <div id="alertContainer"></div>

    <div class="table-container">
      <table id="plansTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Plan Name</th>
            <th>Duration</th>
            <th>Price (BDT)</th>
            <th>Status</th>
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
      <button class="btn btn-primary" onclick="openCreateModal()">+ Create Plan</button>
    </div>
  </div>
</div>

<!-- Create Plan Modal -->
<div id="createPlanModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create New Plan</h3>
      <button class="modal-close" onclick="closeCreateModal()">&times;</button>
    </div>
    
    <div id="createModalError" class="alert alert-error hide"></div>
    
    <form id="createPlanForm">
      <div class="form-group">
        <label for="create_name">Plan Name *</label>
        <input type="text" id="create_name" name="name" placeholder="e.g., Basic Monthly" required>
      </div>

      <div class="form-group">
        <label for="create_description">Description</label>
        <textarea id="create_description" name="description" placeholder="Plan features and benefits" rows="3"></textarea>
      </div>

      <div class="form-group">
        <label for="create_duration">Duration (months) *</label>
        <input type="number" id="create_duration" name="duration_months" min="1" placeholder="1" required>
      </div>

      <div class="form-group">
        <label for="create_price">Price (BDT) *</label>
        <input type="number" id="create_price" name="price" min="0" step="0.01" placeholder="1500.00" required>
      </div>

      <div class="form-group">
        <label for="create_features">Features</label>
        <textarea id="create_features" name="features" placeholder="List of features included" rows="3"></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeCreateModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Plan</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Plan Modal -->
<div id="editPlanModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Plan</h3>
      <button class="modal-close" onclick="closeEditModal()">&times;</button>
    </div>
    
    <div id="editModalError" class="alert alert-error hide"></div>
    
    <form id="editPlanForm">
      <input type="hidden" id="edit_plan_id" name="id">
      
      <div class="form-group">
        <label for="edit_name">Plan Name *</label>
        <input type="text" id="edit_name" name="name" required>
      </div>

      <div class="form-group">
        <label for="edit_description">Description</label>
        <textarea id="edit_description" name="description" rows="3"></textarea>
      </div>

      <div class="form-group">
        <label for="edit_duration">Duration (months) *</label>
        <input type="number" id="edit_duration" name="duration_months" min="1" required>
      </div>

      <div class="form-group">
        <label for="edit_price">Price (BDT) *</label>
        <input type="number" id="edit_price" name="price" min="0" step="0.01" required>
      </div>

      <div class="form-group">
        <label for="edit_features">Features</label>
        <textarea id="edit_features" name="features" rows="3"></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Plan</button>
      </div>
    </form>
  </div>
</div>

<script src="/assets/js/admin_plans.js"></script>
</body>
</html>
