<?php
//Start session so we can save store login
session_start();

require_once 'db.php';  //including my db connection

// using (??) to avoid undefined index
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

//Basic Validation if fields are empty
if ($username ==='' || $password === '') {
    //Store an error messsage in session
    $_SESSION['error_message'] = 'Please enter both username and password';
    //Then Redirect to the login.php
    header("Location: login.php");
    exit();
}

//Query the database for user by username
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username =?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result= mysqli_stmt_get_result($stmt);
$user= mysqli_fetch_assoc($result);

//Check if user exists and password matches
// For now Comparing plain texts password 
if ($user && $password === $user['password_eg']) {
    //Login Successfull
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $user['username']; //Store username in session

    //Redirected to dashboard
    header("Location: dashboard.php");
    exit();
}

    else {
        $_SESSION['error_message'] = "Invalid Username or Password";
        header("Location: login.php");
        exit();
    }

