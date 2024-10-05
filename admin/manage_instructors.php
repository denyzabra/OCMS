<?php
include('../includes/middleware.php');
include('../includes/db.php');

checkRole('admin');

// Handle adding a new instructor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_instructor'])) {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'instructor';

    // Check if the email already exists
    $emailCheckStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $emailCheckStmt->bind_param("s", $email);
    $emailCheckStmt->execute();
    $emailCheckStmt->store_result();

    if ($emailCheckStmt->num_rows > 0) {
        $error_message = "This email is already in use. Please use a different email.";
    } else {
        // Proceed with adding the instructor
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);

        if ($stmt->execute()) {
            $success_message = "Instructor added successfully!";
        } else {
            $error_message = "Error adding instructor.";
        }
    }

    $emailCheckStmt->close();
}

// Handle deleting an instructor
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'instructor'");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_message = "Instructor deleted successfully!";
    } else {
        $error_message = "Error deleting instructor.";
    }
}

// Fetch all instructors
$instructors = $conn->query("SELECT * FROM users WHERE role = 'instructor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Instructors</title>
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
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="text-center">Manage Instructors</h2>
            <p class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="manage_instructors.php" class="mb-4">
                <h4>Add New Instructor</h4>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="add_instructor" class="btn btn-primary">Add Instructor</button>
            </form>

            <h4>Existing Instructors</h4>
            <table class="table table-bordered">
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
                            <a href="manage_instructors.php?delete_id=<?php echo $instructor['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this instructor?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
