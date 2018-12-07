<?php
session_start();
$_SESSION = array();
session_destroy();
?>
<!doctype html>

<html>

<head>
    <title>College Internships</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h1>College Internships</h1>
    <h2>Register / Login</h2>
    <p>
    New Interns, please complete the top form to register as a new user. 
    Returning users, please complete the second form to login.
    </p>
    
    <h3>New Intern Registration</h3>
    <form action="RegisterIntern.php?PHPSESSID='<?php echo session_id(); ?>'" method="post">
        <p>Enter your name: First
           <input type="text" name="first">
           Last
           <input type="text" name="last">    
        </p>
        <p>
            Enter your e-mail address:
            <input type="text" name="email">
        </p>
        <p>
            Enter a password for your account:
            <input type="password" name="password">
        </p>
        <p>
            Confirm your password:
            <input type="password" name="password2">
        </p>
        <p>
            <em>(Passwords are case-sensitive and must be at least 6 characters long)</em>
        </p>
        <input type="reset" name="reset" value="Reset Registration Form">
        <input type="submit" name="register" value="Register">
    </form>
    
    
    <h3>Returning Intern Login</h3>
    <form action="VerifyLogin.php?PHPSESSID='<?php echo session_id(); ?>'" method="post">
        <p>
            Enter your e-mail address:
            <input type="text" name="email">
        </p>
        <p>
            Enter a password for your account:
            <input type="password" name="password">
        </p>
        <p>
            <em>(Passwords are case-sensitive and must be at least 6 characters long)</em>
        </p>
        <input type="reset" name="reset" value="Reset Login Form">
        <input type="submit" name="register" value="Log In">
    </form>
</body>

</html>
