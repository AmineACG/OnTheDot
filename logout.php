<?php
session_start();

// Unset all of the session variables
$_SESSION = array();
session_destroy();
// Prevent caching of the logout page
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Location:login.php");
exit();
?> 