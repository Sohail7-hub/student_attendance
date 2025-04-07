<?php
include '..\config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ..\login.php");
    exit();
}

if (isset($_GET['student_id'])) {
    $student_id = mysqli_real_escape_string($conn, $_GET['student_id']);

    // Get the user_id associated with the student
    $result = mysqli_query($conn, "SELECT user_id FROM students WHERE student_id='$student_id'");
    if (!$result || mysqli_num_rows($result) == 0) {
        header("Location: admin_dashboard.php?error=Student not found");
        exit();
    }
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['user_id'];

    // Start transaction to ensure data consistency
    mysqli_begin_transaction($conn);

    // Delete related records (attendance, grades) first due to foreign key constraints
    $attendance_delete = mysqli_query($conn, "DELETE FROM attendance WHERE student_id='$student_id'");
    $grades_delete = mysqli_query($conn, "DELETE FROM grades WHERE student_id='$student_id'");

    // Delete from students table
    $student_delete = mysqli_query($conn, "DELETE FROM students WHERE student_id='$student_id'");
    if ($student_delete) {
        // Delete from users table
        $user_delete = mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");
        if ($user_delete) {
            mysqli_commit($conn);
            header("Location: admin_dashboard.php?success=Student deleted successfully");
            exit();
        } else {
            mysqli_rollback($conn);
            header("Location: admin_dashboard.php?error=Error deleting user: " . mysqli_error($conn));
            exit();
        }
    } else {
        mysqli_rollback($conn);
        header("Location: admin_dashboard.php?error=Error deleting student: " . mysqli_error($conn));
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
}

mysqli_close($conn);
