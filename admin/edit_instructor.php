<?php
include('../includes/middleware.php');
include('../includes/db.php');

checkRole('admin');

if (!isset($_GET['id'])) {
    header("Location: manage_instructors.php");
    exit();
}

$id = intval($_GET['id']);
$instructor = $conn->query("SELECT * FROM users WHERE id = $id AND role = 'instructor'")->fetch_assoc();

if (!$instructor) {
    header("Location: manage_instructors.php");
    exit();
}

// Handle the update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $id);

    if ($stmt->execute()) {
        $success_message = "Instructor updated successfully!";
    } else {
        $error_message = "Error updating instructor.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Instructor</title>
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
        <a href="manage_instructors.php" class="active">Manage Instructors</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="view_reports.php">View Reports</a>
        <a href="export_courses.php">Export Courses</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="text-center">Edit Instructor</h2>
            <p class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_instructor.php?id=<?php echo $id; ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($instructor['username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($instructor['email']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Instructor</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
