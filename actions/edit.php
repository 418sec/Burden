<?php

//Burden, Copyright Josh Fradley (http://github.com/joshf/Burden)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
    exit;
}

require_once("../config.php");

session_start();
if (!isset($_SESSION["burden_user"])) {
    header("Location: ../login.php");
    exit; 
}

if (!isset($_POST["idtoedit"])) {
    header("Location: ../index.php");
    exit;
}	

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_POST["idtoedit"]);

//Set variables
$newtask = mysql_real_escape_string($_POST["task"]);
$newdetails = mysql_real_escape_string($_POST["details"]);
$newcategory = mysql_real_escape_string($_POST["category"]);
if (isset($_POST["priority"])) {
    $newpriority = mysql_real_escape_string($_POST["priority"]);
}
$newdue = mysql_real_escape_string($_POST["due"]);

//Failsafes
if (empty($newtask) || empty($newdue)) {
    header("Location: ../edit.php?id=$idtoedit&error=emptyfields");
    exit;
}

//Flip dates back for consistency, work around #5
if (strpos($newdue, "-") !== false) {
    $segments = explode("-", $newdue);
    if (count($segments) == 3) {
        list($year, $month, $day) = $segments;
    }
    $newdue = "$day/$month/$year";
}

if (isset($_POST["highpriority"])) {
    $newhighpriority = "1";
} else {
    $newhighpriority = "0";
}

mysql_query("UPDATE `Data` SET `category` = \"$newcategory\", `highpriority` = \"$newhighpriority\", `task` = \"$newtask\", `details` = \"$newdetails\", `due` = \"$newdue\" WHERE `id` = \"$idtoedit\"");

mysql_close($con);

header("Location: ../index.php");

exit;

?>