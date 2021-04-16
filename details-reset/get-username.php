<?php
require_once('../.credentials.php');
$userid = $_COOKIE["sems-user"].explode()[0];
$conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    if ($conn) {
        // Compare posted values to see if they exist in users table
        $result = mysqli_query($conn, "SELECT username, FROM users WHERE userId = '".$userid."'");
        $arr = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $arr["username"];
    }
?>