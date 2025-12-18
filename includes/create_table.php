<?php
//Create Connection
$conn= new mysqli("localhost","root","","db_dummy");

//Check Connection
if ($conn->connect_error) {
    die("Connection Failed:" . $conn->connect_error);
}

//Creating a new Table
$sql= "CREATE TABLE products (
id INT(6) AUTO_INCREMENT PRIMARY KEY,
product_name VARCHAR(255) NOT NULL,
description Text,
price DECIMAL(10,2) NOT NULL,
stock INT NOT NULL,
created_at DATETIME,
updated_at DATETIME )" ;

//Excute the Query
if ($conn->query($sql)===TRUE) {
    echo "Table Created Successfully";
}
else{
    echo "Error Creating Table:" . $conn->error;
}
//Close the connection
$conn->close();
?>