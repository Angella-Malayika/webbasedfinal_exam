<?php
session_start();

// Protect the page: Only logged-in users with the 'admin' role can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include_once '../config/db.php';

// Handle setting of return date
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_return_date'])) {
    $record_id = intval($_POST['record_id']);
    $return_date = $_POST['return_date'];
    
    // Fetch borrow_date to validate minimum days
    $check_stmt = $conn->prepare("SELECT borrow_date FROM borrow_records WHERE id = ?");
    $check_stmt->bind_param("i", $record_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $borrow_date = new DateTime($row['borrow_date']);
        $assigned_date = new DateTime($return_date);
        $interval = $borrow_date->diff($assigned_date);
        
        if ($assigned_date >= $borrow_date && $interval->days >= 7) {
            $update_stmt = $conn->prepare("UPDATE borrow_records SET return_date = ? WHERE id = ?");
            $update_stmt->bind_param("si", $return_date, $record_id);
            if ($update_stmt->execute()) {
                $message = "<div style='color: green; background: #dfd; padding: 10px; margin-bottom: 10px;'>Return date assigned successfully.</div>";
            } else {
                $message = "<div style='color: red; background: #fdd; padding: 10px; margin-bottom: 10px;'>Error assigning return date.</div>";
            }
            $update_stmt->close();
        } else {
            $message = "<div style='color: red; background: #fdd; padding: 10px; margin-bottom: 10px;'>The return date must be at least 7 days after the borrow date.</div>";
        }
    }
    $check_stmt->close();
}

// Prepare a complex query linking borrow_records with books AND users
// We use INNER JOINs to pull down readable names instead of raw IDs.
$query = "
    SELECT 
        borrow_records.id AS record_id,
        users.username AS student_name,
        b.title AS book_title,
        borrow_records.borrow_date,
        borrow_records.return_date,
        borrow_records.status
    FROM borrow_records
    INNER JOIN users ON borrow_records.user_id = users.id
    INNER JOIN books b ON borrow_records.book_id = b.id
    ORDER BY borrow_records.borrow_date DESC
";

$records_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Borrowing Records - Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
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
        .table-container { padding: 50px; 
        margin-left: 280px;
        width: calc(100% - 280px); 
        box-sizing: border-box; }
         .table-container, h2, p{
            padding: 20px;

         }
        
        table { width: 100%; 
        border-collapse: collapse;
         margin-top: 15px; background: #fff; }
        table, th, td { 
        border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        
        .badge-returned { background: #28a745; 
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

            
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <a href="../admin/dashboard.php">
                <i class="fa-solid fa-book"></i>
                <span>Dashboard</span>
            </a>

            <a href="../admin/borrow_records.php">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Borrowing Records</span>
            </a>
             <a href="../admin/manage_book.php">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Manage Books</span>


            <a href="../auth/logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

        </div>

    </div>

    <div class="table-container">
        <h2>All Borrowing Activity</h2>
        
        <p>This page lists all books currently checked out and historic returns.</p>
        
        <?php echo $message; ?>

        <table>
            <thead>
                <tr>
                    <th>Record ID</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Assigned/Return Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($records_result && $records_result->num_rows > 0): ?>
                    <?php while ($row = $records_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['record_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['borrow_date'])); ?></td>
                            <td>
                                <?php 
                                    if ($row['return_date']) {
                                        echo date('M d, Y', strtotime($row['return_date']));
                                    } else {
                                        echo "<span style='color: #888;'>Not assigned</span>";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'returned'): ?>
                                    <span class="badge-returned">Returned</span>
                                <?php else: ?>
                                    <span class="badge-borrowed">Borrowed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'borrowed' && !$row['return_date']): ?>
                                    <form method="POST" style="margin: 0; display: flex; gap: 5px;">
                                        <input type="hidden" name="record_id" value="<?php echo $row['record_id']; ?>">
                                        <?php 
                                            // Calculate minimum date (7 days from borrow date)
                                            $min_date = date('Y-m-d\TH:i', strtotime($row['borrow_date'] . ' + 7 days'));
                                        ?>
                                        <input type="datetime-local" name="return_date" required min="<?php echo $min_date; ?>" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                                        <button type="submit" name="set_return_date" style="padding: 4px 8px; background: #002366; color: white; border: none; border-radius: 4px; cursor: pointer;">Assign</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No borrowing records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>