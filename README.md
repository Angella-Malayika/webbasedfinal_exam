 1.Web-Based Library Management System
 (a) System Description
The Web-Based Library Management System is an interactive database application designed to simplify library operations. Built with PHP, MySQL, HTML, and CSS, it automates the management of books and user borrowing processes.

The system operates across two main roles:
Admin:
 Has full oversight. They can add new books to the catalogue, manage existing books, and view a comprehensive list of all borrowing records to keep track of library inventory.
Student:
 Can view the library catalogue, search for specific titles or authors, borrow books that are marked as "available" and review their personal borrowing history.

2.Access the System:
    Open your browser and go to `http://localhost/webbasedfinal_exam/index.php` or `http://localhost/webbasedfinal_exam/`.

(b) Default Login Credentials
Instructions to create your own accounts:
1. Navigate to `http://localhost/webbasedfinal_exam/auth/register.php`.
2. Register a new account (all accounts are created as `student` by default).


The seeded emails currently in the database are:
 Admin: `angella.lib@gmail.com` 123456
Student: `nakanwagi.angela@stud.umu.ac.ug`12345678 / `travoradmin@gmail.com`1234567
The system relies on a relational MySQL database named "library" consisting of three primary tables:

1.users
Stores user accounts for both students and admins.
 id (INT, Primary Key, Auto Increment)
 username (VARCHAR 50, Unique)
 email (VARCHAR 100, Unique)
 password(VARCHAR 255) - Stores bcrypt hashed passwords
 role (VARCHAR 20) - 'student' or 'admin'
 created_at (TIMESTAMP)

2.books
Stores the inventory of the library.
 id (INT, Primary Key, Auto Increment)
 title (VARCHAR 150)
 author (VARCHAR 100)
 status (ENUM)'available' or 'borrowed'
 created_at(TIMESTAMP)

 3.borrow_records
A junction/transaction table tracking the borrowing history.
 id (INT, Primary Key, Auto Increment)
 user_id (INT, Foreign Key referencing users.id)
 book_id (INT, Foreign Key referencing books.id)
 borrow_date (TIMESTAMP)
 return_date (TIMESTAMP, NULLable)
 status (ENUM)'borrowed' or 'returned'

 3 Key Interface Screenshots
 Login & Registration Screen

   ![alt text](<assets/images/Screenshot 2026-05-08 191837.png>)
   
   
   ![alt text](<assets/images/Screenshot 2026-05-08 192248.png>)
   Description:
   default login(nakanwagi.angela@stud.umu.ac.ug
   password "12345678")note this is login to student dashaboard
   default login to admin dashboard(angella.lib@gmail.com
   password "123456")
    Secure entry point for authentication and account creation.
    for a student to login must have(stud) in their email
    for an admin to login must have (lib) in their email.


     Challenges Faced
Session Management & Access Control: 
Ensuring that students could not access admin pages and vice versa required careful handling of PHP `$_SESSION` variables and redirect logic.
UI Consistency:
 Developing a fixed sidebar and ensuring the main content did not overlap (by applying proper `margin-left` styling) took some troubleshooting and CSS adjustments.
Database Joins for Records:
 Displaying proper borrowing history meant constructing SQL statements to join three tables (`users`, `books`, and `borrow_records`) correctly to fetch titles alongside borrower details.

Lessons Learned
Separation of Concerns: Dividing the file structure into logical directories (`admin/`, `student/`, `auth/`, and `config/`) proved essential for code maintainability and organization.
Security Best Practices: Learned the importance of using hashed passwords for authentication and using prepared statements for database queries to prevent SQL Injection vulnerabilities.
Relational Databases: Gained hands-on experience designing relational tables and enforcing referential integrity with cascading foreign keys (`ON DELETE CASCADE`), ensuring that deleted users or books smoothly updated the system records without breaking dependencies.

