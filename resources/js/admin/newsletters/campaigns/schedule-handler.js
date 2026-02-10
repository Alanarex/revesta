/**
 * Newsletter Schedule Form Handler
 * Handles date/time display formatting on the schedule page
 */

document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('scheduled_date');
    const timeInput = document.getElementById('scheduled_time');
    const displayElement = document.getElementById('scheduledDateDisplay');

    if (!dateInput || !timeInput || !displayElement) return;

    function updateScheduledDateDisplay() {
        const dateValue = dateInput.value;
        const timeValue = timeInput.value;

        if (dateValue && timeValue) {
            const date = new Date(dateValue + 'T' + timeValue);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const formattedDate = date.toLocaleDateString('fr-FR', options);
            displayElement.textContent = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
        } else {
            displayElement.textContent = '-';
        }
    }

    dateInput.addEventListener('change', updateScheduledDateDisplay);
    timeInput.addEventListener('change', updateScheduledDateDisplay);

    // Initialize on page load
    updateScheduledDateDisplay();
});

