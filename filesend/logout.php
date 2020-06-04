<?php

session_start();

// clear session variables and destroy the session
$_SESSION = array();
session_destroy();

// back to login page
header("location: login.php");
exit;
