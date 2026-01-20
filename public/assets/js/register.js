(() => {
  const form = document.getElementById('regForm');
  const errorBox = document.getElementById('clientError');

  form.addEventListener('submit', e => {
    errorBox.classList.add('hide');
    if (password.value.length < 4) {
      e.preventDefault();
      errorBox.textContent = 'Password too short';
      errorBox.classList.remove('hide');
    }
  });
})();