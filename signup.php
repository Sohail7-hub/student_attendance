<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(to right, #1a2f4b, #2c5282);
            font-family: 'Arial', sans-serif;
        }

        .mx-auto {
            background-color: white;
            border: 2px solid black;
        }

        h1 {
            font-family: 'Roboto', sans-serif;
            color: #fff;
            font-size: 36px;
            font-weight: bold;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .container {
            margin-top: 100px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #f5f5f0, #e8e8e0);
            transition: transform 0.3s ease;
        }

        .card-body {
            padding: 40px;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0;
        }

        .navbar-brand {
            font-weight: bold;
            color: white;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            padding: 0;
        }

        .navbar-brand i {
            font-size: 1.8rem;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            border-bottom: 2px solid white;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a5568, #2d3748);
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 94, 255, 0.2);
            border-radius: 10px;
            padding: 12px;
            position: relative;
            overflow: hidden;
            animation: pulse 2s infinite;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #2d3748, #1a202c);
            box-shadow: 0 0 25px rgba(0, 94, 255, 0.6);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 15px rgba(0, 94, 255, 0.2);
            }

            50% {
                box-shadow: 0 0 20px rgba(0, 94, 255, 0.4);
            }

            100% {
                box-shadow: 0 0 15px rgba(0, 94, 255, 0.2);
            }
        }

        .alert {
            border-radius: 10px;
        }

        .input-group .btn-outline-secondary {
            border-radius: 10px;
        }

        small.form-text {
            font-size: 12px;
        }

        .text-muted {
            font-size: 14px;
        }

        .text-center p {
            font-size: 14px;
        }

        .text-center a {
            color: #fff;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <div class="d-flex align-items-center justify-content-start flex-grow-1">
                <a class="navbar-brand" href="index.php"><i class="bi bi-mortarboard-fill"></i> Student Attendance</a>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="signup.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Student Sign Up</h1>
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <form action="signup.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age" required>
                    </div>
                    <div class="mb-3">
                        <label for="class_type" class="form-label">Class Type</label>
                        <select class="form-control" id="class_type" name="class_type" required>
                            <option value="">Select Class Type</option>
                            <option value="CIT">CIT</option>
                            <option value="ELC">ELC</option>
                            <option value="CIVIL">CIVIL</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="class_year" class="form-label">Class Year</label>
                        <select class="form-control" id="class_year" name="class_year" required>
                            <option value="">Select Class Year</option>
                            <option value="1st">1st Year</option>
                            <option value="2nd">2nd Year</option>
                            <option value="3rd">3rd Year</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                </form>

                <?php

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $name = $_POST['name'];
                    $age = $_POST['age'];
                    $class_type = $_POST['class_type'];
                    $class_year = $_POST['class_year'];
                    $email = $_POST['email'];
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    if (strlen($password) < 6) {
                        echo '<div class="alert alert-danger mt-3">Password must be at least 6 characters long!</div>';
                    } else {
                        $password = md5($password);

                        $check_username = "SELECT * FROM users WHERE username='$username'";
                        $result_username = mysqli_query($conn, $check_username);

                        if (mysqli_num_rows($result_username) > 0) {
                            echo '<div class="alert alert-danger mt-3">Username already taken!</div>';
                        } else {
                            $sql = "INSERT INTO users (username, password, role, email) VALUES ('$username', '$password', 'student', '$email')";
                            if (mysqli_query($conn, $sql)) {
                                $user_id = mysqli_insert_id($conn);

                                // Get the latest student_id for this department and year
                                $result = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING_INDEX(student_id, '-', -1) AS UNSIGNED)) as max_id FROM students WHERE class_type='$class_type' AND class_year='$class_year'");
                                $row = mysqli_fetch_assoc($result);
                                $next_id = ($row['max_id'] === null) ? 1 : $row['max_id'] + 1;

                                // Generate the new student_id (e.g., CIT-1-01 for first CIT first year student)
                                $dept_code = strtoupper(substr($class_type, 0, 3));
                                $year_num = substr($class_year, 0, 1);
                                $student_id = sprintf("%s-%s-%02d", $dept_code, $year_num, $next_id);

                                $sql = "INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id) 
                                        VALUES ('$student_id', '$name', '$age', '$email', '$class_type', '$class_year', '$user_id')";
                                if (mysqli_query($conn, $sql)) {
                                    echo '<div class="alert alert-success mt-3">Sign up successful! <a href="login.php">Login here</a></div>';
                                } else {
                                    echo '<div class="alert alert-danger mt-3">Error registering student: ' . mysqli_error($conn) . '</div>';
                                    mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");
                                }
                            } else {
                                echo '<div class="alert alert-danger mt-3">Error creating user: ' . mysqli_error($conn) . '</div>';
                            }
                        }
                    }
                    mysqli_close($conn);
                }
                ?>
            </div>
        </div>
        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    <!-- Footer Section -->
    <footer style="background: linear-gradient(to right, #1a2f4b, #2c5282); padding: 20px 0 10px; margin-top: 30px; backdrop-filter: blur(10px); border-top: 1px solid rgba(255, 255, 255, 0.2);">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <h5 class="text-light mb-2">Quick Links</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="index.php" class="text-light text-decoration-none"><i class="bi bi-house-door me-2"></i>Home</a></li>
                        <li><a href="html/about.html" class="text-light text-decoration-none"><i class="bi bi-info-circle me-2"></i>About Us</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none"><i class="bi bi-envelope me-2"></i>Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-2">
                    <h5 class="text-light mb-2">Connect With Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light fs-4"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light fs-4"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="text-light fs-4"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <h5 class="text-light mb-2">Contact Info</h5>
                    <p class="text-light mb-1"><i class="bi bi-geo-alt me-2"></i>Earth near Moon</p>
                    <p class="text-light mb-1"><i class="bi bi-telephone me-2"></i>xxxxxxxxxxx</p>
                    <p class="text-light mb-0"><i class="bi bi-envelope me-2"></i>studentmanagement@gmail.com</p>
                </div>
            </div>
            <hr class="my-3 bg-light">
            <div class="text-center text-light">
                <p class="mb-0">&copy; 2025 Student Attendance System. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>

</html>