<?php
include('../includes/middleware.php');
include('../includes/db.php');

if ($_SESSION['role'] != 'instructor') {
    header("Location: ../login.php");
    exit();
}

$instructor_id = $_SESSION['user_id'];

// Fetch courses for the instructor
$courses = $conn->query("SELECT * FROM courses WHERE instructor_id = $instructor_id");

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Fetch enrolled students for the selected course
$students = [];
if ($course_id) {
    $students = $conn->query("
        SELECT users.username, users.email
        FROM enrollment
        JOIN users ON enrollment.student_id = users.id
        WHERE enrollment.course_id = $course_id
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
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
            <h4 class="text-white">Instructor Panel</h4>
        </div>
        <a href="dashboard.php">Dashboard</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="text-center">View Students</h2>

            <div class="mb-3">
                <label for="courseSelect" class="form-label">Select Course</label>
                <select id="courseSelect" class="form-select" onchange="location = this.value;">
                    <option value="">-- Select Course --</option>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <option value="view_students.php?course_id=<?php echo $course['id']; ?>" <?php echo $course_id == $course['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <?php if ($course_id && $students->num_rows > 0): ?>
                <table class="table table-bordered">
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
            <?php elseif ($course_id): ?>
                <div class="alert alert-warning">No students enrolled in this course.</div>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
