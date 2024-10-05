<?php
include('../includes/middleware.php');
include('../includes/db.php');

if ($_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$enrolled_courses = $conn->query("SELECT courses.course_name, courses.course_description, grades.grade 
                                  FROM enrollment 
                                  LEFT JOIN courses ON enrollment.course_id = courses.id
                                  LEFT JOIN grades ON enrollment.course_id = grades.course_id AND enrollment.student_id = grades.student_id
                                  WHERE enrollment.student_id = $student_id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">My Courses</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $enrolled_courses->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $course['course_name']; ?></td>
                    <td><?php echo $course['course_description']; ?></td>
                    <td><?php echo $course['grade'] ?? 'Not graded yet'; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="text-center mt-3">
            <a href="enroll.php" class="btn btn-primary">Enroll in More Courses</a>
        </div>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
