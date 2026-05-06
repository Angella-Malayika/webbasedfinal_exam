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
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .nav-links { padding: 10px; background: #eee; margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; }
        .form-container { max-width: 400px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php">Add New Book</a>
        <a href="manage_book.php">Manage Books</a>
        <a href="borrow_records.php">Borrowing Records</a>
        <a href="../auth/logout.php" style="color: red; float: right;">Logout</a>
    </div>

    <div class="Container">
        <h2 style="text-align: center;">Add a New Book</h2>

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