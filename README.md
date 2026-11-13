# MentorshipMatch

MentorshipMatch is a web app prototype for connecting students with mentors. The project is being developed for the Delaware Area Career Center BPA team. It includes a public site, student and mentor page layouts, admin page layouts, and early PHP/database experiments.

## Project status

The site is currently a **work in progress**, not a complete full-stack application. The HTML pages and styles provide the primary interface, but most user-facing features are not connected to persistent application data.

- Login and sign-up forms are UI prototypes; authentication and account creation are not working end to end.
- The student booking page saves session requests to SQLWorkbench and attempts to send a confirmation email through Gmail SMTP.
- The mentor, student, and admin areas are page prototypes; role-based access and most actions are not implemented.
- `database/schema.sql` and `database/seed.sql` contain planning comments, not runnable table definitions or sample data.
- The PHP database experiments use PDO with MySQL, while the database guide and checklist describe a planned PostgreSQL design. These are not yet aligned.
- Some links and PHP integrations are incomplete. In particular, the landing page links to a sign-up page that is not present in the repository.

Use the [BPA project checklist](BPA-CHECKLIST.md) to track the planned requirements and testing still needed.

## Run locally

The static pages can be opened in a browser, but use Apache to run PHP pages. With XAMPP and this repository under `C:\xampp\htdocs\adp2\MentorshipMatch`, start Apache and visit:

- Public landing page: <http://localhost/adp2/MentorshipMatch/mainpages/index.html>
- Student booking prototype: <http://localhost/adp2/MentorshipMatch/student/student-booking.php>

The booking form requires Apache/PHP, a configured MySQL database named `mentorshipmatch`, and the `studentstb`, `mentorstb`, and `session_requests` tables expected by the PHP code. The repository does not currently provide a runnable schema or seed data, so use a database prepared for the existing PHP code.

## Booking email setup

Booking is handled entirely by PHP on the server. After a successful database insert, PHP launches `web app features/email.py`; Python sends the message using the server's Gmail SMTP configuration. No email credentials belong in browser code.

Configure these variables in the environment inherited by Apache (for XAMPP on Windows, for example, use Apache `SetEnv` directives and restart Apache). `.env.example` is a list of the expected variable names and sample values; PHP and Python do not load a `.env` file automatically.

- `MENTORSHIPMATCH_SMTP_USERNAME`: the Gmail account used to send confirmations.
- `MENTORSHIPMATCH_SMTP_PASSWORD`: that account's Google App Password. Keep it private and out of Git.
- `MENTORSHIPMATCH_PYTHON`: Python executable visible to Apache, such as `C:/Python314/python.exe`; defaults to `python`.
- `MENTORSHIPMATCH_DB_HOST`, `MENTORSHIPMATCH_DB_NAME`, `MENTORSHIPMATCH_DB_USER`, `MENTORSHIPMATCH_DB_PASSWORD`: MySQL connection settings. Defaults are `localhost`, `mentorshipmatch`, `root`, and an empty password.

The PHP runtime must be version 7.4 or newer with `proc_open` enabled. If the booking is saved but the email process fails, the page reports that separately; configure the server and submit a new request to test email delivery.

## Pages and features

- **Public:** landing page, about page, contact page, and login/sign-up interface in `mainpages/`.
- **Student:** dashboard, mentor list and profile, profile, sessions, messages, notifications, reviews, and booking page in `student/`.
- **Mentor:** dashboard, profile, requests, schedule, and notifications in `mentor/`.
- **Admin:** dashboard, user management, reports, and flagged activity pages in `admin/`.
- **Shared assets:** stylesheets in `style/`, browser scripts in `javascript/`, and branding/hero images in `Images/`.

These pages indicate the intended product areas; their presence does not mean each feature is functional or backed by a database.

## Repository layout

```text
admin/                Admin page prototypes
database/             PHP database experiments and SQL planning files
Images/               Logo and page imagery
javascript/           Browser-side JavaScript
mainpages/            Public-facing pages
mentor/               Mentor page prototypes
Servers/              Backend planning guide
student/              Student pages and booking prototype
style/                CSS stylesheets
web app features/     Search feature experiment
BPA-CHECKLIST.md      BPA requirements and project checklist
```

## Technology

- HTML and CSS for page structure and styling
- JavaScript for browser interactions and page animations
- PHP for server-side booking, database operations, and launching the email process
- Python's standard library for Gmail SMTP email delivery
- PDO/MySQL in the current PHP database connection code

There is no Node.js package manifest or working Express API in the repository at this time. The [backend guide](Servers/README.md) and [database guide](database/README.md) contain proposed implementation guidance and should be updated as the backend architecture is finalized.
