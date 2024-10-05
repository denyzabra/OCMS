<?php
include('../includes/middleware.php');
include('../includes/db.php');
if ($_SESSION['role'] != 'instructor') {
    header("Location: ../login.php");
    exit();
}

$instructor_id = $_SESSION['user_id'];
$enrollments = $conn->query("
    SELECT users.username AS student_name, courses.course_name, enrollment.created_at 
    FROM enrollment 
    JOIN users ON enrollment.student_id = users.id 
    JOIN courses ON enrollment.course_id = courses.id
    WHERE courses.instructor_id = $instructor_id
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Reports</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Instructor Reports</h2>
        <table id="reportTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Course Name</th>
                    <th>Enrollment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($enrollment = $enrollments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($enrollment['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button class="btn btn-primary mt-3" onclick="printTable()">Print Report</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#reportTable').DataTable();
        });

        function printTable() {
            var printContents = document.getElementById('reportTable').outerHTML;
            var win = window.open();
            win.document.write('<html><head><title>Print Report</title>');
            win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">');
            win.document.write('</head><body>');
            win.document.write(printContents);
            win.document.write('</body></html>');
            win.document.close();
            win.print();
        }
    </script>
</body>
</html>
