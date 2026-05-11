<?php
session_start();

// Ensure user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include_once '../config/db.php';
$user_id = $_SESSION['user_id'];
$message = "";

// Handle Return Book logic
if (isset($_GET['return_id'])) {
    $record_id = intval($_GET['return_id']);

    // Process Return inside a transaction
    $conn->begin_transaction();

    try {
        // 1. Verify this record belongs to the user and is genuinely borrowed
        $check_stmt = $conn->prepare("SELECT book_id, status FROM borrow_records WHERE id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $record_id, $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Record not found.");
        }

        $record = $result->fetch_assoc();

        if ($record['status'] === 'returned') {
            throw new Exception("This book has already been returned.");
        }
        $book_id = $record['book_id'];
        $check_stmt->close();

        // 2. Mark as returned in borrow_records with today's date
        $today = date("Y-m-d");
        $update_record = $conn->prepare("UPDATE borrow_records SET return_date = ?, status = 'returned' WHERE id = ?");
        $update_record->bind_param("si", $today, $record_id);
        $update_record->execute();

        // 3. Mark book as 'available' again in the books table
        $update_book = $conn->prepare("UPDATE books SET status = 'available' WHERE id = ?");
        $update_book->bind_param("i", $book_id);
        $update_book->execute();

        $conn->commit();
        $message = "<p style='color: green; background: #dfd; padding: 10px;'>Book successfully returned!</p>";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "<p style='color: red; background: #fdd; padding: 10px;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Fetch user's borrowing history
$query = "
    SELECT 
        br.id AS record_id,
        b.title,
        b.author,
        br.borrow_date,
        br.return_date,
        br.status
    FROM borrow_records br
    INNER JOIN books b ON br.book_id = b.id
    WHERE br.user_id = ?
    ORDER BY br.borrow_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowing History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- <link rel="stylesheet" href="../assets/style.css"> -->
    <style>
        .nav-links {
            padding: 10px;
            background: #eee;
            margin-bottom: 20px;
        }

        .nav-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .container {
            padding: 20px;
            margin-left: 300px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #fff;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .badge-returned {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-borrowed {
            background: #ffc107;
            color: black;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-return {
            display: inline-block;
            padding: 5px 10px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
        }

        .btn-return:hover {
            background: #218838;
        }

        /* sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #002366, #001845);
            color: white;
            position: fixed;
            padding: 25px 20px;
        }

        .logo {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 50px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .menu a:hover,
        .menu .active {
            background: #0d6efd;
        }

        .menu i {
            font-size: 18px;
        }

        .logout {
            position: absolute;
            bottom: 30px;
            width: 85%;
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

            <a href="../student/dashboard.php">
                <i class="fa-solid fa-book"></i>
                <span>Catalogue</span>
            </a>

            <a href="../auth/logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

        </div>

    </div>

    <div class="container">
        <h2>My Borrowing History</h2>

        <?php echo $message; ?>

        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Borrowed On</th>
                    <th>Due / Return Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($history_result && $history_result->num_rows > 0): ?>
                    <?php while ($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['borrow_date'])); ?></td>
                            <td>
                                <?php
                                if ($row['status'] === 'borrowed') {
                                    if ($row['return_date']) {
                                        echo "Due: <strong style='color: #dc3545;'>" . date('M d, Y', strtotime($row['return_date'])) . "</strong>";
                                    } else {
                                        echo "<span style='color: #888;'>Pending admin assignment</span>";
                                    }
                                } else {
                                    echo "Returned on: <strong style='color: #28a745;'>" . date('M d, Y', strtotime($row['return_date'])) . "</strong>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'returned'): ?>
                                    <span class="badge-returned">Returned</span>
                                <?php else: ?>
                                    <span class="badge-borrowed">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'borrowed'): ?>
                                    <a href="history.php?return_id=<?php echo $row['record_id']; ?>"
                                        class="btn-return"
                                        onclick="return confirm('Confirm you are returning this book?');">
                                        Return Book
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 13px;">Done</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">You have no borrowing history.</td>
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

