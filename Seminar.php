<?php
setcookie("cName", $_POST['cname']);
setcookie("cEmail", $_POST['cEmail']);
?>
<!doctype html>

<html>

<head>
    <title>Available Opportunities</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h2>Seminars</h2>
    <?php
    if (isset($_COOKIE['attendeeID'])) {
        $attendeeID = $_REQUEST['attendeeID'];
    }
    else {
        $sttendeeID = -1;
    }
    // debug
    echo "\$attendeeID:" . $_COOKIE['attendeeID'] . "\n";
    $errors = 0;
    $hostname = "localhost";
    $username = "adminer";
    $passwd = "which-along-25";
    $DBConnect = false;
    $DBName = "conference";
    if ($errors == 0) {
        $DBConnect = mysqli_connect($hostname, $username, $passwd);
        if (!$DBConnect) {
            ++$errors;
            echo "<p>Unable to connect to the database server".
                " error code: " . mysqli_connect_error() .
                " </p>\n";
        }
        else {
            $result = mysqli_select_db($DBConnect, $DBName);
            if (!$result) {
                ++$errors;
                echo "<p>Unable to select the database".
                    " \"$DBName\" error code: " . mysqli_error($DBConnect) .
                    " </p>\n";
            }
        }
    }
    $TableName = "attendees";
    if ($errors == 0) {
        $SQLstring = "SELECT * FROM $TableName" . 
            " WHERE attendeeid='" . $_COOKIE['attendeeID'] ."'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (!$queryResult) {
            ++$errors;
            echo "<p>Unable to execute the query, error code: " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) ."</p>\n";
        }
        else {
            if (mysqli_num_rows($queryResult) == 0) {
                ++$errors;
                echo "<p>Invalid attendeeID!</p>\n";
            }
        }
    }
    if ($errors == 0){
        $row = mysqli_fetch_assoc($queryResult);
        $attendeeName = $row['first'] . " " . $row['last'];
    }
    else {
        $attendeeName = "";
    }
    // debug
    echo "\$internName: $attendeeName";
    $TableName = "selected_seminars";
    if ($errors == 0){
        $SQLstring = "SELECT count(seminarid)" .
            " FROM $TableName" .
            " WHERE attendeeID='" . $_COOKIE['attendeeID'] ."'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            $row = mysqli_fetch_row($queryResult);
            $confirmedSeminars = $row[0];
            mysqli_free_result($queryResult);
        }
    }
    if ($errors == 0) {
        $selectedSeminars = array();
        $SQLstring = "SELECT seminarid FROM $TableName" . 
            " WHERE attendeeid='" . $_COOKIE['attendeeID'] . "'";
        $queryResult = mysqli_query($DBConnect,$SQLstring);
         if (mysqli_num_rows($queryResult) > 0) {
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $selectedSeminars[] = $row[0];
            }
            mysqli_free_result($queryResult);
        }
        $confirmedSeminar = array();
        $SQLstring = "SELECT seminarid FROM $TableName" . 
            " WHERE attendeeid='" . $_COOKIE['attendeeID'] . "'";
        $queryResult = mysqli_query($DBConnect,$SQLstring);
         if (mysqli_num_rows($queryResult) > 0) {
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $confirmedSeminars[] = $row[0];
            }
            mysqli_free_result($queryResult);
        }
    }
    $TableName = "seminar";
    $seminars= array();
    if ($errors == 0) {
        $SQLstring = "SELECT  FROM $TableName";
        $queryResult = mysqli_query($DBConnect, $SQLstring);        
          if (mysqli_num_rows($queryResult) > 0) {
            while (($row = mysqli_fetch_assoc($queryResult)) != false) {
                $seminars[] = $row;
            }
            mysqli_free_result($queryResult);
        }
    }
    if ($DBConnect) {
        echo "<p>closing the database connection.</p>\n";
        mysqli_close($DBConnect);
    }
    if (!empty($lastRequestDate)){
        echo "<p>You last requested an Seminar" . 
            " opportunity on $lastRequestDate.</p>\n";
    }
    
    echo "<table border='1' width='100%'>\n";
    echo "<tr>\n";
    echo "<th style='background-color:cyan'>Seminar</th>\n";
    echo "<th style='background-color:cyan'>Description</th>\n";
    echo "</tr>\n";
    foreach ($seminars as $seminar) {
        if (!in_array($opportunity['opportunityID'], $assignedOpportunities)){
        echo "<tr>\n";
        echo "<td>" . htmlentities($seminar['seminarID']) . "</td>\n";
        echo "<td>" . htmlentities($seminar['seminarName']) . "</td>\n";
        echo "<td>" . htmlentities($seminar['description']) . "</td>\n";
        echo "<td>" . htmlentities($seminar['endDate']) . "</td>\n";
        echo "<td>";
        if (in_array($opportunity['opportunityID'], $selectedOpportunities)){
            echo "Selected";
        }
        else if ($approvedOpportunities){
            echo "Open";
        }
        else {
            echo "<a href='RequestOpportunity.php?" .
                "PHPSESSID=" . session_id() . 
                "&opportunityID=" .
                $opportunity['opportunityID'] .
                "'>Available</a>";
        }
        echo "</td>\n";
        echo "</tr>\n";
        }
    }
    echo "</table>\n";
    echo "<p><a href='InternLogin.php'>Log Out</a></p>\n";
    ?>
</body>

</html>
