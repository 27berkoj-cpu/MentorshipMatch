# Backend Guide

This folder will contain the Node.js and Express server for MentorshipMatch.

## What the backend does

The backend is the part of the application that runs on the server. It:

1. Receives requests from the HTML and JavaScript frontend.
2. Checks that the request is valid and the user is allowed to make it.
3. Reads or changes data in PostgreSQL.
4. Sends a JSON response back to the browser.
5. Handles errors without exposing private information.

The browser should never connect directly to PostgreSQL. The browser talks to Express, and Express talks to PostgreSQL.

## Recommended build order

1. Install Node.js and PostgreSQL.
2. Create `package.json` and install `express`, `pg`, `dotenv`, `bcrypt`, and `express-session`.
3. Create a `.env` file for the database connection and session secret. Never commit `.env`.
4. Build `database/schema.sql` and run it in PostgreSQL.
5. Add `database/db.js` with one shared PostgreSQL connection pool.
6. Add the health-check route in `server.js` and confirm the server starts.
7. Add authentication: register, login, logout, password hashing, and session state.
8. Add protected routes for profiles, mentors, requests, sessions, and messages.
9. Add admin-only routes for users, reports, and flagged content.
10. Connect the frontend scripts with `fetch()` calls.
11. Test successful requests, invalid input, unauthorized access, and database errors.

## Basic request flow

```text
HTML form or button
        |
        v
frontend JavaScript fetch('/api/...')
        |
        v
Express route
        |
        v
validation and authentication middleware
        |
        v
controller or route handler
        |
        v
parameterized PostgreSQL query
        |
        v
JSON response to the browser
```

## Important security rules

- Store passwords with `bcrypt`; never store the original password.
- Use parameterized SQL queries such as `$1`, `$2`, and `$3`; never build SQL with string concatenation.
- Use HTTPS when deployed.
- Keep database passwords and session secrets in environment variables.
- Check authorization on the server. Hiding a button in HTML is not security.
- Validate input on both the browser and the server.
- Do not return password hashes or private profile fields in API responses.
- Use role checks for student, mentor, and admin actions.
- Add generic error responses for users and detailed errors only in server logs.

## Suggested API routes

| Method | Route | Purpose | Access |
| --- | --- | --- | --- |
| `POST` | `/api/auth/register` | Create an account | Public |
| `POST` | `/api/auth/login` | Start a session | Public |
| `POST` | `/api/auth/logout` | End a session | Signed in |
| `GET` | `/api/mentors` | Search mentors | Signed in |
| `GET` | `/api/mentors/:id` | View a mentor profile | Signed in |
| `PUT` | `/api/profile` | Update the current profile | Signed in |
| `POST` | `/api/requests` | Request a mentor | Student |
| `PATCH` | `/api/requests/:id` | Accept or decline a request | Mentor |
| `POST` | `/api/sessions` | Book a session | Student |
| `DELETE` | `/api/sessions/:id` | Cancel a session | Participant |
| `GET` | `/api/messages/:userId` | Load a conversation | Conversation members |
| `POST` | `/api/messages` | Send a message | Signed in |
| `GET` | `/api/admin/reports` | View application reports | Admin |
| `PATCH` | `/api/admin/users/:id` | Change user status or role | Admin |
| `GET` | `/api/admin/flagged` | Review flagged activity | Admin |

## How to test each route

For every route, record:

- The HTTP method and URL.
- The required login role.
- The expected request body.
- A successful response status and example JSON.
- Invalid input behavior.
- Unauthorized and forbidden behavior.
- What database query is executed.

The team can use the browser, VS Code REST Client, Postman, or `curl` to test routes. The final submission should show that the frontend and server work together.
