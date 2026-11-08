<?php

require_once __DIR__ . '/server.php';
require_once __DIR__ . '/session.php';

function redirectToLogin($error, $role, $mode)
{
    $query = http_build_query([
        'error' => $error,
        'role' => $role,
        'mode' => $mode,
    ]);
    header('Location: ../mainpages/login.html?' . $query);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../mainpages/login.html');
    exit;
}

$action = $_POST['action'] ?? '';
$role = $_POST['role'] ?? '';
$email = trim($_POST['email'] ?? ''); //to off make it all lowercase, you can use strtolower($email)
$password = $_POST['password'] ?? '';

if (!in_array($role, ['student', 'mentor'], true)) {
    redirectToLogin('invalid_request', 'student', 'login');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
    redirectToLogin('invalid_email', $role, $action === 'signup' ? 'signup' : 'login');
}

if($role === 'student'){
    $isStudent = true;
    $isMentor = false;
    $db = $studentDb;
    $table = 'studentstb';
    $idColumn = 'student_id';
    $fieldColumn = '';
}

else if($role === 'mentor'){
    $isStudent = false;
    $isMentor = true;
    $db = $mentorDb;
    $table = 'mentorstb';
    $idColumn = 'mentor_id';
    $fieldColumn = ', feild';
}

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
        } else if ($isMentor) {
            $insert = $db->prepare(
                'INSERT INTO mentorstb (first_name, last_name, email, password, feild, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $insert->execute([$firstName, $lastName, $email, $passwordHash, $field]);
        }

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

header('Location: ' . ($isStudent ? '../student/student-homepage.html' : '../mentor/mentor-homepage.html'));
exit;
