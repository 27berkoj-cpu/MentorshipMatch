const checkboxes = document.querySelectorAll('.sync-checkbox');
checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        checkboxes.forEach(other => {
            other.checked = checkbox.checked;
        });
    });
});