<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance System</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(to right, #1a2f4b, #2c5282);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            box-shadow: 0 4px 15px #d4cbcb;
            transition: all 0.3s ease;
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
            color: whtie !important;
            transform: translateY(-2px);
        }

        .hero-section {
            padding: 100px 0;
            text-align: center;
            color: white;
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-text {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-custom {
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            border-radius: 30px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .features-section {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 3rem;
            margin-top: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .features-section:hover {
            transform: translateY(-5px);
        }

        .feature-item {
            text-align: center;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
        }

        .feature-item i {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .feature-item:hover i {
            transform: scale(1.1);
            color: #0056b3;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="bi bi-mortarboard-fill"></i> Student Attendance</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-fill"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="html/about.html"><i class="bi bi-info-circle-fill"></i> About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php"><i class="bi bi-envelope-fill"></i> Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php"><i class="bi bi-person-plus-fill"></i> Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="hero-section">
            <h1 class="hero-title">Welcome to Student Attendance System</h1>
            <p class="hero-text">A comprehensive platform for managing student information, attendance, and academic progress.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="login.php" class="btn btn-primary btn-custom"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                <a href="signup.php" class="btn btn-outline-light btn-custom"><i class="bi bi-person-plus-fill"></i> Sign Up</a>
            </div>
        </div>

        <div class="features-section">
            <div class="row">
                <div class="col-md-4 feature-item">
                    <i class="bi bi-person-check"></i>
                    <h3>Easy Attendance</h3>
                    <p>Track and manage student attendance efficiently with our user-friendly system.</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="bi bi-graph-up"></i>
                    <h3>Performance Tracking</h3>
                    <p>Monitor academic progress and generate detailed performance reports.</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="bi bi-shield-check"></i>
                    <h3>Secure Access</h3>
                    <p>Role-based access control ensuring data security and privacy.</p>
                </div>
            </div>
        </div>

        <div class="features-section mt-4">
            <h2 class="text-center mb-4">Why Choose Our Student Attendance Management System?</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-clipboard-data text-primary" style="font-size: 12rem; display: block; text-align: center;"></i>
                </div>
                <div class="col-md-6">
                    <h3 class="mb-3">Streamline Attendance Tracking</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Save valuable teaching time with automated attendance recording</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Eliminate manual paperwork and reduce human errors</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Generate instant attendance reports for better decision-making</li>
                    </ul>
                </div>
            </div>

            <div class="row align-items-center mt-5">
                <div class="col-md-6 order-md-2">
                    <i class="bi bi-bell text-primary" style="font-size: 12rem; display: block; text-align: center;"></i>
                </div>
                <div class="col-md-6 order-md-1">
                    <h3 class="mb-3">Enhanced Communication</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Automatic notifications to parents about student attendance</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Real-time updates on student attendance patterns</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Improved parent-teacher communication</li>
                    </ul>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-4 text-center">
                    <i class="bi bi-clock-history text-primary" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3">Time-Saving</h4>
                    <p>Reduce administrative workload by up to 60% with automated attendance tracking</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-graph-up-arrow text-primary" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3">Improved Accuracy</h4>
                    <p>Achieve 99.9% accuracy in attendance records with digital tracking</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-file-earmark-text text-primary" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3">Easy Reporting</h4>
                    <p>Generate comprehensive attendance reports with just a few clicks</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4" style="background-color: rgba(255, 255, 255, 0.1);">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="text-white mb-3">About Us</h5>
                    <p class="text-light">Student Attendance System provides comprehensive solutions for educational institutions to track and manage student attendance efficiently and effectively.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-white mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="html/about.html" class="text-light text-decoration-none">About</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contact Us</a></li>
                        <li><a href="login.php" class="text-light text-decoration-none">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-white mb-3">Connect With Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light fs-4"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light fs-4"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="text-light fs-4"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-light">
            <div class="text-center text-light">
                <p class="mb-0">&copy; 2025 Student Attendance System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>