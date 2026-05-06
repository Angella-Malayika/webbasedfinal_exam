<?php
session_start();

// Ensure the user is a logged-in student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include_once '../config/db.php';

// Check if a book_id was passed in the URL
if (!isset($_GET['book_id']) || empty($_GET['book_id'])) {
    header("Location: dashboard.php?err=No book selected.");
    exit();
}

$book_id = intval($_GET['book_id']);
$user_id = $_SESSION['user_id'];
$today_date = date("Y-m-d");

// Start Database Transaction to ensure data integrity
$conn->begin_transaction();

try {
    // 1. Check if the book exists and is available.
    // We use "FOR UPDATE" to lock the row temporarily so two students can't borrow it at the absolute exact millisecond.
    $check_stmt = $conn->prepare("SELECT status FROM books WHERE id = ? FOR UPDATE");
    $check_stmt->bind_param("i", $book_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Book not found.");
    }
    
    $book = $result->fetch_assoc();
    
    if ($book['status'] !== 'available') {
        throw new Exception("Sorry, this book is already on loan.");
    }
    $check_stmt->close();

    // 2. Insert into borrow_records
    $borrow_stmt = $conn->prepare("INSERT INTO borrow_records (user_id, book_id, borrow_date, status) VALUES (?, ?, ?, 'borrowed')");
    $borrow_stmt->bind_param("iis", $user_id, $book_id, $today_date);
    $borrow_stmt->execute();
    
    // 3. Update the books table status to 'borrowed'
    $update_stmt = $conn->prepare("UPDATE books SET status = 'borrowed' WHERE id = ?");
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    // Commit the transaction
    $conn->commit();
    
    // Redirect back to dashboard with success message
    header("Location: dashboard.php?msg=Book borrowed successfully! Enjoy your reading.");
    
    $borrow_stmt->close();
    $update_stmt->close();

} catch (Exception $e) {
    // If anything fails, rollback the transaction so the database doesn't get messed up
    $conn->rollback();
    
    // Redirect back with the error message
    $error_msg = urlencode($e->getMessage());
    header("Location: dashboard.php?err=" . $error_msg);
}

exit();