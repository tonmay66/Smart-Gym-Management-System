// Member Workouts AJAX functionality
document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.card');

    loadWorkouts();

    function loadWorkouts() {
        fetch('/gymm/public/api/workouts', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayWorkouts(data.data);
                } else {
                    showError('Failed to load workouts');
                }
            })
            .catch(err => showError('Error loading workouts'));
    }

    function displayWorkouts(workouts) {
        let html = '<table class="tbl"><thead><tr><th>ID</th><th>Name</th><th>Description</th></tr></thead><tbody>';

        workouts.forEach(workout => {
            html += `
        <tr>
          <td>${workout.id}</td>
          <td>${escapeHtml(workout.name)}</td>
          <td>${escapeHtml(workout.description)}</td>
        </tr>
      `;
        });

        html += '</tbody></table>';

        const existingTable = container.querySelector('table');
        if (existingTable) {
            existingTable.remove();
        }

        container.insertAdjacentHTML('beforeend', html);
    }

    function showError(msg) {
        container.insertAdjacentHTML('beforeend', `<p class="err">${msg}</p>`);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
