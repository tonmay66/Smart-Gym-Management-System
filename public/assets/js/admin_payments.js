// Admin Payments & Dues Management
document.addEventListener('DOMContentLoaded', function () {
    const paymentsTbody = document.querySelector('#paymentsTable tbody');
    const duesTbody = document.querySelector('#duesTable tbody');
    const statsContainer = document.getElementById('statsContainer');

    loadStats();
    loadDues();
    loadPayments();

    function loadStats() {
        fetch('/api/payments/stats', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayStats(data.data);
                }
            })
            .catch(err => console.error('Error loading stats:', err));
    }

    function displayStats(stats) {
        const totalRevenue = parseFloat(stats.total_revenue || 0);
        const completedRevenue = parseFloat(stats.completed_revenue || 0);
        const pendingRevenue = parseFloat(stats.pending_revenue || 0);

        statsContainer.innerHTML = `
            <div class="card" style="padding:var(--space-4);">
                <div style="color:var(--text-muted);font-size:0.875rem;margin-bottom:var(--space-2);">Total Revenue</div>
                <div style="font-size:1.5rem;font-weight:600;color:var(--primary);">৳${formatBDT(totalRevenue)}</div>
            </div>
            <div class="card" style="padding:var(--space-4);">
                <div style="color:var(--text-muted);font-size:0.875rem;margin-bottom:var(--space-2);">Completed</div>
                <div style="font-size:1.5rem;font-weight:600;color:var(--success);">৳${formatBDT(completedRevenue)}</div>
            </div>
            <div class="card" style="padding:var(--space-4);">
                <div style="color:var(--text-muted);font-size:0.875rem;margin-bottom:var(--space-2);">Pending</div>
                <div style="font-size:1.5rem;font-weight:600;color:var(--warning);">৳${formatBDT(pendingRevenue)}</div>
            </div>
            <div class="card" style="padding:var(--space-4);">
                <div style="color:var(--text-muted);font-size:0.875rem;margin-bottom:var(--space-2);">Total Payments</div>
                <div style="font-size:1.5rem;font-weight:600;">${stats.total_payments || 0}</div>
            </div>
        `;
    }

    function loadDues() {
        fetch('/api/payments/dues', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayDues(data.data);
                } else {
                    duesTbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No outstanding dues</td></tr>';
                }
            })
            .catch(err => {
                duesTbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading dues</td></tr>';
            });
    }

    function displayDues(dues) {
        if (dues.length === 0) {
            duesTbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No outstanding dues - All payments up to date!</td></tr>';
            return;
        }

        duesTbody.innerHTML = dues.map(due => `
      <tr>
        <td><strong>${escapeHtml(due.full_name)}</strong></td>
        <td>${escapeHtml(due.email)}</td>
        <td>${escapeHtml(due.plan_name)}</td>
        <td>৳${formatBDT(due.plan_price)}</td>
        <td>৳${formatBDT(due.total_paid)}</td>
        <td><strong style="color:var(--danger);">৳${formatBDT(due.dues)}</strong></td>
        <td><span class="badge badge-${due.membership_status === 'active' ? 'success' : 'neutral'}">${due.membership_status}</span></td>
      </tr>
    `).join('');
    }

    function loadPayments() {
        fetch('/api/payments', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayPayments(data.data);
                } else {
                    paymentsTbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No payments found</td></tr>';
                }
            })
            .catch(err => {
                paymentsTbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading payments</td></tr>';
            });
    }

    function displayPayments(payments) {
        if (payments.length === 0) {
            paymentsTbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No payments recorded yet</td></tr>';
            return;
        }

        paymentsTbody.innerHTML = payments.map(payment => `
      <tr>
        <td>${payment.id}</td>
        <td><strong>${escapeHtml(payment.full_name)}</strong><br><small style="color:var(--text-muted);">${escapeHtml(payment.email)}</small></td>
        <td>${escapeHtml(payment.plan_name)}</td>
        <td><strong>৳${formatBDT(payment.amount)}</strong></td>
        <td>${formatPaymentMethod(payment.payment_method)}</td>
        <td>${formatDate(payment.payment_date)}</td>
        <td><span class="badge badge-${getStatusBadge(payment.status)}">${payment.status}</span></td>
      </tr>
    `).join('');
    }

    function formatBDT(amount) {
        return parseFloat(amount).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatPaymentMethod(method) {
        const methods = {
            'cash': 'Cash',
            'card': 'Card',
            'online': 'Online',
            'bank_transfer': 'Bank Transfer'
        };
        return methods[method] || method;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-BD', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function getStatusBadge(status) {
        const badges = {
            'completed': 'success',
            'pending': 'warning',
            'failed': 'danger',
            'refunded': 'neutral'
        };
        return badges[status] || 'neutral';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
