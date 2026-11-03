<?php

$errors = [];
$student_id = 0;
$mentor_id = 0;
$sessionDate = '';
$duration = 0;
$sessionType = '';
$mentorName = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // get the form data
    $student_id = trim($_POST['student_id'] ?? '');
    $mentor_id = trim($_POST['mentor_id'] ?? '');
    $mentorName = trim($_POST['mentorname'] ?? '');
    $sessionDate = trim($_POST['sessiondate'] ?? '');
    $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
    $sessionType = trim($_POST['sessiontype'] ?? '');

    // user is not entering student/mentor IDs yet, so don't require them yet
    // datetime-local sends a value like 2026-09-29T15:30, which should be stored directly

    if (empty($sessionDate)) {
        $errors[] = "Session date and time is required.";
    } elseif (!strtotime($sessionDate)) {
        $errors[] = "Valid session date and time is required.";
    }

    if ($duration === false || $duration === null || !in_array($duration, [15, 20, 30, 45, 60, 90, 120], true)) {
        $errors[] = "Valid session duration is required.";
    }

    if (empty($sessionType)) {
        $errors[] = "Session type is required.";
    }
    if ($mentorName === '') {
        $errors[] = "Choosing a mentor is required.";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Booking</title>
        <link rel="stylesheet" href="../style/style.css">
        <link rel="stylesheet" href="../style/dashboard.css">
        <link rel="stylesheet" href="../style/navbar.css">
        <link rel="stylesheet" href="../style/forms.css?v=1">    
        <link rel="icon" href="../Images/Logo/MentorshipMatchLogo.png" type="image/png"> 
    </head>
    <body>
        <header class="site-header">
            <a class="site-brand" href="../mainpages/index.html">Mentorship<span>Match</span></a>
            <nav class="site-nav" aria-label="Student navigation">
                <a href="student-homepage.html">Dashboard</a>
                <a href="mentors.html">Find mentors</a>
                <a class="is-current" href="sessions.html" aria-current="page">Sessions</a>
                <a href="messages.html">Messages</a>
                <a href="profile.html">Profile</a>
            </nav>
        </header>

        <form class="booking-form" method="POST" action="">
            <h1>Request a mentoring session</h1>

            <?php if (!empty($errors)): ?>
                <div class="error-message" style="color: #b00020; margin-bottom: 1rem; font-weight: bold;">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)): ?>
                <div class="success-message" style="color: #0a7f3e; margin-bottom: 1rem; font-weight: bold;">
                    <p>Booking request submitted successfully.</p>
                </div>
            <?php endif; ?>

            <div class="booking-field">
               <label for="mentorname">Choose a mentor</label> <!-- Replace with the real mentors names when the database is set up. For now, this is a placeholder.-->
               <select id="mentorname" name="mentorname" required>
                    <option value="" disabled selected>Select a mentor</option>
                    <option value="Liam Anderson">Liam Anderson</option>
                    <option value="Ava Mitchell">Ava Mitchell</option>
                    <option value="Noah Bennett">Noah Bennett</option>
                    <option value="Sophia Carter">Sophia Carter</option>
                    <option value="Ethan Brooks">Ethan Brooks</option>
                    <option value="Isabella Hayes">Isabella Hayes</option>
                    <option value="Mason Parker">Mason Parker</option>
                    <option value="Olivia Reed">Olivia Reed</option>
                    <option value="Lucas Morgan">Lucas Morgan</option>
                    <option value="Mia Thompson">Mia Thompson</option>
                    <option value="Caleb Foster">Caleb Foster</option>
                    <option value="Harper Collins">Harper Collins</option>
                    <option value="Elijah Turner">Elijah Turner</option>
                    <option value="Chloe Ramirez">Chloe Ramirez</option>
                    <option value="Jackson Blake">Jackson Blake</option>
                   <option value="" disabled selected>Select a mentor</option>
                   <!-- Options for mentors would be populated here -->
               </select>
            </div>
            <div class="booking-field">
                <label for="sessiondate">Session date and time</label>
                <input type="datetime-local" id="sessiondate" name="sessiondate" required>
            </div>
            <div class="booking-field">
                <label for="duration">Duration</label>
                <select id="duration" name="duration" required>
                    <option value="" disabled selected>Select a duration</option>
                    <option value="15">15 min</option>
                    <option value="20">20 min</option>
                    <option value="30">30 min</option>
                    <option value="45">45 min</option>
                    <option value="60">1 hr</option>
                    <option value="90">1 hr and 30 min</option>
                    <option value="120">2 hr</option>
                </select>
            </div>
            <div class="booking-field">
                <label for="sessiontype">Session type</label>
                <select id="sessiontype" name="sessiontype" required>
                    <option value="" disabled selected>Select a session type</option>
                    <option value="Video call">Video call</option>
                    <option value="Chat Session">Chat Session</option>
                </select>
            </div>
            <input type="submit" value="Request session">
        </form>

        <footer class="home-footer">
        <a class="footer-logo" href="index.html"><img src="../Images/Logo/MentorshipMatchLogo.png" alt="MentorshipMatch logo" width="125" height="93.75"></a>
        <p>Helping students connect with guidance, experience, and opportunity.</p>
        <small>Chapter Name: Delaware Area Career Center</small><br>
        <small>Team Members: Joshua Berko, Thavael Noel, Shou Lin, Kyle Palermini</small><br>
        <small>Delaware, Ohio  2026-2027</small>
    </footer>
</body>
</html>

<!-- student/student-booking.html will display the booking interface for students.
 It should show the selected mentor's availability and allow the student to choose a time slot and confirm the booking. -->