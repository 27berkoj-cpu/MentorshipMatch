<?php

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
        <link rel="stylesheet" href="../style/forms.css?v=1">    </head>
    <body>


        <form class="booking-form" action="/student/student-booking.php">
            <h1>Book a mentoring session</h1>
            <div class="booking-field">
                <label for="sessiondate">Session date and time</label>
                <input type="datetime-local" id="sessiondate" name="sessiondate">
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
            <input type="submit" value="Confirm booking">
        </form>

        <footer>
        <p>Chapter Name: [CHAPTER NAME]</p>
        <p>Team Members: Joshua Berko, Shou Lin, Thavael Noel, Kyle Palermini</p>
        <p>Theme: MentorshipMatch</p>
        <p>Delaware Area Career Center | Delaware, Ohio | 2026–2027</p>
    </footer> 
    <!-- add name of chapter,teammmebers names, theme, school,city,state year     -->
    </body>
</html>

<!-- student/student-booking.html will display the booking interface for students.
 It should show the selected mentor's availability and allow the student to choose a time slot and confirm the booking. -->