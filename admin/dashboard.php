<?php
include('../includes/middleware.php');
include('../includes/db.php');

checkRole('admin');

// Fetching courses, instructors, and students
$courses = $conn->query("SELECT * FROM courses");
$instructors = $conn->query("SELECT * FROM users WHERE role = 'instructor'");
$students = $conn->query("SELECT * FROM users WHERE role = 'student'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 240px;
            background-color: #3c4b64;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a:hover {
            background-color: #303c52;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        @media print {
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate,
            .dataTables_length {
                display: none; 
            }
            h2 {
                text-decoration: underline; 
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h4 class="text-white">Admin Panel</h4>
        </div>
        <a href="add_course.php"><i class='bx bxs-book'></i> Add Course</a>
        <a href="manage_instructors.php"><i class='bx bxs-user'></i> Manage Instructors</a>
        <a href="manage_students.php"><i class='bx bxs-user'></i> Manage Students</a>
        <a href="view_reports.php"><i class='bx bxs-report'></i> View Reports</a>
        <a href="../logout.php"><i class='bx bxs-log-out'></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="text-center">Online Course Management System</h2>
            <p class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        
            <h4 class="mt-4">Courses</h4>
            <div class="text-center mb-3">
                <button id="export_csv" class="btn btn-secondary">Export CSV</button>
                <button id="export_pdf" class="btn btn-secondary">Export PDF</button>
                <button id="print_table" class="btn btn-secondary">Print</button>
            </div>
            <table id="coursesTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Instructor</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                    <tr id="course-<?php echo $course['id']; ?>">
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                        <td>
                            <?php 
                            $instructor_id = $course['instructor_id'];
                            $instructor = $conn->query("SELECT username FROM users WHERE id = $instructor_id")->fetch_assoc();
                            echo htmlspecialchars($instructor['username']);
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-danger" onclick="deleteCourse(<?php echo $course['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4 class="mt-4">Instructors</h4>
            <table id="instructorsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($instructor = $instructors->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($instructor['username']); ?></td>
                        <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                        <td>
                            <a href="edit_instructor.php?id=<?php echo $instructor['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_instructor.php?id=<?php echo $instructor['id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4 class="mt-4">Students</h4>
            <table id="studentsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td>
                            <a href="view_grades.php?student_id=<?php echo $student['id']; ?>" class="btn btn-info">View Grades</a>
                            <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.9/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.rawgit.com/sourabhbajaj/jquery.table2excel/master/src/jquery.table2excel.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#coursesTable, #instructorsTable, #studentsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: false,
                lengthChange: false
            });

            // Export to CSV, PDF, and Print functionality can be integrated here similar to courses.
            // (Add the previous export and print functions here for each table)
        });

        function deleteCourse(courseId) {
            if (confirm("Are you sure you want to delete this course?")) {
                $.ajax({
                    url: 'delete_course.php',
                    type: 'POST',
                    data: { course_id: courseId },
                    success: function(response) {
                        if (response.success) {
                            // Remove the course row from the table
                            $('#course-' + courseId).remove();
                            alert("Course deleted successfully!");
                        } else {
                            alert("Failed to delete course: " + response.message);
                        }
                    },
                    error: function() {
                        alert("An error occurred while deleting the course.");
                    }
                });
            }
        }
    </script>
</body>
</html>
