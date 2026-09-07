// Admin dashboard browser code.
//
// Admin pages call protected endpoints for users, reports, and flagged
// activity. The backend must enforce the admin role on every request; hiding
// an admin link in the browser is not an access control system.
//
// Recommended safety steps:
// 1. Load data with GET requests after confirming the user is signed in.
// 2. Ask for confirmation before deleting or suspending an account.
// 3. Submit changes with PATCH or DELETE and refresh the table after success.
// 4. Display clear feedback when the server returns 401 or 403.
// 5. Record important administrative actions in an audit log.

