// Change Password functionality
document.addEventListener('DOMContentLoaded', function () {
    const msgDiv = document.getElementById('msg');
    const errDiv = document.getElementById('err');
    const form = document.getElementById('passForm');

    // Create password change form
    form.innerHTML = `
    <div class="form-group">
      <label>Current Password:</label>
      <input type="password" name="current_password" required class="form-control">
    </div>
    <div class="form-group">
      <label>New Password:</label>
      <input type="password" name="new_password" required class="form-control">
    </div>
    <div class="form-group">
      <label>Confirm New Password:</label>
      <input type="password" name="confirm_password" required class="form-control">
    </div>
    <button type="submit" class="btn">Change Password</button>
    <a href="/gymm/public/dashboard" class="btn-secondary">Back to Dashboard</a>
  `;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        changePassword(new FormData(form));
    });

    function changePassword(formData) {
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch('/gymm/public/api/password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showMessage('Password changed successfully!');
                    form.reset();
                } else {
                    showError(data.message || 'Failed to change password');
                }
            })
            .catch(err => showError('Error changing password'));
    }

    function showMessage(msg) {
        msgDiv.textContent = msg;
        msgDiv.classList.remove('hide');
        errDiv.classList.add('hide');
        setTimeout(() => msgDiv.classList.add('hide'), 3000);
    }

    function showError(msg) {
        errDiv.textContent = msg;
        errDiv.classList.remove('hide');
        msgDiv.classList.add('hide');
    }
});
