<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Booking</title>
            <link rel="stylesheet" href="../style/style.css">
        <link rel="stylesheet" href="../style/dashboard.css">
        <link rel="stylesheet" href="../style/navbar.css">
        <link rel="stylesheet" href="../style/forms.css">    </head>
    <body>


        <form action="/student/student-booking.php">
            <label for="sessiondate">Session Date and Time:</label>
            <input type="datetime-local" id="sessiondate" name="sessiondate"><br>
            <label for="duration"> Duration</label>
            <input type="number" id="duration" name="duration">


            <input type="submit"> 
        </form>

        <footer>
        <p>Chapter Name: [CHAPTER NAME]</p>
        <p>Team Members: Joshua Berko, Shou Lin, Thavael Noel, Kyle Palermini</p>
        <p>Theme: MentorshipMatch</p>
        <p>Delaware Area Career Center | Delaware, Ohio | 2026–2027</p>
    </footer> //add name of chapter,teammmebers names, theme, school,city,state year    
    </body>
</html>

<!-- student/student-booking.html will display the booking interface for students.
 It should show the selected mentor's availability and allow the student to choose a time slot and confirm the booking. -->