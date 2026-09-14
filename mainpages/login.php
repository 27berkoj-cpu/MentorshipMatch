<?php
    $username = "";
    $password = "";
    $error = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        /*
            Connect to SQL and verify both username/email and password
            Use password_verify()
            Then create a session with a session id
        */
        if(str_contains($username, "@")){

        }else{

        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/forms.css">
    </head>
<body>
    <div>
        <form method = "POST">
            <input type = "text" name = "username" value = "<?= $username ?>" required><br>
            <input type = "password" name = "password" required> <br>
            <input type = "submit" value = "submit"><br>
        </form>
        No Account? <a href = "signup.php">Sign Up!</a>
    </div>

        <footer>
        <p>Chapter Name: [CHAPTER NAME]</p>
        <p>Team Members: Joshua Berko, Shou Lin, Thavael Noel, Kyle Palermini</p>
        <p>Theme: MentorshipMatch</p>
        <p>Delaware Area Career Center | Delaware, Ohio | 2026–2027</p>
    </footer>
</body>
</html>

<!-- login.html will handle user login. 
 It should contain fields for an email or username and password, along with a login button and useful error messages. 
 This is required because the BPA topic specifically requires user registration and login. -->