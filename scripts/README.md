# Frontend JavaScript and API Guide

The files in this folder run in the browser. They should handle user interaction and call the backend with `fetch()`; they should not connect directly to PostgreSQL.

## Responsibilities

- `auth.js`: register, log in, log out, and display authentication feedback.
- `mentors.js`: search and display mentor results from the API.
- `messages.js`: load conversations and send messages.
- `sessions.js`: load availability, book sessions, and cancel sessions.
- `admin.js`: call protected admin routes and refresh tables or reports.

## Standard fetch pattern

```js
const response = await fetch('/api/mentors');
const data = await response.json();

if (!response.ok) {
  throw new Error(data.error || 'The request failed.');
}

// Render data only after the server confirms success.
```

For a request with JSON data, include `method`, `headers`, and `body`:

```js
const response = await fetch('/api/requests', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ mentorId })
});
```

## Every frontend request should

1. Disable or protect the button while a request is in progress.
2. Check `response.ok`.
3. Show a useful success or error message.
4. Never display raw server errors or sensitive data.
5. Update the page after the server confirms the database change.
6. Handle a `401` response by sending the user to the login page.
7. Handle a `403` response by explaining that the account lacks permission.

Client-side validation improves usability, but server-side validation is still required because browser code can be bypassed.
