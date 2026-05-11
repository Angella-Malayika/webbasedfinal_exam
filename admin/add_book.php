<?php
session_start();

// Protect the page: Only logged-in users with the 'admin' role can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include_once '../config/db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);

    if (empty($title) || empty($author)) {
        $message = "<p style='color: red;'>Please enter both title and author.</p>";
    } else {
        // Prepare query to prevent SQL Injection
        // We set status to 'available' by default for new books
        $stmt = $conn->prepare("INSERT INTO books (title, author, status) VALUES (?, ?, 'available')");
        $stmt->bind_param("ss", $title, $author);

        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Book added successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error adding book: " . $conn->error . "</p>";
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
    <title>Add Book - Admin</title>
    <!-- <link rel="stylesheet" href="../assets/style.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
        background-color: #f4f7fc; 
        margin: 0; 
        font-family: 'Poppins', sans-serif; }
        
        .main-content { margin-left: 280px; 
        width: calc(100% - 280px); 
        box-sizing: border-box; 
        padding: 40px; }
        .main-content h2 { color: #002366; 
        font-size: 28px; 
        margin-bottom: 20px;
        text-align: center; }

        .form-container { 
            max-width: 500px; 
            margin: 0 auto; 
            padding: 30px; 
            border-radius: 12px; 
            background-color: #fff; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
        .form-group input { width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; transition: 0.3s; }
        .form-group input:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }
        
        form button { width: 100%; padding: 12px; background: #0d6efd; color: white; border: none; cursor: pointer; border-radius: 8px; font-size: 16px; font-weight: 600; transition: background 0.3s; margin-top: 10px; }
        form button:hover { background: #0056b3; }

         .sidebar{
            width:260px;
            height:92vh;
            background:linear-gradient(180deg,#002366,#001845);
            color:white;
            position:fixed;
            padding:25px 20px;
        }

        .logo{
            font-size:26px;
            font-weight:700;
            margin-bottom:50px;
        }

        .menu a{
            display:flex;
            align-items:center;
            gap:15px;
            color:white;
            text-decoration:none;
            padding:15px;
            margin-bottom:10px;
            border-radius:12px;
            transition:0.3s;
        }

        .menu a:hover,
        .menu .active{
            background:#0d6efd;
        }

        .menu i{
            font-size:18px;
        }

        .logout{
            position:absolute;
            bottom:30px;
            width:85%;
        }
    </style>
</head>
<body>
    <div class="sidebar">

        <div class="logo">
            <i class="fa-solid fa-book-open"></i> Library
        </div>

        <div class="menu">
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <a href="add_book.php" class="active">
                <i class="fa-solid fa-plus"></i>
                <span>Add Book</span>
            </a>

            <a href="manage_book.php">
                <i class="fa-solid fa-book"></i>
                <span>Manage Books</span>
            </a>

            <a href="borrow_records.php">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Borrow Records</span>
            </a>
           
            <a href="../auth/logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>

    </div>

    <div class="main-content">
        <h2>Add a New Book</h2>

        <div class="form-container">
            <!-- Ensure message success/error is displayed -->
            <?php echo $message; ?>
            
            <form action="add_book.php" method="POST">
                <div class="form-group">
                    <label for="title">Book Title</label>
                    <input type="text" name="title" id="title" placeholder="Enter book title" required>
                </div>

                <div class="form-group">
                    <label for="author">Author Name</label>
                    <input type="text" name="author" id="author" placeholder="Enter author name" required>
                </div>

                <button type="submit">Save Book</button>
            </form>
        </div>
    </div>

</body>
</html>