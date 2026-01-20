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
  <title>Payments & Dues - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Payments & Dues</h2>
      <p class="card-subtitle">Track all payments and outstanding dues</p>
    </div>

    <!-- Stats Cards -->
    <div id="statsContainer" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:var(--space-4);margin-bottom:var(--space-6);">
      <!-- Loaded via JS -->
    </div>

    <!-- Dues Section -->
    <div style="margin-bottom:var(--space-6);">
      <h3 style="margin-bottom:var(--space-3);">Outstanding Dues</h3>
      <div class="table-container">
        <table id="duesTable">
          <thead>
            <tr>
              <th>Member</th>
              <th>Email</th>
              <th>Plan</th>
              <th>Total Price (BDT)</th>
              <th>Paid (BDT)</th>
              <th>Dues (BDT)</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- AJAX loaded -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- All Payments Section -->
    <div>
      <h3 style="margin-bottom:var(--space-3);">All Payments</h3>
      <div class="table-container">
        <table id="paymentsTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Member</th>
              <th>Plan</th>
              <th>Amount (BDT)</th>
              <th>Method</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- AJAX loaded -->
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex justify-between items-center mt-6" style="padding-top:var(--space-5);border-top:1px solid var(--divider);">
      <a href="/dashboard" class="btn btn-ghost">← Back to Dashboard</a>
    </div>
  </div>
</div>
<script src="/assets/js/admin_payments.js"></script>
</body>
</html>
