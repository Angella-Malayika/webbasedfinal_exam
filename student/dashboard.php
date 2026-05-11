<?php
session_start();

// Protect the page: Only logged-in users with the 'student' (or any non-admin) role can access
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
//     header("Location: ../auth/login.php");
//     exit();
// }

include_once '../config/db.php';

// Handle Search logic
$search_query = "";
$where_clause = "";
$params = [];
$types = "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    // Search by title OR author
    $where_clause = " WHERE title LIKE ? OR author LIKE ?";
    $search_term = "%" . $search_query . "%";
    $params = [$search_term, $search_term];
    $types = "ss";
}

// Fetch books based on search (or grab all if no search)
$sql = "SELECT id, title, author, status FROM books" . $where_clause . " ORDER BY title ASC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$books_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Library System</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .nav-links { 
            padding: 10px; 
            background: #eee; 
            margin-bottom: 20px; }
        .nav-links a { 
            margin-right: 15px;
            text-decoration: none;
            color: #333; 
            font-weight: bold; }
        .container { padding: 20px; margin-left: 300px; }
        
        /* Search box styling */
        .search-container {
             margin-bottom: 20px; 
             padding: 15px; background: #f9f9f9; 
             border: 1px solid #ddd; }
        .search-container input[type="text"] { 
            padding: 8px; 
            width: 300px; 
            border: 1px solid #ccc; 
            border-radius: 4px; }
        .search-container button { 
            padding: 8px 15px; 
            background: #007bff; 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 4px; }
        .search-container a.clear-btn { 
            margin-left:10px; 
            text-decoration: none; 
            color: #888; 
            border: 1px solid #ccc; 
            padding: 7px 12px; 
            border-radius:4px;}

        table {
             width: 100%; 
             border-collapse: collapse; 
             margin-top: 15px; 
             background: #fff; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        
        .badge-available {
             background: #28a745; 
             color: white;
              padding: 4px 8px; 
              border-radius: 12px; 
              font-size: 12px; 
              font-weight: bold; }
        .badge-borrowed {
             background: #ffc107;
             color: black; 
             padding: 4px 8px; 
             border-radius: 12px; 
             font-size: 12px; 
             font-weight: bold; }
        
        .btn-borrow { display: inline-block;
         padding: 6px 12px; 
         background: #007bff;
         color: white; text-decoration: none;
         border-radius: 4px; font-size: 14px;}
        .btn-disabled { display: inline-block;
         padding: 6px 12px; 
         background: #cccccc; 
         color:#666; 
         cursor: not-allowed;
          border-radius: 4px; 
          font-size: 14px;
        }

        /* sidebar */
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
     <div class="sidebar">

        <div class="logo">
            <i class="fa-solid fa-book-open"></i> Library
        </div>

        <div class="menu">

            <a href="dashboard.php" class="active">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <a href="../student/history.php">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Borrowing History</span>
            </a>

            <a href="../auth/logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

        </div>

    </div>


   
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Browse our collection below. You can only borrow books that are currently available.</p>
        
        <!-- Search Form -->
        <div class="search-container">
            <form action="dashboard.php" method="GET">
                <input type="text" name="search" placeholder="Search by book title or author..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Search</button>
                <?php if (!empty($search_query)): ?>
                    <a href="dashboard.php" class="clear-btn">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php 
        // Display success/error messages from redirecting pages (like borrow.php)
        if (isset($_GET['msg'])) {
            echo "<p style='color: green; background: #dfd; padding: 10px;'>" . htmlspecialchars($_GET['msg']) . "</p>";
        }
        if (isset($_GET['err'])) {
            echo "<p style='color: red; background: #fdd; padding: 10px;'>" . htmlspecialchars($_GET['err']) . "</p>";
        }
        ?>

        <!-- Books List -->
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books_result && $books_result->num_rows > 0): ?>
                    <?php while ($row = $books_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'available'): ?>
                                    <span class="badge-available">Available</span>
                                <?php else: ?>
                                    <span class="badge-borrowed">On Loan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Borrow Logic Button -->
                                <?php if ($row['status'] === 'available'): ?>
                                    <a href="borrow.php?book_id=<?php echo $row['id']; ?>" class="btn-borrow" onclick="return confirm('Do you want to borrow this book?');">Borrow</a>
                                <?php else: ?>
                                    <span class="btn-disabled">Not Available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">
                            <?php echo !empty($search_query) ? "No books match your search." : "No books available in the library yet."; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
<?php 
$stmt->close();
?>