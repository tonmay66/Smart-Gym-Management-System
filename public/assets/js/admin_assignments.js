// Admin Member-Trainer Assignments
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#assignmentsTable tbody');
    const alertContainer = document.getElementById('alertContainer');
    const assignModal = document.getElementById('assignModal');
    const assignForm = document.getElementById('assignForm');

    loadAssignments();
    loadTrainers();

    if (assignForm) {
        assignForm.addEventListener('submit', function (e) {
            e.preventDefault();
            assignTrainer();
        });
    }

    function loadAssignments() {
        // Load all members with their current trainer assignments
        fetch('/api/users', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const members = data.data.filter(u => u.role === 'member');
                    displayAssignments(members);
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading members</td></tr>';
            });
    }

    function displayAssignments(members) {
        if (members.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No members found</td></tr>';
            return;
        }

        tbody.innerHTML = members.map(member => `
      <tr>
        <td><strong>${escapeHtml(member.full_name)}</strong></td>
        <td>${escapeHtml(member.email)}</td>
        <td><span id="trainer_${member.id}" class="badge badge-neutral">Loading...</span></td>
        <td>
          <button onclick="openAssignModal(${member.id}, '${escapeHtml(member.full_name)}')" class="btn btn-sm btn-primary">Assign Trainer</button>
        </td>
      </tr>
    `).join('');

        // Load trainer info for each member
        members.forEach(member => {
            loadMemberTrainer(member.id);
        });
    }

    function loadMemberTrainer(memberId) {
        fetch(`/api/member/${memberId}/trainer`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById(`trainer_${memberId}`);
                if (badge) {
                    if (data.success && data.data && data.data.trainer_name) {
                        badge.textContent = data.data.trainer_name;
                        badge.className = 'badge badge-success';
                    } else {
                        badge.textContent = 'Not assigned';
                        badge.className = 'badge badge-neutral';
                    }
                }
            })
            .catch(err => {
                const badge = document.getElementById(`trainer_${memberId}`);
                if (badge) {
                    badge.textContent = 'Not assigned';
                    badge.className = 'badge badge-neutral';
                }
            });
    }

    function loadTrainers() {
        fetch('/api/users', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const trainers = data.data.filter(u => u.role === 'trainer');
                    populateTrainerSelect(trainers);
                }
            })
            .catch(err => console.error('Error loading trainers:', err));
    }

    function populateTrainerSelect(trainers) {
        const select = document.getElementById('assign_trainer');
        const options = trainers.map(t => `<option value="${t.id}">${escapeHtml(t.full_name)}</option>`).join('');
        select.innerHTML = '<option value="">Choose a trainer</option>' + options;
    }

    window.openAssignModal = function (memberId, memberName) {
        document.getElementById('assign_member_id').value = memberId;
        document.getElementById('assign_member_name').textContent = memberName;
        assignModal.style.display = 'flex';
        document.getElementById('assignModalError').classList.add('hide');
    };

    window.closeAssignModal = function () {
        assignModal.style.display = 'none';
        assignForm.reset();
        document.getElementById('assignModalError').classList.add('hide');
    };

    function assignTrainer() {
        const memberId = parseInt(document.getElementById('assign_member_id').value);
        const trainerId = parseInt(document.getElementById('assign_trainer').value);

        if (!trainerId) {
            showModalError('assignModalError', 'Please select a trainer');
            return;
        }

        fetch('/api/assign-member', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ member_id: memberId, trainer_id: trainerId })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closeAssignModal();
                    showAlert('Trainer assigned successfully!', 'success');
                    loadAssignments();
                } else {
                    showModalError('assignModalError', response.message || 'Failed to assign trainer');
                }
            })
            .catch(err => {
                showModalError('assignModalError', 'Error assigning trainer. Please try again.');
            });
    }

    function showAlert(message, type) {
        alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    function showModalError(elementId, message) {
        const el = document.getElementById(elementId);
        el.textContent = message;
        el.classList.remove('hide');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.onclick = function (event) {
        if (event.target === assignModal) {
            closeAssignModal();
        }
    };
});
