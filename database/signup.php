<?php
require "database/server.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");  //set the email
    $password = $_POST["password"] ?? "";
    $type     = $_POST["account_type"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        echo "Enter a valid email and a password of at least 8 characters.";
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
        $stmt = $db->prepare("INSERT INTO `$table` (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hash);
        $stmt->execute();
        $stmt->close();
        echo "Signup successful!";
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "That email is already registered.";
        } else {
            echo "Something went wrong.";
        }
    }
}
?>