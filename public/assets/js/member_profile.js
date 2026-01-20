// Member Profile - Edit full name and email
document.addEventListener('DOMContentLoaded', function () {
  const profileForm = document.getElementById('profileForm');
  const alertContainer = document.getElementById('alertContainer');
  const fullNameInput = document.getElementById('full_name');
  const emailInput = document.getElementById('email');
  const phoneInput = document.getElementById('phone');
  const accountStatus = document.getElementById('accountStatus');

  loadProfile();

  if (profileForm) {
    profileForm.addEventListener('submit', function (e) {
      e.preventDefault();
      updateProfile();
    });
  }

  function loadProfile() {
    fetch('/api/profile', {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' }
    })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.data) {
          displayProfile(data.data);
        } else {
          showAlert('Error loading profile', 'error');
        }
      })
      .catch(err => {
        showAlert('Error loading profile', 'error');
      });
  }

  function displayProfile(profile) {
    fullNameInput.value = profile.full_name || '';
    emailInput.value = profile.email || '';
    phoneInput.value = profile.phone || 'Not provided';

    const isActive = profile.is_active == 1;
    accountStatus.innerHTML = `
            <span class="badge ${isActive ? 'badge-success' : 'badge-error'}">
                ${isActive ? '✓ Active' : '✗ Inactive'}
            </span>
        `;
  }

  function updateProfile() {
    const fullName = fullNameInput.value.trim();
    const email = emailInput.value.trim();

    // Validation
    if (!fullName) {
      showAlert('Full name is required', 'error');
      return;
    }

    if (!email) {
      showAlert('Email is required', 'error');
      return;
    }

    if (!isValidEmail(email)) {
      showAlert('Please enter a valid email address', 'error');
      return;
    }

    const data = {
      full_name: fullName,
      email: email
    };

    fetch('/api/profile', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
      .then(res => res.json())
      .then(response => {
        if (response.success) {
          showAlert(response.message || 'Profile updated successfully!', 'success');
          // Reload profile to show updated data
          setTimeout(() => {
            loadProfile();
          }, 1000);
        } else {
          showAlert(response.message || 'Failed to update profile', 'error');
        }
      })
      .catch(err => {
        showAlert('Error updating profile. Please try again.', 'error');
      });
  }

  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function showAlert(message, type) {
    alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    setTimeout(() => {
      alertContainer.innerHTML = '';
    }, 5000);
  }
});
