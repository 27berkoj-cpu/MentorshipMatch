document.addEventListener('DOMContentLoaded', () => {
    const flipCheckboxes = document.querySelectorAll('.sync-checkbox');
    const roles = document.querySelectorAll('input[name="role"]');
    const message = document.getElementById('auth-message');
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

    const params = new URLSearchParams(window.location.search);
    const role = params.get('role');
    const mode = params.get('mode');
    const errorMessages = {
        invalid_request: 'Please choose a valid account type and try again.',
        invalid_email: 'Enter a valid email address.',
        invalid_signup: 'Enter your name, a valid mentor field if applicable, and a password.',
        email_exists: 'An account with that email already exists.',
        account_error: 'We could not process your account. Please try again.',
        invalid_credentials: 'The email or password is incorrect.'
    };

    if (role === 'mentor' || role === 'student') {
        document.getElementById(role).checked = true;
    }
    if (mode === 'signup') {
        isSignUp = true;
        syncFlipState();
    }
    if (message && errorMessages[params.get('error')]) {
        message.textContent = errorMessages[params.get('error')];
        message.hidden = false;
        // Funciton to make error message go away after 8 seconds.
        window.setTimeout(() => {
            message.hidden = true;
        }, 8000);
    }
});
