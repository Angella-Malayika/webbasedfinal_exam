# Project Report: Library Management System

## 1. System Description
The Web-Based Library Management System is an interactive database application designed to simplify library operations. Built with PHP, MySQL, HTML, and CSS, it automates the management of books and user borrowing processes. 

The system operates across two main roles:
- **Admin:** Has full oversight. They can add new books to the catalogue, manage existing books, and view a comprehensive list of all borrowing records to keep track of library inventory.
- **Student:** Can view the library catalogue, search for specific titles or authors, borrow books that are marked as "available," and review their personal borrowing history.

## 2. Database Structure
The system relies on a relational MySQL database named `library` consisting of three primary tables:

### 1. `users`
Stores user accounts for both students and admins.
- `id` (INT, Primary Key, Auto Increment)
- `username` (VARCHAR 50, Unique)
- `email` (VARCHAR 100, Unique)
- `password` (VARCHAR 255) - Stores bcrypt hashed passwords
- `role` (VARCHAR 20) - 'student' or 'admin'
- `created_at` (TIMESTAMP)

### 2. `books`
Stores the inventory of the library.
- `id` (INT, Primary Key, Auto Increment)
- `title` (VARCHAR 150)
- `author` (VARCHAR 100)
- `status` (ENUM) - 'available' or 'borrowed'
- `created_at` (TIMESTAMP)

### 3. `borrow_records`
A junction/transaction table tracking the borrowing history.
- `id` (INT, Primary Key, Auto Increment)
- `user_id` (INT, Foreign Key referencing `users.id`)
- `book_id` (INT, Foreign Key referencing `books.id`)
- `borrow_date` (TIMESTAMP)
- `return_date` (TIMESTAMP, NULLable)
- `status` (ENUM) - 'borrowed' or 'returned'

## 3. Key Interface Screenshots (Placeholders)
*(Please insert the actual screenshots in your document below before converting to PDF)*

1. **Login & Registration Screen**
   *(Insert Screenshot Here)*
   *Description: Secure entry point for authentication and account creation.*

2. **Student Dashboard (Catalogue)**
   *(Insert Screenshot Here)*
   *Description: Displays all books with the status, sidebar navigation, and search functionality.*

3. **Student Borrowing History**
   *(Insert Screenshot Here)*
   *Description: Shows the logged-in student's past and current borrowed books.*

4. **Admin Dashboard & Manage Books**
   *(Insert Screenshot Here)*
   *Description: Interface where admins can view and manage current library stock.*

5. **Admin Add Book & Borrow Records**
   *(Insert Screenshot Here)*
   *Description: Admin interface showing the form to add new books and the list of all borrowing records.*

## 4. Challenges Faced & Lessons Learned

### Challenges Faced
- **Session Management & Access Control:** Ensuring that students could not access admin pages and vice versa required careful handling of PHP `$_SESSION` variables and redirect logic.
- **UI Consistency:** Developing a fixed sidebar and ensuring the main content did not overlap (by applying proper `margin-left` styling) took some troubleshooting and CSS adjustments.
- **Database Joins for Records:** Displaying proper borrowing history meant constructing SQL statements to join three tables (`users`, `books`, and `borrow_records`) correctly to fetch titles alongside borrower details.

### Lessons Learned
- **Separation of Concerns:** Dividing the file structure into logical directories (`admin/`, `student/`, `auth/`, and `config/`) proved essential for code maintainability and organization.
- **Security Best Practices:** Learned the importance of using hashed passwords for authentication and using prepared statements for database queries to prevent SQL Injection vulnerabilities.
- **Relational Databases:** Gained hands-on experience designing relational tables and enforcing referential integrity with cascading foreign keys (`ON DELETE CASCADE`), ensuring that deleted users or books smoothly updated the system records without breaking dependencies.
