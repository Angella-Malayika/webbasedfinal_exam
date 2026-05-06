<?php
session_start();

// Protect the page: Only logged-in users with the 'admin' role can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include_once '../config/db.php';

// Quick stats for the dashboard
// 1. Total Books Count
$bookQuery = $conn->query("SELECT count(*) as total FROM books");
$totalBooks = $bookQuery->fetch_assoc()['total'] ?? 0;

// 2. Currently Borrowed Books Count
$borrowQuery = $conn->query("SELECT count(*) as borrowed FROM books WHERE status = 'borrowed'");
$borrowedBooks = $borrowQuery->fetch_assoc()['borrowed'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library System</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .dashboard-container { display: flex; gap: 20px; padding: 20px; }
        .card { border: 1px solid #ccc; padding: 20px; border-radius: 5px; width: 200px; text-align: center; }
        .nav-links { padding: 10px; background: #eee; margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; }
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

    <div class="Container" style="padding: 20px;">
        <h2>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>This is the Library Management control panel.</p>

        <div class="dashboard-container">
            <div class="card">
                <h3>Total Books</h3>
                <h1><?php echo $totalBooks; ?></h1>
            </div>
            
            <div class="card">
                <h3>Books on Loan</h3>
                <h1><?php echo $borrowedBooks; ?></h1>
            </div>
        </div>
    </div>

</body>
</html>