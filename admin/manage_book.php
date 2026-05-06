<?php
session_start();

// Protect the page: Only logged-in users with the 'admin' role can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include_once '../config/db.php';

$message = '';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    // Check if book is currently borrowed before deleting (prevent breaking foreign keys)
    $check_stmt = $conn->prepare("SELECT status FROM books WHERE id = ?");
    $check_stmt->bind_param("i", $delete_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $book = $result->fetch_assoc();
    
    if ($book && $book['status'] === 'borrowed') {
        $message = "<p style='color: red; padding: 10px; background: #fdd;'>Cannot delete a book that is currently on loan. Please wait for the student to return it.</p>";
    } else {
        $del_stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $del_stmt->bind_param("i", $delete_id);
        if ($del_stmt->execute()) {
            $message = "<p style='color: green; padding: 10px; background: #dfd;'>Book deleted successfully.</p>";
        } else {
            $message = "<p style='color: red;'>Error deleting book.</p>";
        }
        $del_stmt->close();
    }
    $check_stmt->close();
}

// Fetch all books
$query = "SELECT id, title, author, status, created_at FROM books ORDER BY created_at DESC";
$books_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .nav-links { padding: 10px; background: #eee; margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; }
        .table-container { padding: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        
        .btn-delete { color: white; background-color: #dc3545; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-delete:hover { background-color: #c82333; }
        
        .badge-available { background: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
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
        <h2>Manage Library Catalogue</h2>
        
        <?php echo $message; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books_result && $books_result->num_rows > 0): ?>
                    <?php while ($row = $books_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'available'): ?>
                                    <span class="badge-available">Available</span>
                                <?php else: ?>
                                    <span class="badge-borrowed">Borrowed</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <!-- Delete link with JavaScript confirmation prompt -->
                                <a href="manage_book.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this book? This cannot be undone.');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No books found in the library catalogue.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>