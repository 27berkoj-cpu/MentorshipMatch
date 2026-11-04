document.addEventListener('DOMContentLoaded', () => {
    // Find the student and mentor role selectors after the page is ready.
    const roles = document.querySelectorAll('input[name="role"]');

    roles.forEach(role => {
        role.addEventListener('change', () => {
            // Show the selected role's sign-in side when switching roles.
            const activeFlip = document.getElementById(`${role.id}-flip`);
            if (activeFlip) {
                activeFlip.checked = false;
            }
        });
    });
});