<?php
session_start();
$errors = 0;
$email = 0;
$body = "";
if (empty($_POST['email'])) {
    ++$errors;
    $body .= "<p>You need to enter an e-mail address</p>\n";
}
else{
    $email = stripslashes($_POST['email']);
 if (preg_match("/^[\w-]+(\.[\w-])*@[\w-]+(\.[\w-]+)*(\.[A-Za-z]{2,})$/i", $email) == 0){
     ++$errors;
     $body .= "<p>You need to enter a valid e-mail address</p>\n";
     $email = "";
 }
}
if (empty($_POST['password'])) {
    ++$errors;
    $body .= "<p>You need to enter a password</p>\n";
}
else {
    $password = stripslashes($_POST['password']);
}

if (empty($_POST['password2'])) {
    ++$errors;
    $body .= "<p>You need to enter a conformation password</p>\n";
}
else {
    $password2 = stripslashes($_POST['password2']);
}
if (!empty($password) && !empty($password2)) {
    if (strlen($password) < 6) {
        ++$errors;
        $body .= "<p>The passwords do not match.</p>\n";
        $password = "";
        $password2 = "";
    }  
    if ($password <> $password2) {
        ++$errors;
        $body .= "<p>The password is too short.</p>\n";
        $password = "";
        $password2 = "";
    }   
}
$hostname = "localhost";
$username = "adminer";
$passwd = "which-along-25";
$DBConnect = false;
$DBName = "internships2";
if ($errors == 0) {
    $DBConnect = mysqli_connect($hostname, $username, $passwd);
    if (!$DBConnect) {
        ++$errors;
        $body .= "<p>Unable to connect to the database server".
            " error code: " . mysqli_connect_error() .
            " </p>\n";
    }
    else {
        $result = mysqli_select_db($DBConnect, $DBName);
        if (!$result) {
            ++$errors;
            $body .= "<p>Unable to select the database".
                " \"$DBName\" error code: " . mysqli_error($DBConnect) .
                " </p>\n";
        }
    }
}
$TableName = "interns";
if ($errors == 0) {
    $SQLstring = "SELECT count(*) FROM $TableName" .
        " WHERE email='$email'";
    $queryResult = mysqli_query($DBConnect,$SQLstring);
    if ($queryResult) {
        $row = mysqli_fetch_row($queryResult);
        if ($row[0] > 0) {
            ++$errors;
            $body .= "<p>The e-mail address entered (".
                htmlentities($email) . ") is already registered.</p>\n";
        }
    }
}
if ($errors == 0){
    // added in the method to uppercase the first letter in the clients first and last name in case that they choose not to
    $first = stripslashes(ucfirst($_POST['first']));
    $last = stripslashes(ucfirst($_POST['last']));
    $SQLstring = "INSERT INTO $TableName" .
        " (first, last, email, password_md5)" .
        " VALUES('$first','$last','$email', '" .
        md5($password) ."')";
    $queryResult = mysqli_query($DBConnect,$SQLstring);
    if (!$queryResult) {
        ++$errors;
        $body .= "<p>Unable to save your registration".
            " information error code: " . mysqli_error($DBConnect) ."</p>\n";
    }
    else {
//        $internID = mysqli_insert_id($DBConnect);
        $_SESSION['internID'] = mysqli_insert_id($DBConnect);
    }

}
if ($errors == 0) {
    $internName = $first . " " . $last;
    $body .= "<p>Thank you, $internName. ";
    $body .= "Your new InternID is <strong>" .
        $_SESSION['internID'] . "</strong>.</p>\n";
}

if ($DBConnect) {
    setcookie("internID", $_SESSION['internID']);
     $body .= "<p>Closing database connection.<p>\n";
    mysqli_close($DBConnect);
}
if ($errors == 0) {
//    $body .= "<form action='AvailableOpportunities.php' method='post'>\n";
//    $body .= "<input type='hidden' name='internID' value='$internID'>\n";
//    $body .= "<input type='submit' name='submit' value='View Available Opportunities'>\n";
//    $body .= "</form>\n";
    $body .= "<p><a href='AvailableOpportunities.php?" .
        "PHPSESSID=" . session_id() . "'>" . 
        "View Available Opportunities</a></p>\n";
}
if ($errors > 0) {
    $body .= "<p>Please use your browser's BACK button" . 
         " to return to the form and fix the errors" . 
         " indicated.</p>";
}
?>
<!doctype html>

<html>

<head>
    <title>Internship Registration</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
</head>

<body>
    <h1>College Internship</h1>
    <h2>Intern Registration</h2>
    <?php
    echo $body;
    ?>
</body>

</html>
