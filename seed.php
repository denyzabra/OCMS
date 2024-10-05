<?php
include('includes/db.php');

require 'vendor/autoload.php';
$faker = Faker\Factory::create('en_UG');

// Clear existing data
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['users', 'courses', 'enrollment', 'grades'];
foreach ($tables as $table) {
    $conn->query("TRUNCATE TABLE $table");
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Create Admin User
$admin_password = password_hash('admin123', PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$admin_username = 'Admin User';
$admin_email = 'admin@university.ac.ug';
$admin_role = 'admin';
$stmt->bind_param("ssss", $admin_username, $admin_email, $admin_password, $admin_role);
$stmt->execute();

// Create Students
$students = [];
$ugandan_surnames = ['Mukasa', 'Opio', 'Okello', 'Kato', 'Bbosa', 'Namukasa', 'Ssali', 'Tumwebaze', 'Byaruhanga', 'Namutebi'];
for ($i = 0; $i < 10; $i++) {
    $student_username = $faker->firstName . " " . $ugandan_surnames[array_rand($ugandan_surnames)];
    $student_email = strtolower(str_replace(' ', '.', $student_username)) . '@student.university.ac.ug';
    $student_password = password_hash('student123', PASSWORD_BCRYPT);
    $student_role = 'student';
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $student_username, $student_email, $student_password, $student_role);
    $stmt->execute();
    
    $students[] = $conn->insert_id;
}

// Create Instructors
$instructors = [];
$titles = ['Dr.', 'Prof.', 'Mr.', 'Ms.', 'Mrs.'];
for ($i = 0; $i < 5; $i++) {
    $title = $titles[array_rand($titles)];
    $instructor_username = $title . " " . $faker->firstName . " " . $ugandan_surnames[array_rand($ugandan_surnames)];
    $instructor_email = strtolower(str_replace(['Dr.', 'Prof.', 'Mr.', 'Ms.', 'Mrs.', ' '], ['', '', '', '', '', '.'], $instructor_username)) . '@university.ac.ug';
    $instructor_password = password_hash('instructor123', PASSWORD_BCRYPT);
    $instructor_role = 'instructor';
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $instructor_username, $instructor_email, $instructor_password, $instructor_role);
    $stmt->execute();
    
    $instructors[] = $conn->insert_id;
}

// Create Courses
$course_data = [
    ['Mathematics', 'Calculus I', 'Introduction to limits, derivatives, and integrals'],
    ['Physics', 'Mechanics', 'Study of motion, forces, energy, and momentum'],
    ['Computer Science', 'Programming Fundamentals', 'Introduction to programming concepts using Python'],
    ['Biology', 'Cell Biology', 'Study of cellular structures and functions'],
    ['Economics', 'Microeconomics', 'Analysis of individual markets and decision-making'],
    ['Chemistry', 'Organic Chemistry', 'Study of carbon-based compounds'],
    ['History', 'World History', 'Overview of major historical events and their impact'],
    ['English', 'Academic Writing', 'Development of academic writing and research skills'],
    ['Psychology', 'Introduction to Psychology', 'Overview of human behavior and mental processes'],
    ['Art', 'Art History', 'Study of art movements throughout history']
];

$course_ids = [];
foreach ($course_data as $course) {
    $course_name = $course[0];
    $course_title = $course[1];
    $course_description = $course[2];
    $instructor_id = $instructors[array_rand($instructors)];
    
    $stmt = $conn->prepare("INSERT INTO courses (course_name, course_title, course_description, instructor_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $course_name, $course_title, $course_description, $instructor_id);
    $stmt->execute();
    
    $course_ids[] = $conn->insert_id;
}

// Enroll Students in Courses
foreach ($students as $student_id) {
    $num_courses = rand(3, 5);
    $shuffled_courses = $course_ids;
    shuffle($shuffled_courses);
    $selected_courses = array_slice($shuffled_courses, 0, $num_courses);
    
    foreach ($selected_courses as $course_id) {
        $stmt = $conn->prepare("INSERT INTO enrollment (student_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
    }
}

// Assign Grades
$grades = ['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'];
$grade_weights = [4 => 'A', 3 => 'B+', 3 => 'B', 2 => 'C+', 2 => 'C', 1 => 'D+', 1 => 'D', 1 => 'F'];

foreach ($students as $student_id) {
    $result = $conn->query("SELECT course_id FROM enrollment WHERE student_id = $student_id");
    while ($row = $result->fetch_assoc()) {
        $course_id = $row['course_id'];
        $grade = array_rand($grade_weights);
        $grade_letter = $grade_weights[$grade];
        
        $stmt = $conn->prepare("INSERT INTO grades (student_id, course_id, grade) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $student_id, $course_id, $grade_letter);
        $stmt->execute();
    }
}

echo "Database seeded successfully with realistic data!";
?>