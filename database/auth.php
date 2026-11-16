<?php

require_once __DIR__ . '/server.php';
require_once __DIR__ . '/session.php';

// Return the visitor to the login page. This function will be called whenever there is a problem logining in.
function redirectToLogin()
{
    header('Location: ../mainpages/login.php');
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

if($isStudent){ //set the  table vairabie to the appropitate table based on the user's role
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
    $field = trim($_POST['field'] ?? '');

    if (
        $firstName === '' || $lastName === '' || strlen($firstName) > 255 || strlen($lastName) > 255 || strlen($password) < 8 || strlen($password) > 72 || (!$isStudent && ($field === '' || strlen($field) > 255))
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

        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // hash and store the password in teh database along with all the other variables
        if ($isStudent) {
            $insert = $db->prepare(
                'INSERT INTO studentstb (first_name, last_name, email, password, created_at)
                 VALUES (?, ?, ?, ?, NOW())'
            );
            $insert->execute([$firstName, $lastName, $email, $passwordHash]);
        } elseif ($isMentor) {
            $insert = $db->prepare(
                'INSERT INTO mentorstb (first_name, last_name, email, password, field, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $insert->execute([$firstName, $lastName, $email, $passwordHash, $field]);
        }

        // Regenerate the session ID after authentication to prevent session fixation; true deletes the old session.
        //Basically, this means that the session ID is changed after the user logs in, from not having a account to having a account,
        //this helps so that if a hacker has the session ID, they can't use it to log in as the user.
        //also we can set the session variables to store the user's information, so that we can use it later on in the application.
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $db->lastInsertId(),
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role,
        ];


        //this is where email creation is sent to the user after they create an account.
        if (!function_exists('proc_open')) {
            error_log('Unable to send account creation email: PHP proc_open is unavailable.'); //test to see if the proc_open fucntion is available.
            $emailScript = __DIR__ . '/../web app features/accountcreation.py'; //get the path to the script that send the email.
            $python = getenv('MENTORSHIPMATCH_PYTHON') ?: 'python'; //get the python path from the enviroment variable, and if it is not set the deault is just python
            $process = proc_open([ //open a new process to run the script that sends the email
                $python,
                $emailScript,
                $email,
                trim($firstName . ' ' . $lastName), 
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
