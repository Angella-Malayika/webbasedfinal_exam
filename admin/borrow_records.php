<?php
session_start();

// Protect the page: Only logged-in users with the 'admin' role can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include_once '../config/db.php';

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
        .nav-links { padding: 10px; background: #eee; margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; }
        .table-container { padding: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        
        .badge-returned { background: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-borrowed { background: #ffc107; color: black; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
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

    <div class="table-container">
        <h2>All Borrowing Activity</h2>
        
        <p>This page lists all books currently checked out and historic returns.</p>

        <table>
            <thead>
                <tr>
                    <th>Record ID</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Returned Date</th>
                    <th>Status</th>
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
                                        echo "<span style='color: #888;'>Not returned yet</span>";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'returned'): ?>
                                    <span class="badge-returned">Returned</span>
                                <?php else: ?>
                                    <span class="badge-borrowed">Active Loan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No borrowing activity found yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>