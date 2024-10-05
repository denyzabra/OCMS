<?php
include('../includes/middleware.php');
include('../includes/db.php');

checkRole('admin');

// Fetch total counts
$total_students = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$total_instructors = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'instructor'")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];

// Fetch students
$students = $conn->query("SELECT * FROM users WHERE role = 'student'");

// Fetch instructors
$instructors = $conn->query("SELECT * FROM users WHERE role = 'instructor'");

// Fetch courses
$courses = $conn->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
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
            transition: background-color 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #303c52;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h4 class="text-white">Admin Panel</h4>
        </div>
        <a href="add_course.php">Add Course</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="view_reports.php" class="active">View Reports</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="text-center">Reports</h2>
            <p class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <p class="card-text"><?php echo $total_students; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Instructors</h5>
                            <p class="card-text"><?php echo $total_instructors; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Total Courses</h5>
                            <p class="card-text"><?php echo $total_courses; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <h4>Students List</h4>
            <button class="btn btn-primary mb-3" onclick="printTable('studentsTable')">Print Students Report</button>
            <table id="studentsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4>Instructors List</h4>
            <button class="btn btn-primary mb-3" onclick="printTable('instructorsTable')">Print Instructors Report</button>
            <table id="instructorsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($instructor = $instructors->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($instructor['username']); ?></td>
                        <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4>Courses List</h4>
            <button class="btn btn-primary mb-3" onclick="printTable('coursesTable')">Print Courses Report</button>
            <table id="coursesTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Instructor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                        <td>
                            <?php 
                            $instructor_id = $course['instructor_id'];
                            $instructor = $conn->query("SELECT username FROM users WHERE id = $instructor_id")->fetch_assoc();
                            echo htmlspecialchars($instructor['username']);
                            ?>
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
    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable();
            $('#instructorsTable').DataTable();
            $('#coursesTable').DataTable();
        });

        function printTable(tableId) {
            var printContents = document.getElementById(tableId).outerHTML;
            var win = window.open();
            win.document.write('<html><head><title>Print Report</title>');
            win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">');
            win.document.write('</head><body>');
            win.document.write(printContents);
            win.document.write('</body></html>');
            win.document.close();
            win.print();
        }
    </script>
</body>
</html>
