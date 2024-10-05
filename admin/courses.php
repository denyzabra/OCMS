<?php
include('../includes/middleware.php');
include('../includes/db.php');
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$courses = $conn->query("SELECT courses.*, users.username as instructor FROM courses 
                         LEFT JOIN users ON courses.instructor_id = users.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Manage Courses</h2>
        <div class="text-end mb-3">
            <a href="add_course.php" class="btn btn-success">Add New Course</a>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Instructor</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $courses->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $course['course_name']; ?></td>
                    <td><?php echo $course['instructor']; ?></td>
                    <td><?php echo $course['course_description']; ?></td>
                    <td>
                        <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_course.php?id=<?php echo $course['id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="text-center mt-3">
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>
