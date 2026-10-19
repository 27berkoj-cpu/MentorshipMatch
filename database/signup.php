<?php
require "database/server.php"; //this was the hidden problem it was never rlly connected because it did not know the file was in the database server


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    // $role = $_POST["role"] ?? ""; //this is will be helpful for later
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        echo "Enter a valid email and a password of at least 8 characters.";
    }
    /*
        if ($role === "mentor") {
        $db = $mentorDb;
       } elseif ($role === "student") {
        $db = $studentDb;
       } 
       elseif ($role === "admin"){
       $db = $adminDb;
       } 
       
       else {
        exit("Choose mentor or student.");
    }
    */
     
    $db = $studentDb; // temporary until the role code above is turned on

    
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO table (email, password) VALUES (?, ?)"); // you need to add admin to the form and the uncomment the code for it to work
        $stmt->execute([$email, $hash]);
        echo "Signup successful!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { //this basically searches for if the email is already in the table
            echo "That email is already registered.";
        }else {
            echo "Something went wrong.";
        }
    }
}
?>
