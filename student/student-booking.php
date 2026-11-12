<?php


require_once __DIR__ . '/../database/server.php';
require_once __DIR__ . '/../database/session.php';

if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'student') {
    header('Location: ../mainpages/login.html');
    exit;
}

$errors = [];
$mentors = [];
$mentorLoadError = '';
$bookingError = '';
$bookingSaved = false;
$emailSent = false;
$mentorId = '';
$sessionDate = '';
$duration = 0;
$sessionType = '';

// Load mentors once so the list can be displayed and submitted IDs can be checked.
try {
    $stmt = $mentorDb->query(
        'SELECT mentors_id AS id, first_name, last_name, feild AS field FROM mentorstb ORDER BY first_name, last_name'
    );
    $mentors = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $mentorLoadError = 'Mentor list could not be loaded. Please try again later.';
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Read the submitted values; the mentor ID is validated against the loaded records below.
    $mentorId = trim($_POST['mentor_id'] ?? '');
    $sessionDate = trim($_POST['sessiondate'] ?? '');
    $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
    $sessionType = trim($_POST['sessiontype'] ?? '');

    // A datetime-local value must be present and parse as a valid date/time.
    if (empty($sessionDate)) {
        $errors[] = "Session date and time is required.";
    } elseif (!strtotime($sessionDate)) {
        $errors[] = "Valid session date and time is required.";
    }

    if ($duration === false || $duration === null || !in_array($duration, [15, 20, 30, 45, 60, 90, 120], true)) {
        $errors[] = "Valid session duration is required.";
    }

    if (!in_array($sessionType, ['Video call', 'Chat Session'], true)) {
        $errors[] = "Session type is required.";
    }

    // Do not trust an ID just because it was submitted by the browser.
    $mentorIds = array_map('strval', array_column($mentors, 'id'));
    if ($mentorId === '' || !in_array($mentorId, $mentorIds, true)) {
        $errors[] = "Choose a valid mentor.";
    }

    if (empty($errors)) {
        $selectedMentor = null;
        foreach ($mentors as $mentor) {
            if ((string) $mentor['id'] === $mentorId) {
                $selectedMentor = $mentor;
                break;
            }
        }

        try {
            $insert = $studentDb->prepare(
                'INSERT INTO session_requests
                    (student_id, mentor_id, session_datetime, duration, session_type, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $insert->execute([
                $_SESSION['user']['id'],
                $mentorId,
                str_replace('T', ' ', $sessionDate),
                $duration,
                $sessionType,
            ]);
            $bookingSaved = true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $bookingError = 'Your session request could not be saved. Please try again later.';
        }

        if ($bookingSaved && $selectedMentor !== null) {
            $emailScript = __DIR__ . '/../web app features/email.py';
            $python = getenv('MENTORSHIPMATCH_PYTHON') ?: 'python';
            $command = escapeshellarg($python) . ' ' . escapeshellarg($emailScript) . ' '
                . escapeshellarg($_SESSION['user']['email']) . ' '
                . escapeshellarg(trim($selectedMentor['first_name'] . ' ' . $selectedMentor['last_name'])) . ' '
                . escapeshellarg($sessionDate) . ' '
                . escapeshellarg((string) $duration) . ' '
                . escapeshellarg($sessionType);
            $process = proc_open($command, [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ], $pipes);

            if (is_resource($process)) {
                fclose($pipes[0]);
                $emailOutput = stream_get_contents($pipes[1]);
                $emailError = stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                $emailSent = proc_close($process) === 0;
                if (!$emailSent) {
                    error_log(trim($emailError . ' ' . $emailOutput));
                }
            } else {
                error_log('Unable to start the Python process for the booking email.');
            }
        }
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
            <a class="site-brand" href="../mainpages/index.html" aria-label="MentorshipMatch home">
                <img src="../Images/Logo/MentorshipMatchLogo.png" alt="MentorshipMatch logo" width="125" height="93.75">
            </a>
            <nav class="site-nav" aria-label="Student navigation">
                <a href="student-homepage.html">Dashboard</a>
                <a href="mentors.html">Find mentors</a>
                <a class="is-current" href="student-booking.php" aria-current="page">Book a session</a>
                <a href="sessions.php">Sessions</a>
                <a href="messages.html">Messages</a>
                <a href="student-notfications.html">Notifications</a>
                <a href="student-reviews.html">Reviews</a>
                <a href="profile.html">Profile</a>
            </nav>
        </header>

        <form class="booking-form" method="POST" action="">
            <h1>Request a mentoring session</h1>

            <?php if ($mentorLoadError !== ''): ?>
                <div class="error-message" role="alert" style="color: #b00020; margin-bottom: 1rem; font-weight: bold;">
                    <p><?php echo htmlspecialchars($mentorLoadError, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="error-message" style="color: #b00020; margin-bottom: 1rem; font-weight: bold;">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($bookingError !== ''): ?>
                <div class="error-message" role="alert" style="color: #b00020; margin-bottom: 1rem; font-weight: bold;">
                    <p><?php echo htmlspecialchars($bookingError, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php elseif ($bookingSaved): ?>
                <div class="success-message" style="color: #0a7f3e; margin-bottom: 1rem; font-weight: bold;">
                    <p>Session request saved successfully.</p>
                    <?php if (!$emailSent): ?>
                        <p role="status">The request was saved, but the confirmation email could not be sent.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="booking-field">
               <label for="mentor_id">Choose a mentor</label>
               <select id="mentor_id" name="mentor_id" required>
                    <option value="" disabled <?php echo $mentorId === '' ? 'selected' : ''; ?>>Select a mentor</option>
                    <!-- Keep the chosen mentor selected if the form has validation errors. -->
                    <?php foreach ($mentors as $mentor): ?>
                        <?php
                            $fullName = trim($mentor['first_name'] . ' ' . $mentor['last_name']);
                            $field = trim($mentor['field'] ?? '');
                            $label = $field === '' ? $fullName : $fullName . ' - ' . $field;
                        ?>
                        <option value="<?php echo htmlspecialchars((string) $mentor['id'], ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $mentorId === (string) $mentor['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
               </select>
               <?php if ($mentorLoadError === '' && empty($mentors)): ?>
                   <p>No mentors are currently available.</p>
               <?php endif; ?>
            </div>
            <div class="booking-field">
                <label for="sessiondate">Session date and time</label>
                <input type="datetime-local" id="sessiondate" name="sessiondate"
                    value="<?php echo htmlspecialchars($sessionDate, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="booking-field">
                <label for="duration">Duration</label>
                <select id="duration" name="duration" required>
                    <option value="" disabled selected>Select a duration</option>
                    <option value="15">15 minutes</option>
                    <option value="20">20 minutes</option>
                    <option value="30">30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">1 hour</option>
                    <option value="90">1 hour and 30 minutes</option>
                    <option value="120">2 hours</option>
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
        <a class="footer-logo" href="../mainpages/index.html"><img src="../Images/Logo/MentorshipMatchLogo.png" alt="MentorshipMatch logo" width="125" height="93.75"></a>
        <p>Helping students connect with guidance, experience, and opportunity.</p>
        <small>Chapter Name: Delaware Area Career Center</small><br>
        <small>Team Members: Joshua Berko, Thavael Noel, Shou Lin, Kyle Palermini</small><br>
        <small>Delaware, Ohio  2026-2027</small>
    </footer>
</body>
</html>

<!-- student/student-booking.html will display the booking interface for students.
 It should show the selected mentor's availability and allow the student to choose a time slot and confirm the booking. -->