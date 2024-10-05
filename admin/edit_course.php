<?php
include('../includes/middleware.php');
include('../includes/db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$course_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = filter_var($_POST['course_name'], FILTER_SANITIZE_STRING);
    $course_description = filter_var($_POST['course_description'], FILTER_SANITIZE_STRING);
    $instructor_id = filter_var($_POST['instructor_id'], FILTER_SANITIZE_NUMBER_INT);

    $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_description = ?, instructor_id = ? WHERE id = ?");
    $stmt->bind_param("ssii", $course_name, $course_description, $instructor_id, $course_id);

    if ($stmt->execute()) {
        $success_message = "Course updated successfully!";
    } else {
        $error_message = "Error updating course.";
    }
}

// Fetch course data to prefill the form
$course = $conn->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();
$instructors = $conn->query("SELECT id, username FROM users WHERE role = 'instructor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Course</h2>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_course.php?id=<?php echo $course_id; ?>">
            <div class="mb-3">
                <label for="course_name" class="form-label">Course Name</label>
                <input type="text" name="course_name" class="form-control" value="<?php echo $course['course_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="course_description" class="form-label">Course Description</label>
                <textarea name="course_description" class="form-control" required><?php echo $course['course_description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="instructor_id" class="form-label">Assign Instructor</label>
                <select name="instructor_id" class="form-control" required>
                    <?php while ($instructor = $instructors->fetch_assoc()): ?>
                        <option value="<?php echo $instructor['id']; ?>" <?php echo ($instructor['id'] == $course['instructor_id']) ? 'selected' : ''; ?>>
                            <?php echo $instructor['username']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Course</button>
        </form>

        <div class="text-center mt-3">
            <a href="courses.php" class="btn btn-secondary">Back to Course Management</a>
        </div>
    </div>
</body>
</html>
