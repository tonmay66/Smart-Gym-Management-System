// Trainer Members - Show admin-assigned members
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#membersTable tbody');

    loadMembers();

    function loadMembers() {
        fetch('/api/trainer/members', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayMembers(data.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No members assigned yet</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading members</td></tr>';
            });
    }

    function displayMembers(members) {
        if (members.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No members have been assigned to you yet. Contact admin for member assignments.</td></tr>';
            return;
        }

        tbody.innerHTML = members.map(member => `
      <tr>
        <td><strong>${escapeHtml(member.full_name)}</strong></td>
        <td>${escapeHtml(member.email)}</td>
        <td>${escapeHtml(member.phone || '-')}</td>
        <td><span class="badge badge-primary">${escapeHtml(member.plan_name)}</span></td>
        <td>${formatDate(member.start_date)} - ${formatDate(member.end_date)}</td>
      </tr>
    `).join('');
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
