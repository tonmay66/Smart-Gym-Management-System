// Admin Users Management AJAX functionality
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#usersTable tbody');
    const alertContainer = document.getElementById('alertContainer');
    const createModal = document.getElementById('createUserModal');
    const editModal = document.getElementById('editUserModal');
    const createForm = document.getElementById('createUserForm');
    const editForm = document.getElementById('editUserForm');

    loadUsers();

    // Handle create form submission
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            createUser();
        });
    }

    // Handle edit form submission
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            updateUser();
        });
    }

    function loadUsers() {
        fetch('/api/users', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayUsers(data.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Failed to load users</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading users</td></tr>';
            });
    }

    function displayUsers(users) {
        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No users found</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(user => `
      <tr>
        <td>${user.id}</td>
        <td>${escapeHtml(user.full_name)}</td>
        <td>${escapeHtml(user.email)}</td>
        <td><span class="badge badge-${getRoleBadge(user.role)}">${user.role}</span></td>
        <td><span class="badge badge-${user.is_active == 1 ? 'success' : 'neutral'}">${user.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
        <td>
          <button onclick="openEditModal(${user.id})" class="btn btn-sm btn-ghost">Edit</button>
          <button onclick="toggleStatus(${user.id}, ${user.is_active == 1 ? 0 : 1})" class="btn btn-sm ${user.is_active == 1 ? 'btn-danger' : 'btn-success'}">
            ${user.is_active == 1 ? 'Deactivate' : 'Activate'}
          </button>
          <button onclick="deleteUser(${user.id})" class="btn btn-sm btn-danger">Delete</button>
        </td>
      </tr>
    `).join('');
    }

    function getRoleBadge(role) {
        switch (role) {
            case 'admin': return 'danger';
            case 'trainer': return 'warning';
            case 'member': return 'info';
            default: return 'neutral';
        }
    }

    function createUser() {
        const formData = new FormData(createForm);
        const data = {
            full_name: formData.get('full_name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            role: formData.get('role'),
            password: formData.get('password')
        };

        // Validation
        if (!data.full_name || !data.email || !data.password) {
            showModalError('createModalError', 'Please fill in all required fields');
            return;
        }

        if (data.password.length < 4) {
            showModalError('createModalError', 'Password must be at least 4 characters');
            return;
        }

        hideModalMessages('create');

        fetch('/api/users', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    showModalSuccess('createModalSuccess', response.message || 'User created successfully!');
                    createForm.reset();

                    // Show global success message and reload
                    setTimeout(() => {
                        closeCreateModal();
                        showAlert('User created successfully!', 'success');
                        loadUsers();
                    }, 1500);
                } else {
                    showModalError('createModalError', response.message || 'Failed to create user');
                }
            })
            .catch(err => {
                showModalError('createModalError', 'Error creating user. Please try again.');
            });
    }

    function updateUser() {
        const formData = new FormData(editForm);
        const data = {
            id: parseInt(formData.get('id')),
            full_name: formData.get('full_name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            role: formData.get('role')
        };

        // Only include password if it was entered
        const password = formData.get('password');
        if (password && password.trim() !== '') {
            if (password.length < 4) {
                showModalError('editModalError', 'Password must be at least 4 characters');
                return;
            }
            data.password = password;
        }

        // Validation
        if (!data.full_name || !data.email) {
            showModalError('editModalError', 'Please fill in all required fields');
            return;
        }

        hideModalMessages('edit');

        fetch('/api/users/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    showModalSuccess('editModalSuccess', response.message || 'User updated successfully!');

                    // Show global success message and reload
                    setTimeout(() => {
                        closeEditModal();
                        showAlert('User updated successfully!', 'success');
                        loadUsers();
                    }, 1500);
                } else {
                    showModalError('editModalError', response.message || 'Failed to update user');
                }
            })
            .catch(err => {
                showModalError('editModalError', 'Error updating user. Please try again.');
            });
    }

    window.toggleStatus = function (userId, newStatus) {
        if (!confirm('Are you sure you want to change this user\'s status?')) {
            return;
        }

        fetch('/api/users/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: userId, is_active: newStatus })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message || 'User status updated successfully!', 'success');
                    loadUsers();
                } else {
                    showAlert(data.message || 'Failed to update user status', 'error');
                }
            })
            .catch(err => showAlert('Error updating user status', 'error'));
    };

    window.deleteUser = function (userId) {
        if (!confirm('Are you sure you want to DELETE this user? This action cannot be undone!')) {
            return;
        }

        fetch('/api/users/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: userId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message || 'User deleted successfully!', 'success');
                    loadUsers();
                } else {
                    showAlert(data.message || 'Failed to delete user', 'error');
                }
            })
            .catch(err => showAlert('Error deleting user', 'error'));
    };

    window.openCreateModal = function () {
        createModal.style.display = 'flex';
        hideModalMessages('create');
    };

    window.closeCreateModal = function () {
        createModal.style.display = 'none';
        createForm.reset();
        hideModalMessages('create');
    };

    window.openEditModal = function (userId) {
        // Fetch user data
        fetch(`/api/users/${userId}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const user = data.data;
                    document.getElementById('edit_user_id').value = user.id;
                    document.getElementById('edit_full_name').value = user.full_name;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_phone').value = user.phone || '';
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_password').value = '';

                    editModal.style.display = 'flex';
                    hideModalMessages('edit');
                } else {
                    showAlert('Failed to load user data', 'error');
                }
            })
            .catch(err => showAlert('Error loading user data', 'error'));
    };

    window.closeEditModal = function () {
        editModal.style.display = 'none';
        editForm.reset();
        hideModalMessages('edit');
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

        // Hide success message
        const successId = elementId.replace('Error', 'Success');
        document.getElementById(successId).classList.add('hide');
    }

    function showModalSuccess(elementId, message) {
        const el = document.getElementById(elementId);
        el.textContent = message;
        el.classList.remove('hide');

        // Hide error message
        const errorId = elementId.replace('Success', 'Error');
        document.getElementById(errorId).classList.add('hide');
    }

    function hideModalMessages(modalType) {
        if (modalType === 'create') {
            document.getElementById('createModalError').classList.add('hide');
            document.getElementById('createModalSuccess').classList.add('hide');
        } else if (modalType === 'edit') {
            document.getElementById('editModalError').classList.add('hide');
            document.getElementById('editModalSuccess').classList.add('hide');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close modals when clicking outside
    window.onclick = function (event) {
        if (event.target === createModal) {
            closeCreateModal();
        } else if (event.target === editModal) {
            closeEditModal();
        }
    };
});
