<?php
    session_start();
// $username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = test_input($_POST["username"]);
    $password = test_input($_POST["password"]);
    //Check if the credentials match
if($username === "admin" && $password ==="admin#123") {
    //Successful login
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;
    header("Location: dashboard.php"); //Redirect to dashboard
    exit ();
}
    else{
        //If failed
        $error_message = "Invalid Username or Password!";
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<html>
<head>
    <title>Login Authentication</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <?php
            if (isset($error_message)) {
                echo '. $error_message .' ;
        }
        ?>
        <div class="login-form">
            <div class="login-header">
                <header>Login</header>
            </div>
            <div class="input-box">
                <label for="text">Username</label>
                <input type="text" name="username" id="text" class="input-field" placeholder="Username" autocomplete="username" required>
            </div>
            <div class="input-box">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="input-field" placeholder="Password" autocomplete="current-password" required>
            </div>
            <div class="remember">
                <section>
                    <input type="checkbox" id="checkbox">
                    <label for="checkbox">Remember Me</label>
                </section>
                    <section>
                        <a href="#">Forget Password?</a>
                    </section>
                </div> 
                    <div class="login-button">
                        <button type="submit" id="submit">Login</button>
            </div>
        </div>
</form>
</body>
</html>