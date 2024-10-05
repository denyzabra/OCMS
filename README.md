# OCMS

# Online Course Management System (OCMS)

## Overview
The **Online Course Management System (OCMS)** is a web application designed to facilitate the management of courses, students, and instructors in an academic environment. This system provides role-based dashboards for administrators, instructors, and students, allowing efficient and organized management of the academic workflow.

## Features
### Admin
- **Manage Courses**: Create, edit, delete, and view all courses.
- **Manage Instructors**: Full CRUD functionality to manage instructor profiles.
- **Manage Students**: Full CRUD functionality to manage student profiles.
- **View Reports**: Generate detailed reports on courses, instructors, and students, with the ability to print or export data using DataTables.
  
### Instructor
- **View Courses**: Instructors can view the courses they are teaching.
- **View Enrolled Students**: Instructors can see students enrolled in their courses.
- **Manage Grades**: Instructors can assign and manage student grades for their courses.
- **Generate Reports**: Instructors can generate reports on course performance with print/export options using DataTables.

### Student
- **View Enrolled Courses**: Students can see the courses they are enrolled in.
- **View Grades**: Students can track their grades for completed courses.
- **Generate Reports**: Students can generate and print their academic reports.

## Technologies Used
- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, Bootstrap, JavaScript
- **XAMPP**: Local server environment used for development and testing.
- **DataTables**: For displaying and exporting reports in table format with print options.

## Installation
1. Clone the repository to your local machine:
    ```bash
    git clone https://github.com/your-username/ocms.git
    ```

2. Open XAMPP and start both the **Apache** and **MySQL** services.

3. Import the database:
    - Create a new MySQL database (e.g., `ocms_db`).
    - Import the SQL file from the `database/ocms.sql` folder into your MySQL database.

4. Update the database connection:
    - Open `includes/db.php` and update the database connection parameters (host, username, password, database name).

    ```php
    $conn = new mysqli('localhost', 'root', '', 'ocms_db');
    ```

5. Run the application:
    - Open your web browser and navigate to `http://localhost/ocms`.

## Credentials
Use the following credentials to log in to the system:

### Admin
- **Email**: `admin@university.ac.ug`
- **Password**: `admin123`

### Instructor
- **Email**: `patricia.kato@university.ac.ug`
- **Password**: `instructor123`

### Student
- **Email**: `test@gmail.com`
- **Password**: `test`
