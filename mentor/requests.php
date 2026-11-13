<?php
require_once __DIR__ . '/../database/session.php';

// Making sure only mentors are on the page.
// if (empty($_SESSION['user'])) {
//     header('Location: ../mainpages/login.html');
//     exit;
// }
// elseif (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') == 'student'){
//     header('Location: ../student/student-homepage.html');
//     exit;
// }

// Temporary sample data until requests are connected to the database.
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
        'status' => 'Pending'
    ],
    [
        'student' => 'Terry Lin',
        'datetime' => '2026-11-16 14:00:00',
        'duration' => 60,
        'type' => 'Video call',
        'status' => 'Pending'
    ],
    [
        'student' => 'Kyle Palermini',
        'datetime' => '2026-11-18 10:00:00',
        'duration' => 30,
        'type' => 'Video call',
        'status' => 'Pending'
    ]
];

$allowedStatuses = ['Accepted', 'Declined', 'Completed'];
if (!isset($_SESSION['mentor_request_statuses']) || !is_array($_SESSION['mentor_request_statuses'])) {
    $_SESSION['mentor_request_statuses'] = array_fill(0, count($requests), 'Pending');
}
if (empty($_SESSION['mentor_requests_csrf_token'])) {
    $_SESSION['mentor_requests_csrf_token'] = bin2hex(random_bytes(32));
}

$statusError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestIndex = filter_input(INPUT_POST, 'request_index', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrfToken)
        || !hash_equals($_SESSION['mentor_requests_csrf_token'], $csrfToken)
        || $requestIndex === false
        || $requestIndex === null
        || !array_key_exists($requestIndex, $requests)
        || !in_array($status, $allowedStatuses, true)
    ) {
        http_response_code(400);
        $statusError = 'Unable to update the request. Please refresh the page and try again.';
    } else {
        $_SESSION['mentor_request_statuses'][$requestIndex] = $status;
        header('Location: requests.php');
        exit;
    }
}

foreach ($requests as $index => &$request) {
    $savedStatus = $_SESSION['mentor_request_statuses'][$index] ?? 'Pending';
    $request['status'] = in_array($savedStatus, array_merge(['Pending'], $allowedStatuses), true)
        ? $savedStatus
        : 'Pending';
}
unset($request);

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
                <p class="request-error" role="alert"><?php echo htmlspecialchars($statusError); ?></p>
            <?php endif; ?>
            <div class="sessions-request">
                <?php foreach ($requests as $index => $request): ?>
                <div class="grid-item">

                    <div class="request-card-header">
                        <h3><?php echo htmlspecialchars($request['student']); ?></h3>

                        <span class="request-status status-<?php echo strtolower(htmlspecialchars($request['status'])); ?>">
                            <?php echo htmlspecialchars($request['status']); ?>
                        </span>
                    </div>

                    <div class="request-details">
                        <p>
                            <strong>Date:</strong>
                            <?php echo htmlspecialchars(formatDate($request['datetime'])); ?>
                        </p>

                        <p>
                            <strong>Time:</strong>
                            <?php echo htmlspecialchars(formatTime($request['datetime'])); ?>
                        </p>

                        <p>
                            <strong>Duration:</strong>
                            <?php echo htmlspecialchars(formatDuration((int) $request['duration'])); ?>
                        </p>

                        <p>
                            <strong>Type:</strong> <?php echo htmlspecialchars($request['type']); ?>
                        </p>
                    </div>

                    <form class="request-actions" method="post" action="requests.php">
                        <input type="hidden" name="request_index" value="<?php echo $index; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['mentor_requests_csrf_token']); ?>">
                        <?php foreach ($allowedStatuses as $status): ?>
                            <button
                                class="request-action"
                                type="submit"
                                name="status"
                                value="<?php echo htmlspecialchars($status); ?>"
                                <?php echo $request['status'] === $status ? 'disabled aria-pressed="true"' : ''; ?>
                            >
                                <?php echo htmlspecialchars($status); ?>
                            </button>
                        <?php endforeach; ?>
                    </form>
                </div>

            <?php endforeach; ?>

        
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

<!-- This sample page stores status changes in the PHP session until requests are database-backed. -->