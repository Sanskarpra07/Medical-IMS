<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("db.php"); // make sure db.php exists

// Redirect to login.php
header("Location: login.php");
exit; // always call exit after header redirect

// This line will never run because of exit
echo "Hello, XAMPP!";
?>
