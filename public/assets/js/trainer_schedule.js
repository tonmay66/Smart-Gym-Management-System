// Trainer Schedule - Show admin-created schedules
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#scheduleTable tbody');

    loadSchedule();

    function loadSchedule() {
        fetch('/api/trainer/schedule', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displaySchedule(data.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No schedule found</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading schedule</td></tr>';
            });
    }

    function displaySchedule(schedules) {
        if (schedules.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No schedule has been set yet. Contact admin to create your schedule.</td></tr>';
            return;
        }

        // Group by day of week
        const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const sortedSchedules = schedules.sort((a, b) => {
            const dayDiff = dayOrder.indexOf(a.day_of_week) - dayOrder.indexOf(b.day_of_week);
            if (dayDiff !== 0) return dayDiff;
            return a.start_time.localeCompare(b.start_time);
        });

        tbody.innerHTML = sortedSchedules.map(schedule => `
      <tr>
        <td><strong>${schedule.day_of_week}</strong></td>
        <td>${formatTime(schedule.start_time)} - ${formatTime(schedule.end_time)}</td>
        <td>${escapeHtml(schedule.activity)}</td>
        <td>${escapeHtml(schedule.location || '-')}</td>
        <td>${schedule.max_capacity}</td>
      </tr>
    `).join('');
    }

    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
