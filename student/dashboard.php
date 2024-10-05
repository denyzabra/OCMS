<?php
include('../includes/middleware.php');
include('../includes/db.php');

if ($_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$enrolled_courses = $conn->query("
    SELECT courses.course_name, courses.course_description, grades.grade 
    FROM enrollment 
    LEFT JOIN courses ON enrollment.course_id = courses.id
    LEFT JOIN grades ON enrollment.course_id = grades.course_id AND enrollment.student_id = grades.student_id
    WHERE enrollment.student_id = $student_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h4 class="text-white">Student Panel</h4>
        </div>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="enroll.php">Enroll in Course</a>
        <a href="student_reports.php">View Reports</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="text-center">Student Dashboard</h2>
            <h4>Your Enrolled Courses</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($enrolled_courses->num_rows > 0): ?>
                        <?php while ($course = $enrolled_courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                            <td><?php echo $course['grade'] ?? 'Pending'; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">You are not enrolled in any courses.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="text-center mt-3">
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
