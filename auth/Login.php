<?php
session_start();
include_once '../config/db.php';

$error = '';

if( $_SERVER["REQUEST_METHOD"] == "POST"){
    $email =trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)){
        $error ="Please enter email and password.";
    }
    else {
        // Check if user exists
        $stmt =$conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows >0){
            $stmt->bind_result($id, $username, $hashed_password, $role);
            $stmt->fetch();

            // verify password
            if (password_verify($password, $hashed_password)){
                //set session variables
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
            

                //Redirect based on role
                if ($_SESSION['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                    
                }
                else {
                    header("Location: ../student/dashboard.php");
                }
                exit();
            
            } else {
                // handle incorrect password
                $error = "Invalid email or password.";
            }

        } else {
            // handle unsupported email
            $error = "Invalid email or password.";
        }
    }

    $stmt->close();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .Container {
            margin: 0; /* Reset margin since flex centering is used */
        }
    </style>
</head>

<body>

    <div class="Container">
        <h2>Login to your Account</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="">Email</label> <br>
            <input type="email" name="email" placeholder="Enter your email" required><br><br>

            <label for="">Password</label> <br>
            <input type="password" name="password" placeholder="Enter password" required> <br><br>
            <button type="submit">Login</button>

        </form>
        <p>Don't have an account?<a
                href="register.php"> Sign up</a>
        </p>
    </div>

</body>

</html>