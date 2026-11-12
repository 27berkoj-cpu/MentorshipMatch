<?php
require_once __DIR__ . '/../database/server.php';
require_once __DIR__ . '/../database/session.php';

// Making sure only students are on the page.
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'student') {
    header('Location: ../mainpages/login.html');
    exit;
}

//temporary sample data until we connect the file to the database
$requests = [
    [
    'student' => 'Joshua Berko',
    'datetime' => '2026-11-12 15:00:00',  
    'duration' => 30,
    'type' => 'Video call',
    'status' => 'Pending'
    ],
    [
        'student' => 'Thavael Noel',
        'datetime' => '2026-11-14 16:00:00',
        'duration' => 45,
        'type' => 'Chat Session',
        'status' => 'Accepted'
    ],
    [
        'student' => 'Terry Lin',
        'datetime' => '2026-11-16 14:00:00',
        'duration' => 60,
        'type' => 'Video call',
        'status' => 'Accepted'
    ]
];
function formatDate($date) {
    return date('F j, Y', strtotime($date));
    // Formats date in "Month Day, Year" format
}
function formatTime($date) {
    return date('g:i A', strtotime($date)); 
    // Formats time in 12-hour format with AM/PM
}
function formatDuration($minutes) {
    $durations = [
        15 => '15 minutes',
        20 => '20 minutes',
        30 => '30 minutes',
        45 => '45 minutes',
        60 => '1 hour',
        90 => '1 hour and 30 minutes',
        120 => '2 hours',
    ];

    return $durations[$minutes] ?? $minutes . ' minutes';
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mentor Requests</title>
            <link rel="stylesheet" href="../style/style.css">
        <link rel="stylesheet" href="../style/dashboard.css">
        <link rel="stylesheet" href="../style/navbar.css">
        <link rel="stylesheet" href="../style/forms.css"> 
        <link rel="stylesheet" href="../style/mentors-request.css">   
        <link rel="icon" href="../Images/Logo/MentorshipMatchLogo.png" type="image/png"> 
    </head>
    <body>
        <header class="site-header">
            <a class="site-brand" href="../mainpages/index.html" aria-label="MentorshipMatch home">
                <img src="../Images/Logo/MentorshipMatchLogo.png" alt="MentorshipMatch logo" width="125" height="93.75">
            </a>
            <nav class="site-nav" aria-label="Mentor navigation">
                <a href="mentor-homepage.html">Dashboard</a>
                <a class="is-current" href="requests.html" aria-current="page">Requests</a>
                <a href="schedule.html">Schedule</a>
                <a href="profile.html">Profile</a>
                <a href="mentor-notifcations.html">Notifications</a>
            </nav>
        </header>

        <main class="request-box">
            <h1 class="page-title">Your Requests</h1>
                <?php foreach ($requests as $request) ?>
                <div class="grid-item">


                <div class="grid-item">

                    <div class="session-card-header">
                        <h3><?php echo htmlspecialchars($session['mentor']); ?></h3>

                        <span class="session-status">
                            <?php echo htmlspecialchars($session['status']); ?>
                        </span>
                    </div>

                    <div class="session-details">
                        <p>
                            <strong>Date:</strong>
                            <?php echo htmlspecialchars(formatDate($session['datetime'])); ?>
                        </p>

                        <p>
                            <strong>Time:</strong>
                            <?php echo htmlspecialchars(formatTime($session['datetime'])); ?>
                        </p>

                        <p>
                            <strong>Duration:</strong>
                            <?php echo htmlspecialchars(formatDuration((int) $session['duration'])); ?>
                        </p>

                        <p>
                            <strong>Type:</strong> <?php echo htmlspecialchars($session['type']); ?>
                        </p>
                    </div>
                </div>

            <?php endforeach; ?>

        
                </div>
            
        </main>


        <footer class="home-footer">
        <a class="footer-logo" href="../mainpages/index.html"><img src="../Images/Logo/MentorshipMatchLogo.png" alt="MentorshipMatch logo" width="125" height="93.75"></a>
        <p>Helping students connect with guidance, experience, and opportunity.</p>
        <small>Chapter Name: Delaware Area Career Center</small><br>
        <small>Team Members: Joshua Berko, Thavael Noel, Shou Lin, Kyle Palermini</small><br>
        <small>Delaware, Ohio  2026-2027</small>
    </footer>
</body>
</html>

<!-- mentor/requests.html will allow mentors to view incoming mentorship requests.
  It should provide information about the student and buttons to accept or decline requests. 
 This connects the student request system to the mentor's side. -->