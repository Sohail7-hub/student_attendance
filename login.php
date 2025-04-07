<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Server-side password length validation
    if (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long!';
    } else {
        // First check if username exists
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $hashed_password = md5($password);
            // Compare MD5 hashed passwords
            if ($hashed_password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] == 'admin') {
                    header("Location: admin/admin_dashboard.php");
                    exit();
                } elseif ($row['role'] == 'teacher') {
                    header("Location: teacher/teacher_dashboard.php");
                    exit();
                } elseif ($row['role'] == 'student') {
                    header("Location: student/student_dashboard.php");
                    exit();
                }
            } else {
                $error_message = 'Incorrect password!';
            }
        } else {
            $error_message = 'Username not found!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(to right, #1a2f4b, #2c5282);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(255, 254, 254, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            min-height: 70px;
        }

        .navbar-brand {
            font-weight: bold;
            color: white;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .container {
            flex: 1;
            margin-top: 2rem;
            padding-bottom: 2rem;
        }

        h1 {
            color: white;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .card {
            border-radius: 15px;
            border: 2px solid #2c3e50;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, rgb(255, 255, 255), #e8e8e0);
        }

        .btn-primary {
            background-color: #4a5568;
            border-color: rgb(0, 94, 255);
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 94, 255, 0.1);
            border-radius: 10px;
            padding: 12px;
        }

        .btn-primary:hover {
            background-color: #2d3748;
            border-color: #1a202c;
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0, 94, 255, 0.4);
        }

        footer {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            padding: 20px 0 10px;
            margin-top: 30px;
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
                        <a class="nav-link active" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Login</h1>
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-center mt-3">
            <p>Don't have an account? <a href="signup.php">Sign up as a student</a></p>
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
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
            }
        });
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        });
    </script>
</body>

</html>
<style>
    body {
        background: linear-gradient(to right, #1a2f4b, #2c5282);
        font-family: 'Arial', sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
    }

    .container {
        flex: 1;
        margin-top: 100px;
    }

    h1 {
        color: white;
        font-weight: 600;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .card {
        border-radius: 15px;
        border: 2px solid #2c3e50;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, #f5f5f0, #e8e8e0);
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

    footer {
        background: linear-gradient(135deg, #800020, #000080);
        padding: 20px 0 10px;
        margin-top: 30px;
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .card-body {
        padding: 40px;
    }

    .form-label {
        font-weight: bold;
    }

    .form-control {
        border-radius: 10px;
    }

    .btn-primary {
        border-radius: 10px;
        padding: 12px;
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

    .navbar {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        min-height: 70px;
    }

    .container {
        flex: 1;
        margin-top: 2rem;
        padding-bottom: 2rem;
    }

    .navbar-nav {
        display: flex;
        align-items: center;
        height: 100%;
    }

    .nav-item {
        display: flex;
        align-items: center;
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
        transform: translateY(-2px);
        opacity: 0.9;
    }

    .forgot-password-link {
        color: rgb(0, 0, 0);
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
        background-color: rgba(44, 82, 130, 0.2);
        text-shadow: 0 0 10px rgba(241, 196, 196, 0.3);
    }

    .forgot-password-link:hover {
        color: rgb(255, 255, 255);
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        background-color: rgb(0, 0, 0);
    }


    .nav-link.active {
        border-bottom: 2px solid white;
    }
</style>