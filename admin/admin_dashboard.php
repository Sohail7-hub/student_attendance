<?php
include '..\config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ..\login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: white;
            font-family: 'Arial', sans-serif;
        }

        .mt-5 {
            background-color: white;
            border: 2px solid #dee2e6;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
            max-width: 1200px;
            margin-top: 30px;
            border: 2px solid #2c3e50;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
        }

        .table {
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table th,
        .table td {
            text-align: center;
            color: black;
            border: 1px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        h1 {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .btn-danger {
            border-radius: 10px;
            background: linear-gradient(135deg, #800020, #000080);
            border-color: #2c3e50;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #000080, #800020);
        }

        .card {
            background: linear-gradient(135deg, #f5f5f0, #e8e8e0);
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid black;
        }

        .card-body {
            padding: 30px;
        }

        .form-label {
            font-weight: bold;
        }

        .alert {
            margin-top: 20px;
        }

        .text-center {
            font-size: 19px;
            color: black;

        }

        .text-center a {
            color: #007bff;

        }

        .text-center a:hover {
            text-decoration: none;
        }

        .table th,
        .table td {
            text-align: center;
            color: black;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        .form-text {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
    <script>
        let selectedTeacherClass = 'all';
        let selectedTeacherYear = '';

        function filterTeachers(classType) {
            selectedTeacherClass = classType;
            const teacherRows = document.querySelectorAll('.teacher-row');
            const yearFilters = document.getElementById('teacherYearFilters');

            teacherRows.forEach(row => {
                const teacherClass = row.querySelector('td:nth-child(4)').textContent;
                row.style.display = (classType === 'all' || teacherClass === classType) &&
                    (!selectedTeacherYear || row.querySelector('td:nth-child(5)').textContent === selectedTeacherYear) ?
                    '' : 'none';
            });

            yearFilters.style.display = classType !== 'all' ? 'block' : 'none';
            selectedTeacherYear = '';

            // Update active button state
            const buttons = document.querySelectorAll('.btn-primary, .btn-info, .btn-success, .btn-warning');
            buttons.forEach(btn => btn.style.opacity = '0.7');
            event.target.style.opacity = '1';

            // Reset year filter buttons
            const yearButtons = document.querySelectorAll('#teacherYearFilters .btn');
            yearButtons.forEach(btn => btn.style.opacity = '0.7');
        }

        function filterTeachersByYear(year) {
            selectedTeacherYear = year;
            const teacherRows = document.querySelectorAll('.teacher-row');

            teacherRows.forEach(row => {
                const teacherClass = row.querySelector('td:nth-child(4)').textContent;
                const teacherYear = row.querySelector('td:nth-child(5)').textContent;
                row.style.display = (selectedTeacherClass === 'all' || teacherClass === selectedTeacherClass) &&
                    teacherYear === year ? '' : 'none';
            });

            // Update active year button state
            const yearButtons = document.querySelectorAll('#teacherYearFilters .btn');
            yearButtons.forEach(btn => btn.style.opacity = '0.7');
            event.target.style.opacity = '1';
        }
    </script>
</head>

<body>
    <div class="container mt-5 maincontainer">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Admin Dashboard</h1>
            <div>
                <a href="admin_attendance.php" class="btn btn-primary me-2">More</a>
                <a href="..\logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php } ?>
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php } ?>

        <div class="row mt-4">

            <!-- Add Teacher Form -->
            <div class="col-md-8 mx-auto">
                <h3 class="text-center">Add Teacher</h3>
                <div class="card">
                    <div class="card-body">
                        <form action="add_teacher.php" method="POST" id="teacherForm">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="teacher_id" placeholder="Teacher ID" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="name" placeholder="Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-control" name="class_type" required>
                                    <option value="">Select Class Type</option>
                                    <option value="CIT">CIT</option>
                                    <option value="ELC">ELC</option>
                                    <option value="CIVIL">CIVIL</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-control" name="class_year" required>
                                    <option value="">Select Year</option>
                                    <option value="1st">1st Year</option>
                                    <option value="2nd">2nd Year</option>
                                    <option value="3rd">3rd Year</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" id="teacherPassword" placeholder="Password" required>
                                <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Teacher</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Filter Buttons -->
        <div class="mt-5">
            <h3 class="text-center">Students by Class</h3>
            <div class="text-center mb-3">
                <button class="btn btn-info me-2" onclick="filterStudents('CIT')">CIT</button>
                <button class="btn btn-success me-2" onclick="filterStudents('ELC')">ELC</button>
                <button class="btn btn-warning" onclick="filterStudents('CIVIL')">CIVIL</button>
            </div>
            <div id="yearFilters" class="text-center mb-3" style="display: none;">
                <button class="btn btn-secondary me-2" onclick="filterByYear('1st')">1st Year</button>
                <button class="btn btn-secondary me-2" onclick="filterByYear('2nd')">2nd Year</button>
                <button class="btn btn-secondary" onclick="filterByYear('3rd')">3rd Year</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div id="noFilterMessage" class="text-center mb-3">Please select a class to view students</div>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-responsive" style="display: none;">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Email</th>
                                        <th>Class Type</th>
                                        <th>Year</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="studentTableBody">
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM students ORDER BY class_type, class_year");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr class='student-row' data-class='" . $row['class_type'] . "' data-year='" . $row['class_year'] . "'>";
                                        echo "<td>" . $row['student_id'] . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td>" . $row['age'] . "</td>";
                                        echo "<td>" . $row['email'] . "</td>";
                                        echo "<td>" . $row['class_type'] . "</td>";
                                        echo "<td>" . $row['class_year'] . "</td>";
                                        echo "<td><a href='delete_student.php?student_id=" . $row['student_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers List -->
            <div class="mt-5">
                <h3 class="text-center">Teachers</h3>
                <div class="text-center mb-3 teachers-filter">
                    <button class="btn btn-primary me-2" onclick="filterTeachers('all')">All Classes</button>
                    <button class="btn btn-info me-2" onclick="filterTeachers('CIT')">CIT</button>
                    <button class="btn btn-success me-2" onclick="filterTeachers('ELC')">ELC</button>
                    <button class="btn btn-warning" onclick="filterTeachers('CIVIL')">CIVIL</button>
                </div>
                <!-- Year Filter Buttons (Initially Hidden) -->
                <div id="teacherYearFilters" class="text-center mb-3" style="display: none;">
                    <button class="btn btn-secondary me-2" onclick="filterTeachersByYear('1st')">1st Year</button>
                    <button class="btn btn-secondary me-2" onclick="filterTeachersByYear('2nd')">2nd Year</button>
                    <button class="btn btn-secondary" onclick="filterTeachersByYear('3rd')">3rd Year</button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th>Teacher ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Class</th>
                                        <th>Year</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM teachers ORDER BY class_type, class_year");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr class='teacher-row' data-class='" . $row['class_type'] . "' data-year='" . $row['class_year'] . "'>";
                                        echo "<td>" . htmlspecialchars($row['teacher_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['class_type']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['class_year']) . "</td>";
                                        echo "<td><a href='delete_teacher.php?teacher_id=" . $row['teacher_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>

            <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
            <script>
                // Password validation for student form
                document.getElementById('studentForm').addEventListener('submit', function(e) {
                    const password = document.getElementById('studentPassword').value;
                    if (password.length < 6) {
                        e.preventDefault();
                        alert('Student password must be at least 6 characters long!');
                    }
                });

                // Password validation for teacher form
                document.getElementById('teacherForm').addEventListener('submit', function(e) {
                    const password = document.getElementById('teacherPassword').value;
                    if (password.length < 6) {
                        e.preventDefault();
                        alert('Teacher password must be at least 6 characters long!');
                    }
                });

                let currentClass = 'all';
                let currentYear = 'all';
                let currentTeacherClass = 'all';
                let currentTeacherYear = 'all';

                // Function to filter students by class
                function filterStudents(classType) {
                    currentClass = classType;
                    const yearFilters = document.getElementById('yearFilters');
                    yearFilters.style.display = classType === 'all' ? 'none' : 'block';

                    updateStudentDisplay();

                    // Update active button state
                    const buttons = document.querySelectorAll('.btn-primary, .btn-info, .btn-success, .btn-warning');
                    buttons.forEach(btn => btn.style.opacity = '0.7');
                    event.target.style.opacity = '1';
                }

                // Function to filter by year
                function filterByYear(year) {
                    currentYear = year;
                    updateStudentDisplay();

                    // Update active year button state
                    const yearButtons = document.querySelectorAll('#yearFilters .btn');
                    yearButtons.forEach(btn => btn.style.opacity = '0.7');
                    event.target.style.opacity = '1';
                }

                // Function to update student display based on current filters
                function updateStudentDisplay() {
                    const studentRows = document.querySelectorAll('.student-row');
                    studentRows.forEach(row => {
                        const studentClass = row.getAttribute('data-class');
                        const studentYear = row.getAttribute('data-year');
                        row.style.display = (currentClass === 'all' || studentClass === currentClass) &&
                            (currentYear === 'all' || studentYear === currentYear) ? '' : 'none';
                    });
                }

                // Function to filter teachers by class
                function filterTeachers(classType) {
                    currentTeacherClass = classType;
                    const teacherYearFilters = document.getElementById('teacherYearFilters');
                    teacherYearFilters.style.display = classType === 'all' ? 'none' : 'block';

                    updateTeacherDisplay();

                    // Update active button state
                    const buttons = document.querySelectorAll('.teachers-filter .btn');
                    buttons.forEach(btn => btn.style.opacity = '0.7');
                    event.target.style.opacity = '1';
                }

                // Function to filter teachers by year
                function filterTeachersByYear(year) {
                    currentTeacherYear = year;
                    updateTeacherDisplay();

                    // Update active year button state
                    const yearButtons = document.querySelectorAll('#teacherYearFilters .btn');
                    yearButtons.forEach(btn => btn.style.opacity = '0.7');
                    event.target.style.opacity = '1';
                }

                // Function to update teacher display based on current filters
                function updateTeacherDisplay() {
                    const teacherRows = document.querySelectorAll('.teacher-row');
                    teacherRows.forEach(row => {
                        const teacherClass = row.getAttribute('data-class');
                        const teacherYear = row.getAttribute('data-year');
                        row.style.display = (currentTeacherClass === 'all' || teacherClass === currentTeacherClass) &&
                            (currentTeacherYear === 'all' || teacherYear === currentTeacherYear) ? '' : 'none';
                    });
                }
            </script>
</body>

</html>
<script>
    let selectedClass = '';
    let selectedYear = '';

    function filterStudents(classType) {
        selectedClass = classType;
        const studentRows = document.querySelectorAll('.student-row');
        const yearFilters = document.getElementById('yearFilters');
        const table = document.querySelector('.table');
        const noFilterMessage = document.getElementById('noFilterMessage');

        // Show year filters and hide message when a class is selected
        yearFilters.style.display = 'block';
        noFilterMessage.style.display = 'none';
        table.style.display = 'table';

        studentRows.forEach(row => {
            const studentClass = row.getAttribute('data-class');
            row.style.display = (studentClass === classType &&
                (!selectedYear || row.getAttribute('data-year') === selectedYear)) ? '' : 'none';
        });

        // Update active button state
        const buttons = document.querySelectorAll('.btn-info, .btn-success, .btn-warning');
        buttons.forEach(btn => btn.style.opacity = '0.7');
        event.target.style.opacity = '1';

        // Reset year filter buttons
        const yearButtons = document.querySelectorAll('#yearFilters .btn');
        yearButtons.forEach(btn => btn.style.opacity = '0.7');
        selectedYear = '';
    }

    function filterByYear(year) {
        selectedYear = year;
        const studentRows = document.querySelectorAll('.student-row');

        studentRows.forEach(row => {
            const studentClass = row.getAttribute('data-class');
            const studentYear = row.getAttribute('data-year');
            row.style.display = (studentClass === selectedClass && studentYear === year) ? '' : 'none';
        });

        // Update active year button state
        const yearButtons = document.querySelectorAll('#yearFilters .btn');
        yearButtons.forEach(btn => btn.style.opacity = '0.7');
        event.target.style.opacity = '1';
    }
</script>