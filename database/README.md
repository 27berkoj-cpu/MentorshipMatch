# Database Guide

MentorshipMatch must use PostgreSQL for persistent application data. HTML files, JavaScript variables, and JSON files are not a replacement for the database.

## Recommended tables

- `users`: login identity, role, password hash, and account status.
- `student_profiles`: student interests, education, goals, biography, and visibility.
- `mentor_profiles`: profession, expertise, organization, biography, and visibility.
- `mentor_requests`: student requests and mentor decisions.
- `availability`: time slots offered by mentors.
- `sessions`: booked mentorship meetings.
- `messages`: chat messages between users.
- `reviews`: ratings and written feedback after sessions.
- `flagged_activity`: content reported for admin review.
- `audit_log`: important admin actions and timestamps.

## Relationships

- One user has one role and may have one student profile or one mentor profile.
- A student can send many mentor requests.
- A mentor can receive many mentor requests.
- An accepted request can lead to many sessions.
- A session has a student, a mentor, and optionally one review.
- A conversation contains messages sent by users.
- A user can report flagged activity; an admin can review it.

Use foreign keys for these relationships. Foreign keys stop orphaned records, such as a session pointing to a user who does not exist.

## Database design checklist

- Give every table a primary key.
- Give every user a unique email address.
- Store `password_hash`, never a plain password.
- Use `NOT NULL` for required fields.
- Use `CHECK` constraints for roles and statuses.
- Use timestamps such as `created_at` and `updated_at`.
- Add indexes to columns used often in search or joins.
- Use `ON DELETE` rules deliberately; do not delete related data accidentally.
- Avoid repeating the same user name, email, or profile data in many tables.

## Running SQL

A typical local workflow is:

```text
1. Create a PostgreSQL database named mentorship_match.
2. Run schema.sql to create tables.
3. Run seed.sql to add safe sample data.
4. Connect the Express server with environment variables.
5. Run SELECT queries to verify the tables and relationships.
```

The exact command depends on the team computer and PostgreSQL installation. Keep real passwords out of this repository.

## Query safety

Always use parameterized queries in Node.js:

```js
const result = await pool.query(
  'SELECT id, name, role FROM users WHERE email = $1',
  [email]
);
```

Never do this:

```js
const result = await pool.query(
  `SELECT * FROM users WHERE email = '${email}'`
);
```

The second example allows SQL injection because user input is inserted directly into the SQL text.

## Conceptual model to explain to judges

The database separates identity from profile details, connects users through requests and sessions, and keeps communication and moderation data in their own tables. This reduces redundancy and makes it possible to enforce access control with foreign keys and server-side role checks.
