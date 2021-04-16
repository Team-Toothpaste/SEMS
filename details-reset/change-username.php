<?php
require_once('../.credentials.php');
$userid = $_COOKIE["sems-user"].explode()[0];
$conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
$newuser = mysqli_real_escape_string($conn,$_POST["newuser"]);
    if ($conn) {
        // Compare posted values to see if they exist in users table
        $result = mysqli_query($conn, "UPDATE users SET username='".$newuser"' WHERE userId = '".$userid."'");
        if ($result == TRUE) {
            return $userid;
        }
        else {
            return "Could not change username.";
        }
    }
?>