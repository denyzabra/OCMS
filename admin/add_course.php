<?php
include('../includes/middleware.php');
include('../includes/db.php');
checkRole('admin');

// Fetch statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$total_instructors = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'instructor'")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];

// Fetch recent enrollments
$recent_enrollments = $conn->query("
    SELECT users.username, courses.course_name, enrollment.created_at
    FROM enrollment
    JOIN users ON enrollment.student_id = users.id
    JOIN courses ON enrollment.course_id = courses.id
    ORDER BY enrollment.created_at DESC
    LIMIT 5
");

// Handle the form submission for adding a course
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name']);
    $course_description = trim($_POST['course_description']);
    $instructor_id = $_POST['instructor_id'];

    // Check if the course name already exists
    $stmt_check = $conn->prepare("SELECT id FROM courses WHERE course_name = ?");
    $stmt_check->bind_param("s", $course_name);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $errors[] = "A course with this name already exists. Please choose a different name.";
    }
    
    $stmt_check->close();

    // Insert the course if no errors
    if (count($errors) === 0) {
        $stmt = $conn->prepare("INSERT INTO courses (course_name, course_description, instructor_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $course_name, $course_description, $instructor_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Course added successfully!'); window.location.href='add_course.php';</script>";
        } else {
            echo "<script>alert('Error adding course.');</script>";
        }
        
        $stmt->close();
    }
}

// Fetch instructors for dropdown
$instructors = $conn->query("SELECT id, username FROM users WHERE role = 'instructor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .char-counter {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const courseDescription = document.getElementById('course_description');
            const charCount = document.getElementById('char_count');

            // Update character count as user types
            courseDescription.addEventListener('input', function () {
                const currentLength = courseDescription.value.length;
                charCount.textContent = `${currentLength}/280`;

                if (currentLength > 280) {
                    charCount.classList.add('text-danger');
                } else {
                    charCount.classList.remove('text-danger');
                }
            });
        });

        function validateForm() {
            const courseName = document.getElementById('course_name').value.trim();
            const courseDescription = document.getElementById('course_description').value.trim();
            const instructorId = document.getElementById('instructor_id').value;

            let errors = [];

            if (!instructorId) {
                errors.push('Please select an instructor.');
            }

            if (courseDescription.length > 280) {
                errors.push('Course description cannot exceed 280 characters.');
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h4 class="text-white">Admin Panel</h4>
        </div>
        <a href="add_course.php"><i class='bx bxs-book'></i> Add Course</a>
        <a href="manage_instructors.php"><i class='bx bxs-user'></i> Manage Instructors</a>
        <a href="manage_students.php"><i class='bx bxs-user'></i> Manage Students</a>
        <a href="view_reports.php"><i class='bx bxs-report'></i> View Reports</a>
        <a href="../logout.php"><i class='bx bxs-log-out'></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">Add New Course</h1>

            <?php if (count($errors) > 0): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="add_course.php" method="POST" class="mb-4" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label for="course_name" class="form-label">Course Name</label>
                    <input type="text" class="form-control" id="course_name" name="course_name" required>
                </div>
                <div class="mb-3">
                    <label for="course_description" class="form-label">Course Description</label>
                    <textarea class="form-control" id="course_description" name="course_description" rows="3" maxlength="280" required></textarea>
                    <div class="char-counter" id="char_count">0/280</div>
                </div>
                <div class="mb-3">
                    <label for="instructor_id" class="form-label">Instructor</label>
                    <select class="form-select" id="instructor_id" name="instructor_id" required>
                        <option value="">Select Instructor</option>
                        <?php while ($instructor = $instructors->fetch_assoc()): ?>
                            <option value="<?php echo $instructor['id']; ?>"><?php echo htmlspecialchars($instructor['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Add Course</button>
            </form>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <h2 class="mb-0"><?php echo $total_students; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Instructors</h5>
                            <h2 class="mb-0"><?php echo $total_instructors; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Courses</h5>
                            <h2 class="mb-0"><?php echo $total_courses; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Recent Enrollments
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($enrollment = $recent_enrollments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($enrollment['username']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($enrollment['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Quick Actions
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="add_course.php" class="btn btn-primary">Add New Course</a>
                                <a href="../admin/manage_users.php" class="btn btn-success">Add New User</a>
                                <a href="../admin/reports.php" class="btn btn-info">Generate Reports</a>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
