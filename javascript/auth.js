// Authentication browser code.
//
// This file should submit registration and login forms to the Express API.
// The server, not this file, must verify passwords and decide whether a user
// is allowed to log in. Never put a database password or a password hash in
// browser JavaScript.
//
// Recommended flow:
// 1. Listen for the form submit event and call event.preventDefault().
// 2. Validate required fields and show a useful message beside the form.
// 3. Send JSON to /api/auth/register or /api/auth/login with fetch().
// 4. Check response.ok before treating the request as successful.
// 5. Redirect to the correct dashboard after login.
// 6. Provide a logout action that calls /api/auth/logout.

