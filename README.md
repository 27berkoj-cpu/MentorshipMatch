# MentorshipMatch

Coding Languageds to know 
FRONTEND
HTML
CSS
JavaScript
      ↓
BACKEND
Node.js
Express.js
      ↓
DATABASE
PostgreSQL
      ↓
EXTERNAL SERVICES
APIs



Frontend — HTML

index.html will be the main landing page for MentorshipMatch. It should introduce the platform, explain what it does, provide navigation, and have buttons that direct users to log in or register. This gives users a clear starting point and helps with the overall layout, design, and writing-quality portions of the rubric.

login.html will handle user login. It should contain fields for an email or username and password, along with a login button and useful error messages. This is required because the BPA topic specifically requires user registration and login.

register.html will allow new users to create an account. It should collect information such as their name, email, password, and whether they are registering as a student or mentor. Depending on the account type, it can also collect academic interests, career goals, or professional information. This fulfills the user-registration requirement.

about.html will explain what MentorshipMatch is, why it exists, and how the mentorship process works. It isn't specifically required by the BPA topic, but it makes the application feel more complete and professional and gives you another opportunity to demonstrate strong writing, navigation, and design.

Student Pages

student/dashboard.html will be the main page students see after logging in. It should show things such as recommended mentors, upcoming sessions, pending mentor requests, notifications, and quick links to important parts of the application. This serves as the central hub for the student experience.

student/mentors.html will allow students to browse available mentors. It should include a search bar, filters, mentor cards, areas of expertise, career fields, and possibly availability. This is important because the scenario specifically requires students to be able to browse and request mentors.

student/mentor-profile.html will display an individual mentor's information. It should show their professional background, expertise, biography, reviews, availability, and a button to request them as a mentor. This gives students enough information to make an informed decision before sending a request.

student/sessions.html will manage a student's mentorship sessions. It should show upcoming and previous sessions, the mentor, date/time, session status, and options to book or cancel sessions. Session booking is explicitly listed as a required feature.

student/messages.html will provide the chat functionality between students and mentors. It should display conversations, messages, timestamps, and a message input box. This fulfills the communication aspect of the MentorshipMatch concept.

student/profile.html will allow students to view and edit their profiles. It should contain their academic interests, career goals, education information, biography, and profile visibility settings. Profile management is one of the required features.

Mentor Pages

mentor/dashboard.html will be the mentor's main dashboard. It should show pending student requests, upcoming sessions, notifications, and useful statistics. The mentor needs a dedicated interface rather than simply using the student interface.

mentor/requests.html will allow mentors to view incoming mentorship requests. It should provide information about the student and buttons to accept or decline requests. This connects the student request system to the mentor's side.

mentor/schedule.html will allow mentors to set their availability. Mentors should be able to create available time slots that students can use when booking sessions. This makes the session-booking system actually functional.

mentor/profile.html will contain the mentor's professional profile. It should include their profession, expertise, company or organization if applicable, biography, career experience, and availability. Students will see this information when browsing mentors.

Admin Pages

admin/dashboard.html will be the main administrative dashboard. It should provide an overview of the system, including the number of users, mentors, sessions, pending requests, and flagged activity. The BPA requirements specifically call for an admin panel to manage users and inappropriate activity.

admin/users.html will allow administrators to manage accounts. It should provide a searchable list of users and allow authorized administrators to add, modify, delete, or otherwise manage data. This directly supports the Admin Data Control and Admin Functions rubric categories.

admin/reports.html will generate reports using actual application data. For example, it could show the number of active users, mentorship requests, completed sessions, and reviews. The rubric specifically scores web-based reports and report accuracy.

admin/flagged.html will handle inappropriate activity that has been reported. It should show what was flagged, who reported it, why it was flagged, and allow an administrator to review and take appropriate action. This directly addresses the required admin functionality in the scenario.

CSS

css/style.css will contain the global styling for the entire application. It should define the basic fonts, spacing, buttons, colors, backgrounds, links, and other reusable styles. Having consistent global styling will help with the BPA categories for layout clarity, design aesthetics, writing presentation, and contrast.

css/navbar.css will control the navigation bar and/or sidebar. It should make navigation consistent across the application and provide responsive behavior for different screen sizes. This helps with the navigation and cross-platform portions of the rubric.

css/dashboard.css will style the student, mentor, and admin dashboards. It can contain the styling for statistic cards, grids, recent activity, recommendations, and other dashboard components.

css/forms.css will handle the appearance of login, registration, profile, booking, and other forms. It should include styling for labels, inputs, buttons, error messages, success messages, and focus states.

