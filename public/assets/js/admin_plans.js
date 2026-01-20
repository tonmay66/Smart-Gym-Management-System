// Admin Plans Management
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#plansTable tbody');
    const alertContainer = document.getElementById('alertContainer');
    const createModal = document.getElementById('createPlanModal');
    const editModal = document.getElementById('editPlanModal');
    const createForm = document.getElementById('createPlanForm');
    const editForm = document.getElementById('editPlanForm');

    loadPlans();

    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            createPlan();
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            updatePlan();
        });
    }

    function loadPlans() {
        fetch('/api/plans', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayPlans(data.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No plans found</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading plans</td></tr>';
            });
    }

    function displayPlans(plans) {
        if (plans.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No plans available. Create your first plan!</td></tr>';
            return;
        }

        tbody.innerHTML = plans.map(plan => `
      <tr>
        <td>${plan.id}</td>
        <td><strong>${escapeHtml(plan.name)}</strong></td>
        <td>${plan.duration_months} month${plan.duration_months > 1 ? 's' : ''}</td>
        <td><strong>৳${formatBDT(plan.price)}</strong></td>
        <td><span class="badge badge-${plan.is_active == 1 ? 'success' : 'neutral'}">${plan.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
        <td>
          <button onclick="openEditModal(${plan.id})" class="btn btn-sm btn-ghost">Edit</button>
          <button onclick="deletePlan(${plan.id})" class="btn btn-sm btn-danger">Delete</button>
        </td>
      </tr>
    `).join('');
    }

    function createPlan() {
        const formData = new FormData(createForm);
        const data = {
            name: formData.get('name'),
            description: formData.get('description'),
            duration_months: parseInt(formData.get('duration_months')),
            price: parseFloat(formData.get('price')),
            features: formData.get('features'),
            is_active: 1
        };

        if (!data.name || !data.duration_months || !data.price) {
            showModalError('createModalError', 'Please fill in all required fields');
            return;
        }

        fetch('/api/plans', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closeCreateModal();
                    showAlert('Plan created successfully!', 'success');
                    loadPlans();
                } else {
                    showModalError('createModalError', response.message || 'Failed to create plan');
                }
            })
            .catch(err => {
                showModalError('createModalError', 'Error creating plan. Please try again.');
            });
    }

    function updatePlan() {
        const formData = new FormData(editForm);
        const data = {
            id: parseInt(formData.get('id')),
            name: formData.get('name'),
            description: formData.get('description'),
            duration_months: parseInt(formData.get('duration_months')),
            price: parseFloat(formData.get('price')),
            features: formData.get('features')
        };

        if (!data.name || !data.duration_months || !data.price) {
            showModalError('editModalError', 'Please fill in all required fields');
            return;
        }

        fetch('/api/plans/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closeEditModal();
                    showAlert('Plan updated successfully!', 'success');
                    loadPlans();
                } else {
                    showModalError('editModalError', response.message || 'Failed to update plan');
                }
            })
            .catch(err => {
                showModalError('editModalError', 'Error updating plan. Please try again.');
            });
    }

    window.deletePlan = function (planId) {
        if (!confirm('Are you sure you want to DELETE this plan? This action cannot be undone!')) {
            return;
        }

        fetch('/api/plans/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: planId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('Plan deleted successfully!', 'success');
                    loadPlans();
                } else {
                    showAlert(data.message || 'Failed to delete plan', 'error');
                }
            })
            .catch(err => showAlert('Error deleting plan', 'error'));
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

    window.openEditModal = function (planId) {
        fetch(`/api/plans/${planId}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const plan = data.data;
                    document.getElementById('edit_plan_id').value = plan.id;
                    document.getElementById('edit_name').value = plan.name;
                    document.getElementById('edit_description').value = plan.description || '';
                    document.getElementById('edit_duration').value = plan.duration_months;
                    document.getElementById('edit_price').value = plan.price;
                    document.getElementById('edit_features').value = plan.features || '';

                    editModal.style.display = 'flex';
                    document.getElementById('editModalError').classList.add('hide');
                } else {
                    showAlert('Failed to load plan data', 'error');
                }
            })
            .catch(err => showAlert('Error loading plan data', 'error'));
    };

    window.closeEditModal = function () {
        editModal.style.display = 'none';
        editForm.reset();
        document.getElementById('editModalError').classList.add('hide');
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

    function formatBDT(amount) {
        return parseFloat(amount).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.onclick = function (event) {
        if (event.target === createModal) {
            closeCreateModal();
        } else if (event.target === editModal) {
            closeEditModal();
        }
    };
});
