<?php
require_once __DIR__ . '/../database/session.php';
require_once __DIR__ . '/../database/server.php';

if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header('Location: ../mainpages/login.html');
    exit;
}

// Only the mentor role can view and respond to session requests.
if (($_SESSION['user']['role'] ?? '') !== 'mentor') {
    header('Location: ../student/student-homepage.html');
    exit;
}

$mentorId = filter_var($_SESSION['user']['id'] ?? null, FILTER_VALIDATE_INT);
if (!$mentorId || $mentorId < 1) {
    header('Location: ../mainpages/login.html');
    exit;
}

if (empty($_SESSION['mentor_requests_csrf_token'])) {
    $_SESSION['mentor_requests_csrf_token'] = bin2hex(random_bytes(32));
}

$statusError = '';
$requestLoadError = '';
$requests = [];
$allowedStatuses = ['accepted', 'declined'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrfToken)
        || !hash_equals($_SESSION['mentor_requests_csrf_token'], $csrfToken)
        || $requestId === false
        || $requestId === null
        || $requestId < 1
        || !in_array($status, $allowedStatuses, true)
    ) {
        http_response_code(400);
        $statusError = 'Unable to update the request. Please refresh the page and try again.';
    } else {
        try {
            // Scope the update to this mentor and pending requests to prevent unauthorized or repeated decisions.
            $update = $db->prepare(
                "UPDATE session_requests
                    SET status = ?
                  WHERE id = ?
                    AND mentor_id = ?
                    AND status = 'pending'"
            );
            $update->bind_param('sii', $status, $requestId, $mentorId);
            $update->execute();
            $updated = $update->affected_rows;
            $update->close();

            if ($updated !== 1) {
                http_response_code(409);
                $statusError = 'This request could not be updated. It may no longer be pending.';
            } else {
                header('Location: requests.php');
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            $statusError = 'The request could not be updated. Please try again later.';
        }
    }
}

try {
    // Load only requests assigned to the signed-in mentor.
    $query = $db->prepare(
        "SELECT sr.id, sr.session_datetime, sr.duration, sr.session_type, sr.status,
                CONCAT_WS(' ', s.first_name, s.last_name) AS student
           FROM session_requests sr
           JOIN studentstb s ON s.student_id = sr.student_id
          WHERE sr.mentor_id = ?
          ORDER BY sr.created_at DESC"
    );
    $query->bind_param('i', $mentorId);
    $query->execute();
    $result = $query->get_result();
    while ($request = $result->fetch_assoc()) {
        $requests[] = $request;
    }
    $query->close();
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    $requestLoadError = 'Your requests could not be loaded. Please try again later.';
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}
function formatTime($date) {
    return date('g:i A', strtotime($date));
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
    <body class="request-page">
        <header class="site-header">
            <a class="site-brand" href="../mainpages/index.html" aria-label="MentorshipMatch home">
                <img src="../Images/Logo/MentorshipMatchLogo.png" alt="MentorshipMatch logo" width="125" height="94">
            </a>
            <nav class="site-nav" aria-label="Mentor navigation">
                <a href="mentor-homepage.html">Dashboard</a>
                <a class="is-current" href="requests.php" aria-current="page">Requests</a>
                <a href="schedule.html">Schedule</a>
                <a href="profile.html">Profile</a>
                <a href="mentor-notifcations.html">Notifications</a>
            </nav>
        </header>

        <main class="request-box">
            <h1 class="page-title">Your Requests</h1>
            <?php if ($statusError !== ''): ?>
                <p class="request-error" role="alert"><?php echo htmlspecialchars($statusError, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($requestLoadError !== ''): ?>
                <p class="request-error" role="alert"><?php echo htmlspecialchars($requestLoadError, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <div class="sessions-request">
                <?php foreach ($requests as $request): ?>
                <div class="grid-item">

                    <div class="request-card-header">
                        <h3><?php echo htmlspecialchars($request['student'], ENT_QUOTES, 'UTF-8'); ?></h3>

                        <?php $displayStatus = ucfirst(strtolower($request['status'])); ?>
                        <span class="request-status status-<?php echo htmlspecialchars(strtolower($displayStatus), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($displayStatus, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>

                    <div class="request-details">
                        <p>
                            <strong>Date:</strong>
                            <?php echo htmlspecialchars(formatDate($request['session_datetime']), ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                        <p>
                            <strong>Time:</strong>
                            <?php echo htmlspecialchars(formatTime($request['session_datetime']), ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                        <p>
                            <strong>Duration:</strong>
                            <?php echo htmlspecialchars(formatDuration((int) $request['duration']), ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                        <p>
                            <strong>Type:</strong> <?php echo htmlspecialchars($request['session_type'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>

                    <?php if (strtolower($request['status']) === 'pending'): ?>
                        <!-- Decisions are available only while a request is pending. -->
                        <form class="request-actions" method="post" action="requests.php">
                            <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['mentor_requests_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php foreach ($allowedStatuses as $status): ?>
                                <button class="request-action" type="submit" name="status" value="<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8'); ?>
                                </button>
                            <?php endforeach; ?>
                        </form>
                    <?php endif; ?>
                </div>

            <?php endforeach; ?>

                <?php if ($requestLoadError === '' && empty($requests)): ?>
                    <p>No session requests yet.</p>
                <?php endif; ?>
            </div>
            
        </main>


        <footer class="home-footer">
        <a class="footer-logo" href="../mainpages/index.html"><img src="../Images/Logo/MentorshipMatchLogo.png" alt="MentorshipMatch logo" width="125" height="94"></a>
        <p>Helping students connect with guidance, experience, and opportunity.</p>
        <small>Chapter Name: Delaware Area Career Center</small><br>
        <small>Team Members: Joshua Berko, Thavael Noel, Shou Lin, Kyle Palermini</small><br>
        <small>Delaware, Ohio  2026-2027</small>
    </footer>
</body>
</html>
