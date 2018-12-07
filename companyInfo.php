<?php
    setcookie("attendeeLast", $_POST['last']);
    setcookie("attendeeFirst", $_POST['first']);
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
        setcookie("attendeeEmail",$_POST['email']);
}

$hostname = "localhost";
$username = "adminer";
$passwd = "which-along-25";
$DBConnect = false;
$DBName = "conference";
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
$TableName = "attendees";
if ($errors == 0) {
    $SQLstring = "SELECT count(*) FROM $TableName" ." WHERE attendeeEmail='$email'";
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
        " (attendeeFirst, attendeeLast, attendeeEmail)" .
        " VALUES('$first','$last','$email')";
    $queryResult = mysqli_query($DBConnect,$SQLstring);
    if (!$queryResult) {
        ++$errors;
        $body .= "<p>Unable to save your registration".
            " information error code: " . mysqli_error($DBConnect) ."</p>\n";
    }
    else {
        $attendeeID = mysqli_insert_id($DBConnect);
    }

}
if ($DBConnect) {
    setcookie("attendeeID", $attendeeID);
     $body .= "<p>Closing database connection.<p>\n";
    mysqli_close($DBConnect);
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
    <h1>Company Info</h1>
    <form action="seminars.php" method="post">
    Company Name:
    <input type="text" name="cName" required>
    <br>
    Company Email:
    <input type="text" name="cEmail" required>
    <br>
    <input type="reset" name="reset" value="Reset Form">
    <input type="submit" name="submit" value="Submit">
    </form>
    <?php
    echo $body;
    ?>
</body>

</html>
