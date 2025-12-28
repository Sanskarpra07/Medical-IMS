<?php
    require_once'../db.php';

    //sql query to insert a new table
    $sql = "INSERT INTO products(product_name, description, price, stock, created_at)
    VALUES('Flexon', 'a painkiller medicine', 50.00, 75, NOW())";

    //Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "New record inserted successfully";
    }   else{
            echo "Inserting Error:" . $conn->error;
    }

    //close the connection
    $conn->close();
?>