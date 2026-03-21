<?php
$servername="localhost";
$username="root";
$password="";
$dbName="db_dummy";

//Creating Connection
$conn=mysqli_connect($servername,$username,$password,$dbName);
if (!$conn) {
    die("Connection Failed:" . mysqli_connect_error());
}
?>