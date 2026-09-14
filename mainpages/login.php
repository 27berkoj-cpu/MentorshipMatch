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
    <link href = "style.css">
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
</body>
</html>

<!-- login.html will handle user login. 
 It should contain fields for an email or username and password, along with a login button and useful error messages. 
 This is required because the BPA topic specifically requires user registration and login. -->