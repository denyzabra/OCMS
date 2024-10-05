<?php
include('../includes/middleware.php');
include('../includes/db.php');

if ($_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$grades = $conn->query("
    SELECT courses.course_name, grades.grade, courses.course_description 
    FROM grades 
    JOIN courses ON grades.course_id = courses.id 
    WHERE grades.student_id = $student_id
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Student Reports</h2>
        <table id="reportTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($grade = $grades->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($grade['course_description']); ?></td>
                    <td><?php echo $grade['grade'] ?? 'Pending'; ?></td>
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
