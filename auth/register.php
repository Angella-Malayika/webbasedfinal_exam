<?php
session_start();
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Automatically determine role based on keyword in email
            $role = 'student'; // Default fallback
            if (strpos($email, 'lib') !== false) {
                $role = 'admin';
            } elseif (strpos($email, 'stud') !== false) {
                $role = 'student';
            }
            
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($insert_stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register page</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <style>
     body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .Container {
            margin: 0; 
        }
    </style>
    <div class="Container">
        <h2>Create your Account</h2>
        
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Username</label> <br>
            <input type="text" id="username" name="username" placeholder="Enter your Username" required><br><br>

            <label for="email">Email</label> <br>
            <input type="email" id="email" name="email" placeholder="Enter your email" required><br><br>

            <label for="password">Password</label> <br>
            <input type="password" id="password" name="password" placeholder="Enter password" required> <br><br>

            <label for="confirm_password">Confirm Password</label> <br>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required> <br><br>
            
            <button type="submit">Sign up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>