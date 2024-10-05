<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function checkLogin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header("Location: ../login.php");
        exit();
    }
}

// Function to check if user has correct role
function checkRole($required_role) {
    checkLogin();
    if ($_SESSION['role'] !== $required_role) {
        header("Location: ../login.php");
        exit();
    }
}
?>