<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['auth']) || $_SESSION['auth']['role']!=='member') {
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
  <title>Payment History - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card card-full animate-fade-in">
    <div class="card-header">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
          <h2 class="card-title">Payment History</h2>
          <p class="card-subtitle">View and manage your payments</p>
        </div>
        <button onclick="openPaymentModal()" class="btn btn-primary">+ Make Payment</button>
      </div>
    </div>

    <div id="alertContainer"></div>

    <div class="table-container">
      <table id="paymentsTable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Plan</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loaded via JavaScript -->
        </tbody>
      </table>
    </div>

    <div class="flex justify-between items-center mt-6" style="padding-top:var(--space-5);border-top:1px solid var(--divider);">
      <a href="/dashboard" class="btn btn-ghost">← Back to Dashboard</a>
    </div>
  </div>
</div>

<!-- Make Payment Modal -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Make Payment</h3>
      <button class="modal-close" onclick="closePaymentModal()">&times;</button>
    </div>
    
    <div id="paymentModalError" class="alert alert-error hide"></div>
    
    <form id="paymentForm">
      <div class="form-group">
        <label for="plan_id">Select Membership Plan *</label>
        <select id="plan_id" name="plan_id" required>
          <option value="">Choose a plan</option>
          <!-- Loaded via JS -->
        </select>
      </div>

      <div class="form-group">
        <label for="plan_amount">Amount (BDT)</label>
        <input type="text" id="plan_amount" readonly style="background:var(--card-bg);cursor:not-allowed;">
      </div>

      <div class="form-group">
        <label for="payment_method">Payment Method *</label>
        <select id="payment_method" name="payment_method" required>
          <option value="">Select method</option>
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="online">Online</option>
          <option value="bank_transfer">Bank Transfer</option>
        </select>
      </div>

      <div class="form-group">
        <label for="transaction_id">Transaction ID (Optional)</label>
        <input type="text" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID if applicable">
      </div>

      <div class="form-group">
        <label for="notes">Notes (Optional)</label>
        <textarea id="notes" name="notes" rows="3" placeholder="Any additional notes"></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closePaymentModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Complete Payment</button>
      </div>
    </form>
  </div>
</div>

<script src="/assets/js/member_payments.js"></script>
</body>
</html>
