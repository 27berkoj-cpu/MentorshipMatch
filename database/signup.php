<?php
require_once __DIR__ . '/server.php';

$database = new DBConn();
$db = $database->get_connection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");  //set the email
    $password = $_POST["password"] ?? "";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        echo "Enter a valid email and a password of at least 8 characters.";
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO studentstb (email, password) VALUES (?, ?)");
        // Bind the email and password hash so submitted data cannot alter the SQL statement.
        $stmt->bind_param('ss', $email, $hash);
        $stmt->execute();
        $stmt->close();
        echo "Signup successful!";
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1062) {
            echo "That email is already registered.";
        } else {
            error_log($e->getMessage());
            echo "Something went wrong.";
        }
    }
}
?>
