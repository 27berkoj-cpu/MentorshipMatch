# Mentor Request and Connection Flow

The student mentor directory and mentor requests page are currently static placeholders. Before implementing them, agree on the behavior and database design, then build and test each side of the flow.

## Suggested implementation order

1. **Confirm the request lifecycle.** Define what happens when a student requests a mentor, when the mentor accepts or declines, whether a declined request can be sent again, and how both users see the connection status.
2. **Design the MySQL schema.** The current PHP app connects to MySQL through `database/server.php`, while the general database guide describes a possible PostgreSQL design. Choose the active database target and document it consistently. Create a migration/schema for mentor requests with student and mentor IDs, pending/accepted/declined status, and timestamps. Match ID types to the existing `studentstb` and `mentorstb` primary keys; add foreign keys and indexes where supported.
3. **Implement student browsing.** Load mentor names and career fields from the database, add useful search/filtering, show the current request status, and provide a request action. Do not rely on browser-submitted mentor IDs without checking the database.
4. **Implement mentor review.** Load only requests addressed to the signed-in mentor. Show relevant student information and pending status, and provide accept/decline actions.
5. **Protect all actions server-side.** Require the correct signed-in role, verify request ownership, validate IDs and status transitions, use parameterized SQL, and include CSRF protection. Decide how to prevent duplicate active requests and handle repeated submissions.
6. **Keep navigation consistent.** Convert the static placeholder pages to server-rendered pages and update links only once the final routes are in place.
7. **Test both roles end to end.** Verify request creation, pending display, accept/decline, access denial for the wrong role or owner, invalid IDs, duplicate submissions, empty states, and database errors using separate student and mentor accounts.

## Keep authentication separate

The request flow should use the existing authenticated session and should not need changes to the signup/login branches in `database/auth.php`. Keep the student and mentor paths explicit, so adding an admin role later does not accidentally expose mentor-request actions to administrators.

## Database setup convenience

Prefer a checked-in schema file or migration that can be run with the project setup instructions. Avoid silently creating or altering application tables during normal page requests; setup should report clearly if the schema has not been installed or the database user lacks permissions.
