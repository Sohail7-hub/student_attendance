<?php
include '..\config.php';
if ($_SESSION['role'] != 'admin') {
    header("Location: ..\login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_POST['teacher_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $class_type = $_POST['class_type'];
    $class_year = $_POST['class_year'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Server-side password validation
    if (strlen($password) < 6) {
        header("Location: admin_dashboard.php?error=Password must be at least 6 characters long");
        exit();
    }

    $password = md5($password); // Use password_hash() in production

    $sql = "INSERT INTO users (username, password, role, email) VALUES ('$username', '$password', 'teacher', '$email')";
    if (mysqli_query($conn, $sql)) {
        $user_id = mysqli_insert_id($conn);
        $sql = "INSERT INTO teachers (teacher_id, name, email, class_type, class_year, user_id) VALUES ('$teacher_id', '$name', '$email', '$class_type', '$class_year', '$user_id')";
        if (mysqli_query($conn, $sql)) {
            header("Location: admin_dashboard.php?success=Teacher added");
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
