<?php
include('../includes/middleware.php');
include('../includes/db.php');

if ($_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$courses = $conn->query("SELECT * FROM courses WHERE id NOT IN (SELECT course_id FROM enrollment WHERE student_id = {$_SESSION['user_id']})");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = filter_var($_POST['course_id'], FILTER_SANITIZE_NUMBER_INT);


    $stmt = $conn->prepare("INSERT INTO enrollment (student_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $_SESSION['user_id'], $course_id);

    if ($stmt->execute()) {
        $success_message = "Enrolled in course successfully!";
    } else {
        $error_message = "Error enrolling in course.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Courses</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Available Courses</h2>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="enroll.php">
            <div class="mb-3">
                <label for="course_id" class="form-label">Select a Course</label>
                <select name="course_id" class="form-control" required>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo $course['course_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enroll</button>
        </form>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
