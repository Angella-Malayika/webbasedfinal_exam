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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7fc; margin: 0; font-family: 'Poppins', sans-serif; }
        
        .main-content { margin-left: 280px; width: calc(100% - 280px); box-sizing: border-box; padding: 40px; }
        
        /* Typography */
        .main-content h2 { color: #002366; font-size: 28px; margin-bottom: 10px; }
        .main-content p { color: #666; font-size: 16px; margin-bottom: 30px; }

        /* Dashboard Cards Container */
        .dashboard-container { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 25px; 
        }

        /* Modern Card Styling */
        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .card-info h3 { 
            margin: 0; 
            color: #888; 
            font-size: 16px; 
            font-weight: 500; 
        }
        
        .card-info h1 { 
            margin: 10px 0 0 0; 
            color: #333; 
            font-size: 32px; 
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-blue { background: #e0f0ff; color: #007bff; }
        .icon-orange { background: #fff3cd; color: #ffc107; }
        .icon-green { background: #d4edda; color: #28a745; }

        /* Sidebar Styling */
            .sidebar{
            width:260px;
            height:100vh;
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

    <!-- <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php">Add New Book</a>
        <a href="manage_book.php">Manage Books</a>
        <a href="borrow_records.php">Borrowing Records</a>
        <a href="../auth/logout.php" style="color: red; float: right;">Logout</a>
    </div> -->

    <div class="sidebar">

        <div class="logo">
            <i class="fa-solid fa-book-open"></i> Library
        </div>

        <div class="menu">
            <a href="dashboard.php" class="active">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <a href="add_book.php">
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
        <h2>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>This is the Library Management control panel. Here is an overview of the system.</p>

        <div class="dashboard-container">
            <div class="card">
                <div class="card-info">
                    <h3>Total Books</h3>
                    <h1><?php echo $totalBooks; ?></h1>
                </div>
                <div class="card-icon icon-blue">
                    <i class="fa-solid fa-book"></i>
                </div>
            </div>
            
            <div class="card">
                <div class="card-info">
                    <h3>Books on Loan</h3>
                    <h1><?php echo $borrowedBooks; ?></h1>
                </div>
                <div class="card-icon icon-orange">
                    <i class="fa-solid fa-hand-holding-hand"></i>
                </div>
            </div>
            
            <div class="card">
                <div class="card-info">
                    <h3>Available Books</h3>
                    <h1><?php echo $totalBooks - $borrowedBooks; ?></h1>
                </div>
                <div class="card-icon icon-green">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

</body>
</html>