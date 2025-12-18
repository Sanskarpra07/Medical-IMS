<!DOCTYPE html>
<html>
<head>
    <title>Register - Create Account</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-message {
            color: #d32f2f;
            background: #ffebee;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .success-message {
            color: #388e3c;
            background: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php
require_once 'config.php';

$username = $email = $password = $confirm_password = "";
$errors = array();
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = test_input($_POST["username"]);
    $email = test_input($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Validate username
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    } elseif (strlen($username) > 50) {
        $errors[] = "Username must be less than 50 characters";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    // Validate confirm password
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // If no errors, check if user already exists
    if (empty($errors)) {
        $conn = getDBConnection();
        
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "Username already taken";
        }
        $stmt->close();
        
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered";
        }
        $stmt->close();
        
        // If still no errors, insert the user
        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>";
                $username = $email = "";
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
            
            $stmt->close();
        }
        
        $conn->close();
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="login-form">
            <div class="login-header">
                <header>Register</header>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p style="margin: 5px 0;"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <div class="input-box">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="input-field" 
                       placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            
            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="input-field" 
                       placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="input-box">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="input-field" 
                       placeholder="Password (min 6 characters)" required>
            </div>
            
            <div class="input-box">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="input-field" 
                       placeholder="Confirm Password" required>
            </div>
            
            <div class="login-button">
                <button type="submit" id="submit">Register</button>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </form>
</body>
</html>