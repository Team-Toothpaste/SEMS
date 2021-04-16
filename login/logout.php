<?php
session_start();
$_SESSION['sems-user'] = 'deleted';
session_destroy();
setcookie("sems-user", "deleted", time() - (86400 * 30), "/");

?>
