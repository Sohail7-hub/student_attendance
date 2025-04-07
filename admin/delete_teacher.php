<?php
include '..\config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ..\login.php");
    exit();
}

if (isset($_GET['teacher_id'])) {
    $teacher_id = $_GET['teacher_id'];

    // Get the user_id associated with the teacher
    $result = mysqli_query($conn, "SELECT user_id FROM teachers WHERE teacher_id='$teacher_id'");
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['user_id'];

    // Delete related records (attendance, grades) where the teacher is referenced
    mysqli_query($conn, "DELETE FROM attendance WHERE teacher_id='$teacher_id'");
    mysqli_query($conn, "DELETE FROM grades WHERE teacher_id='$teacher_id'");

    // Delete from teachers table
    $sql = "DELETE FROM teachers WHERE teacher_id='$teacher_id'";
    if (mysqli_query($conn, $sql)) {
        // Delete from users table
        $sql = "DELETE FROM users WHERE id='$user_id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: admin_dashboard.php?success=Teacher deleted successfully");
        } else {
            echo "Error deleting user: " . mysqli_error($conn);
        }
    } else {
        echo "Error deleting teacher: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_dashboard.php");
}

mysqli_close($conn);
