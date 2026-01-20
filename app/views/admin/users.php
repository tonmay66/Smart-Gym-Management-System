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
  <title>Manage Users - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Manage Users</h2>
      <p class="card-subtitle">Create, update, activate or deactivate user accounts</p>
    </div>

    <div id="alertContainer"></div>

    <div class="table-container">
      <table id="usersTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
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
      <button class="btn btn-primary" onclick="openCreateModal()">+ Create User</button>
    </div>
  </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create New User</h3>
      <button class="modal-close" onclick="closeCreateModal()">&times;</button>
    </div>
    
    <div id="createModalError" class="alert alert-error hide"></div>
    <div id="createModalSuccess" class="alert alert-success hide"></div>
    
    <form id="createUserForm">
      <div class="form-group">
        <label for="create_full_name">Full Name *</label>
        <input 
          type="text" 
          id="create_full_name" 
          name="full_name" 
          placeholder="Enter full name"
          required
        >
      </div>

      <div class="form-group">
        <label for="create_email">Email *</label>
        <input 
          type="email" 
          id="create_email" 
          name="email" 
          placeholder="user@example.com"
          required
        >
      </div>

      <div class="form-group">
        <label for="create_phone">Phone</label>
        <input 
          type="tel" 
          id="create_phone" 
          name="phone" 
          placeholder="123-456-7890"
        >
      </div>

      <div class="form-group">
        <label for="create_role">Role *</label>
        <select id="create_role" name="role" required>
          <option value="member">Member</option>
          <option value="trainer">Trainer</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="form-group">
        <label for="create_password">Password *</label>
        <input 
          type="password" 
          id="create_password" 
          name="password" 
          placeholder="Minimum 4 characters"
          required
        >
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeCreateModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create User</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit User</h3>
      <button class="modal-close" onclick="closeEditModal()">&times;</button>
    </div>
    
    <div id="editModalError" class="alert alert-error hide"></div>
    <div id="editModalSuccess" class="alert alert-success hide"></div>
    
    <form id="editUserForm">
      <input type="hidden" id="edit_user_id" name="id">
      
      <div class="form-group">
        <label for="edit_full_name">Full Name *</label>
        <input 
          type="text" 
          id="edit_full_name" 
          name="full_name" 
          placeholder="Enter full name"
          required
        >
      </div>

      <div class="form-group">
        <label for="edit_email">Email *</label>
        <input 
          type="email" 
          id="edit_email" 
          name="email" 
          placeholder="user@example.com"
          required
        >
      </div>

      <div class="form-group">
        <label for="edit_phone">Phone</label>
        <input 
          type="tel" 
          id="edit_phone" 
          name="phone" 
          placeholder="123-456-7890"
        >
      </div>

      <div class="form-group">
        <label for="edit_role">Role *</label>
        <select id="edit_role" name="role" required>
          <option value="member">Member</option>
          <option value="trainer">Trainer</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="form-group">
        <label for="edit_password">New Password (leave blank to keep current)</label>
        <input 
          type="password" 
          id="edit_password" 
          name="password" 
          placeholder="Enter new password or leave blank"
        >
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Update User</button>
      </div>
    </form>
  </div>
</div>

<script src="/assets/js/admin_users.js"></script>
</body>
</html>
