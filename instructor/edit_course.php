<?php
include('../includes/middleware.php');
include('../includes/db.php');

if ($_SESSION['role'] != 'instructor') {
    header("Location: ../login.php");
    exit();
}

// Fetch the course ID from the URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$course_id = intval($_GET['id']);

// Fetch the course details
$course = $conn->query("SELECT * FROM courses WHERE id = $course_id AND instructor_id = " . $_SESSION['user_id'])->fetch_assoc();

if (!$course) {
    header("Location: dashboard.php");
    exit();
}

// Handle the update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = filter_var($_POST['course_name'], FILTER_SANITIZE_STRING);
    $course_description = filter_var($_POST['course_description'], FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $course_name, $course_description, $course_id);

    if ($stmt->execute()) {
        $success_message = "Course updated successfully!";
    } else {
        $error_message = "Error updating course.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - Instructor Panel</title>
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
        <a href="view_students.php">View Students</a>
        <a href="view_reports.php">View Reports</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="text-center">Edit Course</h2>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_course.php?id=<?php echo $course_id; ?>">
                <div class="mb-3">
                    <label for="course_name" class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="course_description" class="form-label">Course Description</label>
                    <textarea name="course_description" class="form-control" rows="3" required><?php echo htmlspecialchars($course['course_description']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </form>
            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</body>
</html>
