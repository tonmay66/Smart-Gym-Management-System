(() => {
  const form = document.getElementById('loginForm');
  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const errorBox = document.getElementById('clientError');

  const validateEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

  form.addEventListener('submit', e => {
    errorBox.classList.add('hide');
    if (!email.value.trim() || !password.value.trim()) {
      e.preventDefault();
      errorBox.textContent = 'Email and password required';
      errorBox.classList.remove('hide');
    }
  });
})();