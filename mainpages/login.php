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
    <title>Login Page</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/forms.css">
    </head>
<body>
    <header class="site-header">
        <a class="site-brand" href="index.html">Mentorship<span>Match</span></a>
        <nav class="site-nav" aria-label="Main navigation">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="../student/mentors.html">Find a mentor</a>
            <a class="is-current" href="login.php" aria-current="page">Log in</a>
            <a class="nav-cta" href="register.html">Get started</a>
        </nav>
    </header>
    <div>
        <form method = "POST">
            <input type = "text" name = "username" value = "<?= $username ?>" required><br>
            <input type = "password" name = "password" required> <br>
            <input type = "submit" value = "submit"><br>
        </form>
        No Account? <a href = "signup.php">Sign Up!</a>
    </div>

        <footer class="home-footer">
        <a class="site-brand" href="index.html">Mentorship<span>Match</span></a>
        <p>Helping students connect with guidance, experience, and opportunity.</p>
        <small>Chapter Name: Delaware Area Career Center</small><br>
        <small>Team Members: Joshua Berko, Thavael Noel, Shou Lin, Kyle Palermini</small><br>
        <small>Delaware, Ohio  2026-2027</small>
    </footer>
</body>
</html>

<!-- login.html will handle user login. 
 It should contain fields for an email or username and password, along with a login button and useful error messages. 
 This is required because the BPA topic specifically requires user registration and login. -->