<?php

require_once __DIR__ . '/server.php';
require_once __DIR__ . '/session.php';

// Return the visitor to the login page with a safe, page-specific error message.
function redirectToLogin($error, $role, $mode)
{
    $query = http_build_query([
        'error' => $error,
        'role' => $role,//student or mentor
        'mode' => $mode, //login or signup
    ]);
    header('Location: ../mainpages/login.html?' . $query); // Re
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../mainpages/login.html');
    exit;
}

// Read submitted values before validating the requested account action and role.
$action = $_POST['action'] ?? '';
$role = $_POST['role'] ?? '';
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!in_array($role, ['student', 'mentor'], true)) {
    redirectToLogin('invalid_request', 'student', 'login');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
    redirectToLogin('invalid_email', $role, $action === 'signup' ? 'signup' : 'login');
}

$isStudent = $role === 'student';
$isMentor = $role === 'mentor';
$db = $isStudent ? $studentDb : $mentorDb;
$table = $isStudent ? 'studentstb' : 'mentorstb';
$idColumn = $isStudent ? 'student_id' : 'mentors_id';
$fieldColumn = $isStudent ? '' : ', feild';

// Create the account in the table for the selected role.
if ($action === 'signup') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $field = trim($_POST['field'] ?? '');

    if (
        $firstName === '' || $lastName === '' || strlen($firstName) > 255 || strlen($lastName) > 255 || strlen($password) < 8 || strlen($password) > 72 || (!$isStudent && ($field === '' || strlen($field) > 255))
    ) {
        redirectToLogin('invalid_signup', $role, 'signup');
    }

    try {
        // Check duplicates within this role; an email may have both a student and mentor account.
        $check = $db->prepare("SELECT 1 FROM $table WHERE email = ? LIMIT 1");
        $check->execute([$email]);
        if ($check->fetchColumn()) {
            redirectToLogin('email_exists', $role, 'signup');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($isStudent) {
            $insert = $db->prepare(
                'INSERT INTO studentstb (first_name, last_name, email, password, created_at)
                 VALUES (?, ?, ?, ?, NOW())'
            );
            $insert->execute([$firstName, $lastName, $email, $passwordHash]);
        } elseif ($isMentor) {
            $insert = $db->prepare(
                'INSERT INTO mentorstb (first_name, last_name, email, password, feild, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $insert->execute([$firstName, $lastName, $email, $passwordHash, $field]);
        }

        // Sign in the new account immediately and rotate the session ID.
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $db->lastInsertId(),
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role,
        ];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        redirectToLogin('account_error', $role, 'signup');
    }
// Look up the account using only the table and columns selected for its role.
} elseif ($action === 'login') {
    if ($password === '' || strlen($password) > 255) {
        redirectToLogin('invalid_credentials', $role, 'login');
    }

    try {
        $query = $db->prepare(
            "SELECT $idColumn AS id, first_name, last_name, email, password$fieldColumn
             FROM $table
             WHERE email = ?
             LIMIT 1"
        );
        $query->execute([$email]);
        $account = $query->fetch();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        redirectToLogin('account_error', $role, 'login');
    }

    if (!$account || !password_verify($password, $account['password'])) {
        redirectToLogin('invalid_credentials', $role, 'login');
    }

    // Rotate the session ID after successful authentication.
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $account['id'],
        'email' => $account['email'],
        'first_name' => $account['first_name'],
        'last_name' => $account['last_name'],
        'role' => $role,
    ];
} else {
    redirectToLogin('invalid_request', $role, 'login');
}

// Send each role to the page that matches its authenticated account.
header('Location: ' . ($isStudent ? '../student/student-homepage.html' : '../mentor/mentor-homepage.html'));
exit;
