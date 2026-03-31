<?php session_start(); ?>
<html>
<head>
    <title>Login Authentication</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="post" action="process_login.php">
        <?php
        if (!empty($_SESSION['error_message'])) {
        echo '<p style="color: red; text-align: center;">' . htmlspecialchars($_SESSION['error_message']). '</p>';
        unset($_SESSION['error_message']); //Clear after showing
    }
    ?>
        <div class="login-form">
            <div class="login-header">
                <header>Adminstration Login</header>
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
                    <input type="checkbox" id="show-password" onclick="togglePassword()">
                    <label for="show-password">Show Password</label>
                </section>
                    <!-- <section>
                        <a href="#">Forget Password?</a>
                    </section> -->
                </div> 
                <div class="login-button">
                    <button type="submit" id="submit">Login</button>
            </div>
        </div>
</form>
<script>
function togglePassword() {
    // Select the password input field by its ID
    const passwordField = document.getElementById("password");
    const checkbox = document.getElementById("show-password");

    // Toggle the type attribute
    if (checkbox.checked) {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>
</body>
</html>