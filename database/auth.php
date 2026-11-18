<?php

require_once __DIR__ . '/server.php';
require_once __DIR__ . '/session.php';

// Return the visitor to the login page. This function will be called whenever there is a problem with login.
function redirectToLogin()
{
    header('Location: ../mainpages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../mainpages/login.html');
    exit;
}

// Set the submitted values to variables and validate them.
$action = $_POST['action'] ?? '';
$role = $_POST['role'] ?? '';
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!in_array($role, ['student', 'mentor'], true)) { // validate the role to protect from SQL injection
    redirectToLogin();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) { // validate the email and make sure it is less than 255 characters,
    redirectToLogin();
}

$isStudent = $role === 'student';
$isMentor = $role === 'mentor'; // set  variable for if the user is a student or mentor which is needed for inserting the user to the correct table and redirection to their respective mainpages
$db = $isStudent ? $studentDb : $mentorDb;
$idColumn = '';
$table = '';
$fieldColumn = '';    

if($isStudent){ //set the  table variable to the appropriate table based on the user's role
    $table = 'studentstb';
}elseif($isMentor){
    $table = 'mentorstb';
}
if ($isStudent) { // set the idColumn variable to the appropriate column name based on the user's role
    $idColumn = 'student_id';
} elseif ($isMentor) {
    $idColumn = 'mentor_id';
}
if($isMentor){
    $fieldColumn = ', field'; // set the fieldColumn variable to include the field column for mentors, and leave it empty for students
}

// Create the account in the table for the selected role.
if ($action === 'signup') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $acaGoal = trim($_POST['aca_goal'] ?? '');
    $field = trim($_POST['field'] ?? '');
    $currentCareer = trim($_POST['current_career'] ?? '');

    if (
        $firstName === '' || $lastName === '' || strlen($firstName) > 255 || strlen($lastName) > 255 || strlen($password) < 8 || strlen($password) > 72 ||
        ($isStudent && ($acaGoal === '' || strlen($acaGoal) > 255)) ||
        ($isMentor && ($field === '' || strlen($field) > 255 || $currentCareer === '' || strlen($currentCareer) > 255))
    ) {
        redirectToLogin();
    }

    try {
        // Check duplicates within this role; an email may have both a student and mentor account.
        $check = $db->prepare("SELECT 1 FROM $table WHERE email = ? LIMIT 1");
        $check->execute([$email]);
        if ($check->fetchColumn()) {
            redirectToLogin();
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // hash and store the password in the database along with all the other variables
        if ($isStudent) {
            $insert = $db->prepare(
                'INSERT INTO studentstb (first_name, last_name, email, password, aca_goal, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $insert->execute([$firstName, $lastName, $email, $passwordHash, $acaGoal]);
        } elseif ($isMentor) {
            $insert = $db->prepare(
                'INSERT INTO mentorstb (first_name, last_name, email, password, current_career, field, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())'
            );
            $insert->execute([$firstName, $lastName, $email, $passwordHash, $currentCareer, $field]);
        }

        // Regenerate the session ID after authentication to prevent session fixation; true deletes the old session.
        //Basically, this means that the session ID is changed after the user logs in, from not having a account to having a account,
        //this helps so that if a hacker has the session ID, they can't use it to log in as the user.
        //also we can set the session variables to store the user's information, so that we can use it later on in the application.

        if($isStudent){
            session_regenerate_id(true);
            $_SESSION['user'] = [
            'id' => (int) $db->lastInsertId(), //get the last inserted id from the database, which is the user's id, and store it in the session variable
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'aca_goal' => $acaGoal,
            'role' => $role,
        ];
        }
        elseif($isMentor){
            session_regenerate_id(true);
            $_SESSION['user'] = [
            'id' => (int) $db->lastInsertId(), //get the last inserted id from the database, which is the user's id, and store it in the session variable
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role,
            'field' => $field,
            'current_career' => $currentCareer
        ];
        }
        // session_regenerate_id(true);
        // $_SESSION['user'] = [
        //     'id' => (int) $db->lastInsertId(), //get the last inserted id from the database, which is the user's id, and store it in the session variable
        //     'email' => $email,
        //     'first_name' => $firstName,
        //     'last_name' => $lastName,
        //     'role' => $role,
        // ];


        // Send the account confirmation email with the new profile fields.
        if (!function_exists('proc_open')) {
            error_log('Unable to send account creation email: PHP proc_open is unavailable.');
        } else {
            $emailScript = __DIR__ . '/../web app features/accountcreation.py';
            $python = getenv('MENTORSHIPMATCH_PYTHON') ?: 'python';
            $process = proc_open([
                $python,
                $emailScript,
                $email,
                trim($firstName . ' ' . $lastName),
                $role,
                $acaGoal,
                $currentCareer,
                $field,
            ], [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ], $pipes); //open the pipes to the process, so that we can read the output and error messages from the script (wont show up on the web page).

            if (is_resource($process)) {
                // Close stdin because the email script does not need input from PHP.
                fclose($pipes[0]);
                // Capture the Python script's normal output and error output for logging.
                $emailOutput = stream_get_contents($pipes[1]);
                $emailError = stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);

                // Wait for the script to finish and log its output if it failed.
                if (proc_close($process) !== 0) {
                    error_log('Unable to send account creation email: ' . trim($emailError . ' ' . $emailOutput));
                }
            } else {
                // proc_open did not return a valid process handle.
                error_log('Unable to start the Python process for the account creation email.');
            }
        }
    } catch (PDOException $e) { // catch any errors that occur during the database operations and log them, then redirect to the login page
        error_log($e->getMessage());
        redirectToLogin();
    }
} elseif ($action === 'login') { // if the user is trying to log in, check if the email and password are valid
    if ($password === '' || strlen($password) > 255) {
        redirectToLogin();
    }

    try {
        $query = $db->prepare(
            "SELECT $idColumn AS id, first_name, last_name, email, password$fieldColumn
             FROM $table
             WHERE email = ?
             LIMIT 1"
        );// prepare the query to get the user's information from the database based on the email and role
        $query->execute([$email]);
        $account = $query->fetch();
    } catch (PDOException $e) {
        error_log($e->getMessage()); // log any errors that occur during the database operations and redirect to the login page
        redirectToLogin();
    }

    if (!$account || !password_verify($password, $account['password'])) { // check if the account exists and if the password is correct, if not redirect to the login page
        redirectToLogin();
    }

    // Regenerate the session ID after authentication to prevent session fixation; true deletes the old session.
    //There is more comments to explain why this important in the signup section, but it is the same reason here.
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $account['id'],
        'email' => $account['email'],
        'first_name' => $account['first_name'],
        'last_name' => $account['last_name'],
        'role' => $role,
    ];
} else {
    redirectToLogin();
}

// Send each role to the page that matches its authenticated account.
header('Location: ' . ($isStudent ? '../student/student-homepage.html' : '../mentor/mentor-homepage.html'));
exit;

header('Location: ' .($isMentor ? '../mentor/mentor-homepage.html' : '../student/student-homepage.html'));
exit;
