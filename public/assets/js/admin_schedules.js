// Admin Trainer Schedules & Member Assignment
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#schedulesTable tbody');
    const alertContainer = document.getElementById('alertContainer');
    const createModal = document.getElementById('createScheduleModal');
    const assignModal = document.getElementById('assignMemberModal');
    const createForm = document.getElementById('createScheduleForm');
    const assignForm = document.getElementById('assignMemberForm');

    loadSchedules();
    loadTrainers();
    loadMembers();

    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            createSchedule();
        });
    }

    if (assignForm) {
        assignForm.addEventListener('submit', function (e) {
            e.preventDefault();
            assignMember();
        });
    }

    function loadSchedules() {
        fetch('/api/schedules', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displaySchedules(data.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No schedules found</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading schedules</td></tr>';
            });
    }

    function displaySchedules(schedules) {
        if (schedules.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No schedules created yet. Create your first schedule!</td></tr>';
            return;
        }

        tbody.innerHTML = schedules.map(schedule => `
      <tr>
        <td><strong>${escapeHtml(schedule.trainer_name)}</strong></td>
        <td>${schedule.day_of_week}</td>
        <td>${formatTime(schedule.start_time)} - ${formatTime(schedule.end_time)}</td>
        <td>${escapeHtml(schedule.activity)}</td>
        <td>${escapeHtml(schedule.location || '-')}</td>
        <td>${schedule.max_capacity}</td>
        <td>
          <button onclick="deleteSchedule(${schedule.id})" class="btn btn-sm btn-danger">Delete</button>
        </td>
      </tr>
    `).join('');
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
                    populateTrainerDropdowns(trainers);
                }
            })
            .catch(err => console.error('Error loading trainers:', err));
    }

    function populateTrainerDropdowns(trainers) {
        const createTrainerSelect = document.getElementById('create_trainer');
        const assignTrainerSelect = document.getElementById('assign_trainer');

        const options = trainers.map(t => `<option value="${t.id}">${escapeHtml(t.full_name)}</option>`).join('');

        if (createTrainerSelect) {
            createTrainerSelect.innerHTML = '<option value="">Select Trainer</option>' + options;
        }
        if (assignTrainerSelect) {
            assignTrainerSelect.innerHTML = '<option value="">Select Trainer</option>' + options;
        }
    }

    function loadMembers() {
        fetch('/api/users', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const members = data.data.filter(u => u.role === 'member');
                    populateMemberDropdown(members);
                }
            })
            .catch(err => console.error('Error loading members:', err));
    }

    function populateMemberDropdown(members) {
        const assignMemberSelect = document.getElementById('assign_member');
        const options = members.map(m => `<option value="${m.id}">${escapeHtml(m.full_name)} (${escapeHtml(m.email)})</option>`).join('');

        if (assignMemberSelect) {
            assignMemberSelect.innerHTML = '<option value="">Select Member</option>' + options;
        }
    }

    function createSchedule() {
        const formData = new FormData(createForm);
        const data = {
            trainer_id: parseInt(formData.get('trainer_id')),
            day_of_week: formData.get('day_of_week'),
            start_time: formData.get('start_time'),
            end_time: formData.get('end_time'),
            activity: formData.get('activity'),
            location: formData.get('location'),
            max_capacity: parseInt(formData.get('max_capacity') || 1),
            is_active: 1
        };

        if (!data.trainer_id || !data.day_of_week || !data.start_time || !data.end_time || !data.activity) {
            showModalError('createModalError', 'Please fill in all required fields');
            return;
        }

        fetch('/api/schedules', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closeCreateModal();
                    showAlert('Schedule created successfully!', 'success');
                    loadSchedules();
                } else {
                    showModalError('createModalError', response.message || 'Failed to create schedule');
                }
            })
            .catch(err => {
                showModalError('createModalError', 'Error creating schedule. Please try again.');
            });
    }

    function assignMember() {
        const formData = new FormData(assignForm);
        const data = {
            member_id: parseInt(formData.get('member_id')),
            trainer_id: parseInt(formData.get('trainer_id'))
        };

        if (!data.member_id || !data.trainer_id) {
            showModalError('assignModalError', 'Please select both member and trainer');
            return;
        }

        fetch('/api/assign-member', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closeAssignModal();
                    showAlert('Member assigned to trainer successfully!', 'success');
                } else {
                    showModalError('assignModalError', response.message || 'Failed to assign member');
                }
            })
            .catch(err => {
                showModalError('assignModalError', 'Error assigning member. Please try again.');
            });
    }

    window.deleteSchedule = function (scheduleId) {
        if (!confirm('Are you sure you want to delete this schedule?')) {
            return;
        }

        fetch('/api/schedules/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: scheduleId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('Schedule deleted successfully!', 'success');
                    loadSchedules();
                } else {
                    showAlert(data.message || 'Failed to delete schedule', 'error');
                }
            })
            .catch(err => showAlert('Error deleting schedule', 'error'));
    };

    window.openCreateModal = function () {
        createModal.style.display = 'flex';
        document.getElementById('createModalError').classList.add('hide');
    };

    window.closeCreateModal = function () {
        createModal.style.display = 'none';
        createForm.reset();
        document.getElementById('createModalError').classList.add('hide');
    };

    window.openAssignModal = function () {
        assignModal.style.display = 'flex';
        document.getElementById('assignModalError').classList.add('hide');
    };

    window.closeAssignModal = function () {
        assignModal.style.display = 'none';
        assignForm.reset();
        document.getElementById('assignModalError').classList.add('hide');
    };

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

    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.onclick = function (event) {
        if (event.target === createModal) {
            closeCreateModal();
        } else if (event.target === assignModal) {
            closeAssignModal();
        }
    };
});
