// Member Membership - Real-time display
document.addEventListener('DOMContentLoaded', function () {
    const contentDiv = document.getElementById('membershipContent');
    const alertContainer = document.getElementById('alertContainer');

    loadMembership();

    function loadMembership() {
        fetch('/api/member/membership', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    displayMembership(data.data);
                } else {
                    showNoMembership();
                }
            })
            .catch(err => {
                contentDiv.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:var(--space-6);">Error loading membership</p>';
            });
    }

    function displayMembership(membership) {
        if (!membership) {
            showNoMembership();
            return;
        }

        const startDate = new Date(membership.start_date);
        const endDate = new Date(membership.end_date);
        const today = new Date();
        const isActive = endDate >= today && membership.status === 'active';
        const daysRemaining = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));

        contentDiv.innerHTML = `
      <div style="display:grid;gap:var(--space-4);">
        <!-- Status Card -->
        <div class="card">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
              <h3 style="margin:0;font-size:1.25rem;">Membership Status</h3>
              <p style="margin:var(--space-2) 0 0;color:var(--text-muted);">Current status of your membership</p>
            </div>
            <span class="badge ${isActive ? 'badge-success' : 'badge-error'}" style="font-size:1rem;padding:var(--space-2) var(--space-4);">
              ${isActive ? '✓ Active' : '✗ Expired'}
            </span>
          </div>
        </div>

        <!-- Plan Details -->
        <div class="card">
          <h3 style="margin:0 0 var(--space-4);font-size:1.25rem;">Plan Details</h3>
          <div style="display:grid;gap:var(--space-3);">
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">Plan Name:</span>
              <strong>${escapeHtml(membership.plan_name)}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">Duration:</span>
              <strong>${membership.duration_months} month(s)</strong>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">Price:</span>
              <strong>৳${parseFloat(membership.price).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">Start Date:</span>
              <strong>${formatDate(startDate)}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">End Date:</span>
              <strong>${formatDate(endDate)}</strong>
            </div>
            ${isActive ? `
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">Days Remaining:</span>
              <strong style="color:${daysRemaining <= 7 ? 'var(--danger)' : 'var(--success)'};">${daysRemaining} days</strong>
            </div>
            ` : ''}
          </div>
        </div>

        <!-- Trainer Info -->
        ${membership.trainer_name ? `
        <div class="card">
          <h3 style="margin:0 0 var(--space-4);font-size:1.25rem;">Assigned Trainer</h3>
          <div style="display:grid;gap:var(--space-3);">
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">Name:</span>
              <strong>${escapeHtml(membership.trainer_name)}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-muted);">Email:</span>
              <strong>${escapeHtml(membership.trainer_email)}</strong>
            </div>
          </div>
        </div>
        ` : `
        <div class="card" style="background:var(--warning-bg);border-color:var(--warning);">
          <p style="margin:0;color:var(--warning);">⚠️ No trainer assigned yet. Contact admin to assign a trainer.</p>
        </div>
        `}
      </div>
    `;
    }

    function showNoMembership() {
        contentDiv.innerHTML = `
      <div class="card" style="text-align:center;padding:var(--space-8);">
        <h3 style="margin:0 0 var(--space-2);color:var(--text-muted);">No Active Membership</h3>
        <p style="margin:0 0 var(--space-4);color:var(--text-muted);">You don't have an active membership yet.</p>
        <a href="/member/payments" class="btn btn-primary">Make a Payment</a>
      </div>
    `;
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
