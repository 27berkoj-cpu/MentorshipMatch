<?php
require __DIR__ . "/server.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email     = strtolower(trim($_POST["email"] ?? ""));
    $password  = $_POST["password"] ?? "";
    $type      = $_POST["account_type"] ?? "";
    $firstName = trim($_POST["first_name"] ?? "");
    $lastName  = trim($_POST["last_name"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255 || strlen($password) < 8 || strlen($password) > 72) {
        echo "Enter a valid email and a password of 8 to 72 characters.";
        exit;
    }

    if ($firstName === "" || $lastName === "" || strlen($firstName) > 255 || strlen($lastName) > 255) {
        echo "Enter your first and last name.";
        exit;
    }

    if ($type === "student") {
        $table = "studentstb";
    } elseif ($type === "mentor") {
        $table = "mentorstb";
    } else {
        echo "Please choose whether you are signing up as a student or a mentor.";
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO `$table` (first_name, last_name, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $hash);
        $stmt->execute();
        $stmt->close();
        echo "Signup successful!";
    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage());
        if ($e->getCode() == 1062) {
            echo "That email is already registered.";
        } else {
            echo "Something went wrong.";
        }
    }
}
?>