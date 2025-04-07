<?php
include '..\config.php';
if ($_SESSION['role'] != 'admin') {
    header("Location: ..\login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $class_type = $_POST['class_type'];
    $class_year = $_POST['class_year'];

    // Get the latest student_id for this department and year
    $result = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(student_id, '-', -1), '-', 1) AS UNSIGNED)) as max_id FROM students WHERE class_type='$class_type' AND class_year='$class_year'");
    $row = mysqli_fetch_assoc($result);
    $next_id = ($row['max_id'] === null) ? 1 : $row['max_id'] + 1;

    // Generate the new student_id (e.g., CIV-1-01 for first Civil first year student)
    $dept_code = strtoupper(substr($class_type, 0, 3));
    $student_id = sprintf("%s-%s-%02d", $dept_code, $class_year, $next_id);

    // Server-side password validation
    if (strlen($password) < 6) {
        header("Location: admin_dashboard.php?error=Password must be at least 6 characters long");
        exit();
    }

    $password = md5($password); // Use password_hash() in production

    $sql = "INSERT INTO users (username, password, role, email) VALUES ('$username', '$password', 'student', '$email')";
    if (mysqli_query($conn, $sql)) {
        $user_id = mysqli_insert_id($conn);
        $sql = "INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id) VALUES ('$student_id', '$name', '$age', '$email', '$class_type', '$class_year', '$user_id')";
        if (mysqli_query($conn, $sql)) {
            header("Location: admin_dashboard.php?success=Student added");
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