css/profiles.css will handle mentor and student profile layouts. It can style profile cards, biographies, skills, career information, reviews, and profile visibility controls.

css/sessions.css will style the session-booking interface, including calendars, available time slots, upcoming sessions, and session cards.

css/admin.css will style the administrative interface, including tables, reports, filters, buttons, warnings, and user-management controls.

css/responsive.css will contain media queries that make the application work properly on different screen sizes. This is particularly useful for the BPA Cross-Browser/Platform Compatibility category.

JavaScript

js/auth.js will handle authentication-related functionality. It will communicate with the backend when users log in, register, or log out and help maintain their authentication state. This supports the required authentication and password-protection functionality.

js/mentors.js will control the mentor browsing page. It should retrieve mentor information from the backend, display mentor cards, and work with filtering and searching.

js/profile.js will handle viewing and editing user profiles. It should retrieve profile information from the database and send changes back to the server.

js/requests.js will manage mentorship requests. Students should be able to send requests, while mentors should be able to accept or decline them.

js/sessions.js will handle the session-booking system. It should retrieve available times, allow students to select a time, submit the booking, and display the resulting session.

js/messages.js will handle the chat system. It should retrieve messages, display them, and send new messages to the backend.

js/reviews.js will handle reviews after mentorship sessions. Students or mentors should be able to submit reviews, and appropriate reviews should appear on profiles.

js/admin.js will handle interactions inside the admin panel, including adding, modifying, deleting, and managing application data.

js/validation.js will handle client-side form validation. It should check things such as required fields, valid email addresses, acceptable inputs, and other constraints before sending information to the server. The BPA rubric specifically evaluates form validation, including client-side and server-side validation.

js/notifications.js will handle notifications such as new mentorship requests, accepted requests, upcoming sessions, and new messages. This is an optional enhancement mentioned in the BPA scenario and could make the application feel significantly more complete.

js/search.js will handle the search functionality. Students should be able to search for mentors and receive relevant results. Search usability is directly scored in the technical rubric.

Backend

server/server.js will be the main entry point for the backend. It will start the Express server, connect the application's middleware, connect to the database, register the API routes, serve the appropriate resources, and handle errors. This is necessary because BPA requires server-side scripting and a functional full-stack application.

The routes/ folder will contain files such as authRoutes.js, userRoutes.js, mentorRoutes.js, sessionRoutes.js, messageRoutes.js, reviewRoutes.js, and adminRoutes.js. These files define the API endpoints that the frontend communicates with. For example, mentorRoutes.js could contain endpoints for retrieving mentors, while sessionRoutes.js could contain endpoints for creating and cancelling sessions.

The controllers/ folder will contain the actual backend logic. For example, mentorController.js would determine how mentor information is retrieved from the database, while sessionController.js would handle creating and cancelling sessions. Separating routes from controllers makes the code easier to understand and maintain.

The middleware/ folder will contain security and validation logic. authMiddleware.js should ensure that users are logged in before accessing protected functionality. adminMiddleware.js should ensure that only administrators can access administrative functions. validationMiddleware.js should validate incoming data on the server. Finally, errorMiddleware.js should catch and handle errors without allowing the application to crash. These files directly support the BPA rubric's categories for access control, validation, error handling, and security.

Database

database/schema.sql will define the structure of your database. It should create tables for users, student profiles, mentor profiles, mentorship requests, sessions, messages, reviews, and flagged activity. Each table should use appropriate primary and foreign keys so the relationships between the tables are clear.

database/seed.sql can contain sample data for development and testing. For example, you could have sample mentors, students, sessions, and reviews so your team can test the application without manually creating everything each time.

database/queries.sql can contain important SQL queries used throughout development. This is also useful for documenting how your application retrieves and modifies information.

The database is one of the most important parts of the project. BPA specifically requires a database and prohibits flat files for data storage. The rubric evaluates database use, conceptual modeling, redundancy, encryption, access control, unique keys, and queries/procedures.











44
server/
│
├── server.js
│
├── routes/
│   ├── authRoutes.js
│   ├── userRoutes.js
│   ├── mentorRoutes.js
│   ├── sessionRoutes.js
│   ├── messageRoutes.js
│   ├── reviewRoutes.js
│   └── adminRoutes.js
│
├── controllers/
│   ├── authController.js
│   ├── mentorController.js
│   ├── sessionController.js
│   ├── messageController.js
│   └── adminController.js
│
├── middleware/
│   ├── authMiddleware.js
│   ├── adminMiddleware.js
│   ├── validationMiddleware.js
│   └── errorMiddleware.js
│
└── database/
    └── db.js
