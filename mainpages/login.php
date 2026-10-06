<?php
    $username = "";
    $password = "";
    $email = "";
    $first_name = "";
    $last_name = "";
    $error = [];
    $confirm = $password;
?>
<?php
//sign up code 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
}

    // Validation
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    //i need to add a check for if the email/username is already in use.
    // save a "new user" for that sign up to the server.
    //i need to make the information more secure to make sure that nothing gets stolen or hacked.
    //that will be when signup will me done.

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
<<<<<<< HEAD
    <!--<div>
        <form method = "POST">
            <input type = "text" name = "username" value = <?= htmlspecialchars($username ?? '') ?> required><br>
=======
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
>>>>>>> main
            <input type = "password" name = "password" required> <br>
            <input type = "submit" value = "submit"><br>
        </form>
        No Account? <a href = "signup.php">Sign Up!</a>
<<<<<<< HEAD
    </div> -->
    <!-- Sign Up form -->
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Sign Up</button>
        
    </form>
=======
    </div>

        <footer class="home-footer">
        <a class="site-brand" href="index.html">Mentorship<span>Match</span></a>
        <p>Helping students connect with guidance, experience, and opportunity.</p>
        <small>Chapter Name: Delaware Area Career Center</small><br>
        <small>Team Members: Joshua Berko, Thavael Noel, Shou Lin, Kyle Palermini</small><br>
        <small>Delaware, Ohio  2026-2027</small>
    </footer>
>>>>>>> main
</body>
</html>

<!-- login.html will handle user login. 
 It should contain fields for an email or username and password, along with a login button and useful error messages. 
 This is required because the BPA topic specifically requires user registration and login. -->