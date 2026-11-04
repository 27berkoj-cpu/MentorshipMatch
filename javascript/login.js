document.addEventListener('DOMContentLoaded', () => {
    const flipCheckboxes = document.querySelectorAll('.sync-checkbox');
    const roles = document.querySelectorAll('input[name="role"]');
    let isSignUp = false;

    // Keep both role cards on the same side of the login/sign-up card.
    const syncFlipState = () => {
        flipCheckboxes.forEach(checkbox => {
            checkbox.checked = isSignUp;
        });
    };

    flipCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            isSignUp = checkbox.checked;
            syncFlipState();
        });
    });

    roles.forEach(role => {
        role.addEventListener('change', syncFlipState);
    });
});
