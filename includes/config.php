<?php
$servername="localhost";
$username="root";
$password="";
$dbName="db_dummy";

//Creating Connection
$conn=mysqli_connect($servername,$username,$password,$dbName);

//Checking Connection
if (!$conn){
    //if connecting fails
    die("Connection Failed: " . mysqli_connect_error());
}
echo "Connection Successfully Established...";
// Close the connection
mysqli_close($conn);
?>