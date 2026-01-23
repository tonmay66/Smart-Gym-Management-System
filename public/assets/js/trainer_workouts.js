// Trainer Workouts - Create, View, Delete
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#workoutsTable tbody');
    const alertContainer = document.getElementById('alertContainer');
    const createModal = document.getElementById('createWorkoutModal');
    const viewModal = document.getElementById('viewWorkoutModal');
    const createForm = document.getElementById('createWorkoutForm');

    loadWorkouts();
    loadMembers();

    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            createWorkout();
        });
    }

    function loadWorkouts() {
        fetch('/api/workouts', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayWorkouts(data.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No workouts found</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading workouts</td></tr>';
            });
    }

    function displayWorkouts(workouts) {
        if (workouts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No workout plans created yet. Create your first workout!</td></tr>';
            return;
        }

        tbody.innerHTML = workouts.map(workout => `
      <tr>
        <td><strong>${escapeHtml(workout.name)}</strong></td>
        <td><span class="badge badge-${getDifficultyBadge(workout.difficulty_level)}">${workout.difficulty_level}</span></td>
        <td>${workout.duration_minutes ? workout.duration_minutes + ' min' : '-'}</td>
        <td>${formatDate(workout.created_at)}</td>
        <td>
          <button onclick="viewWorkout(${workout.id})" class="btn btn-sm btn-ghost">View</button>
          <button onclick="deleteWorkout(${workout.id})" class="btn btn-sm btn-danger">Delete</button>
        </td>
      </tr>
    `).join('');
    }

    function loadMembers() {
        fetch('/api/trainer/members', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    populateMemberSelect(data.data);
                } else {
                    populateMemberSelect([]);
                }
            })
            .catch(err => {
                console.error('Error loading members:', err);
                populateMemberSelect([]);
            });
    }

    function populateMemberSelect(members) {
        const select = document.getElementById('create_members');
        if (!select) return;

        if (members.length === 0) {
            select.innerHTML = '<option value="" disabled>No members assigned to you yet</option>';
            return;
        }

        select.innerHTML = members.map(member =>
            `<option value="${member.id}">${escapeHtml(member.full_name)} (${escapeHtml(member.email)})</option>`
        ).join('');
    }

    function createWorkout() {
        const formData = new FormData(createForm);

        // Get selected members from multi-select
        const memberSelect = document.getElementById('create_members');
        const selectedMembers = [];
        if (memberSelect && memberSelect.options) {
            for (let option of memberSelect.options) {
                if (option.selected && option.value) {
                    selectedMembers.push(parseInt(option.value));
                }
            }
        }

        const data = {
            name: formData.get('name'),
            description: formData.get('description'),
            difficulty_level: formData.get('difficulty_level'),
            duration_minutes: formData.get('duration_minutes') ? parseInt(formData.get('duration_minutes')) : null,
            exercises: formData.get('exercises'),
            member_ids: selectedMembers
        };

        if (!data.name) {
            showModalError('createModalError', 'Workout name is required');
            return;
        }

        fetch('/api/workouts', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closeCreateModal();
                    showAlert('Workout created successfully!', 'success');
                    loadWorkouts();
                } else {
                    showModalError('createModalError', response.message || 'Failed to create workout');
                }
            })
            .catch(err => {
                showModalError('createModalError', 'Error creating workout. Please try again.');
            });
    }

    window.viewWorkout = function (workoutId) {
        // Fetch workout details and assignments
        Promise.all([
            fetch(`/api/workouts`, { method: 'GET', headers: { 'Content-Type': 'application/json' } }).then(r => r.json()),
            fetch(`/api/workouts/${workoutId}/assignments`, { method: 'GET', headers: { 'Content-Type': 'application/json' } }).then(r => r.json())
        ])
            .then(([workoutsData, assignmentsData]) => {
                const workout = workoutsData.data.find(w => w.id === workoutId);
                const assignments = assignmentsData.data || [];

                if (workout) {
                    displayWorkoutDetails(workout, assignments);
                    viewModal.style.display = 'flex';
                } else {
                    showAlert('Workout not found', 'error');
                }
            })
            .catch(err => showAlert('Error loading workout details', 'error'));
    };

    function displayWorkoutDetails(workout, assignments) {
        const detailsDiv = document.getElementById('workoutDetails');

        let assignmentsHTML = '';
        if (assignments.length > 0) {
            assignmentsHTML = `
                <h4 style="margin-top:var(--space-4);margin-bottom:var(--space-3);">Assigned Members (${assignments.length})</h4>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Email</th>
                                <th>Plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${assignments.map(a => `
                                <tr>
                                    <td>${escapeHtml(a.full_name)}</td>
                                    <td>${escapeHtml(a.email)}</td>
                                    <td><span class="badge badge-primary">${escapeHtml(a.plan_name || 'N/A')}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            assignmentsHTML = '<p style="margin-top:var(--space-4);color:var(--text-muted);">No members assigned to this workout yet.</p>';
        }

        detailsDiv.innerHTML = `
            <div style="padding:var(--space-4);">
                <h4 style="margin-bottom:var(--space-3);">${escapeHtml(workout.name)}</h4>
                <p style="color:var(--text-muted);margin-bottom:var(--space-3);">${escapeHtml(workout.description || 'No description')}</p>
                
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:var(--space-3);margin-bottom:var(--space-4);">
                    <div>
                        <small style="color:var(--text-muted);">Difficulty</small>
                        <div><span class="badge badge-${getDifficultyBadge(workout.difficulty_level)}">${workout.difficulty_level}</span></div>
                    </div>
                    <div>
                        <small style="color:var(--text-muted);">Duration</small>
                        <div><strong>${workout.duration_minutes ? workout.duration_minutes + ' minutes' : 'Not specified'}</strong></div>
                    </div>
                </div>

                ${workout.exercises ? `
                    <div style="margin-bottom:var(--space-4);">
                        <h5 style="margin-bottom:var(--space-2);">Exercises</h5>
                        <div style="background:var(--card-bg);padding:var(--space-3);border-radius:var(--radius);white-space:pre-wrap;">${escapeHtml(workout.exercises)}</div>
                    </div>
                ` : ''}

                ${assignmentsHTML}
            </div>
        `;
    }

    window.deleteWorkout = function (workoutId) {
        if (!confirm('Are you sure you want to DELETE this workout plan? This action cannot be undone!')) {
            return;
        }

        fetch('/api/workouts/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: workoutId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('Workout deleted successfully!', 'success');
                    loadWorkouts();
                } else {
                    showAlert(data.message || 'Failed to delete workout', 'error');
                }
            })
            .catch(err => showAlert('Error deleting workout', 'error'));
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

    window.closeViewModal = function () {
        viewModal.style.display = 'none';
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

    function getDifficultyBadge(level) {
        const badges = {
            'beginner': 'success',
            'intermediate': 'warning',
            'advanced': 'danger'
        };
        return badges[level] || 'neutral';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.onclick = function (event) {
        if (event.target === createModal) {
            closeCreateModal();
        } else if (event.target === viewModal) {
            closeViewModal();
        }
    };
});
