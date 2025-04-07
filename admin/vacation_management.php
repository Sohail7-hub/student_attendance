<?php
include '../config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new vacation
    if (isset($_POST['add_vacation'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
        $admin_id = $_SESSION['user_id'];

        // Validate dates
        if (strtotime($end_date) < strtotime($start_date)) {
            $error = "End date cannot be before start date";
        } else {
            $query = "INSERT INTO vacations (title, description, start_date, end_date, created_by) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $start_date, $end_date, $admin_id);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Vacation period added successfully";
            } else {
                $error = "Error adding vacation period: " . mysqli_error($conn);
            }
        }
    }

    // Delete vacation
    if (isset($_POST['delete_vacation'])) {
        $vacation_id = mysqli_real_escape_string($conn, $_POST['vacation_id']);

        $query = "DELETE FROM vacations WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $vacation_id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Vacation period deleted successfully";
        } else {
            $error = "Error deleting vacation period: " . mysqli_error($conn);
        }
    }

    // Update vacation
    if (isset($_POST['update_vacation'])) {
        $vacation_id = mysqli_real_escape_string($conn, $_POST['vacation_id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

        // Validate dates
        if (strtotime($end_date) < strtotime($start_date)) {
            $error = "End date cannot be before start date";
        } else {
            $query = "UPDATE vacations SET title = ?, description = ?, start_date = ?, end_date = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $start_date, $end_date, $vacation_id);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Vacation period updated successfully";
            } else {
                $error = "Error updating vacation period: " . mysqli_error($conn);
            }
        }
    }
}

// Get all vacation periods
$query = "SELECT v.*, u.username FROM vacations v 
          LEFT JOIN users u ON v.created_by = u.id 
          ORDER BY v.start_date DESC";
$result = mysqli_query($conn, $query);
$vacations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $vacations[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacation Management</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .vacation-card {
            border-left: 4px solid #007bff;
        }

        .vacation-dates {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .vacation-description {
            margin-top: 10px;
            color: #495057;
        }

        .calendar-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }

        .calendar-day {
            height: 100px;
            border: 1px solid #dee2e6;
            padding: 5px;
            position: relative;
        }

        .calendar-day.vacation {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .calendar-day.weekend {
            background-color: rgba(108, 117, 125, 0.1);
        }

        .day-number {
            font-weight: bold;
            position: absolute;
            top: 5px;
            right: 5px;
        }

        .vacation-indicator {
            background-color: #007bff;
            color: white;
            border-radius: 3px;
            padding: 2px 5px;
            font-size: 0.7rem;
            margin-top: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <h2><i class="fas fa-calendar-alt"></i> Vacation Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="admin_attendance.php">Attendance Management</a></li>
                        <li class="breadcrumb-item active">Vacation Management</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Vacation Management Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add Vacation Period</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="vacationForm">
                            <input type="hidden" name="vacation_id" id="vacation_id">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="text" class="form-control date-picker" id="start_date" name="start_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="text" class="form-control date-picker" id="end_date" name="end_date" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="submitBtn" name="add_vacation">
                                    <i class="fas fa-save"></i> Save Vacation
                                </button>
                                <button type="button" class="btn btn-secondary" id="resetBtn">
                                    <i class="fas fa-redo"></i> Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Vacation List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Vacation Periods</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($vacations)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No vacation periods have been added yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vacations as $vacation): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($vacation['title']); ?></td>
                                                <td><?php echo htmlspecialchars($vacation['description']); ?></td>
                                                <td><?php echo htmlspecialchars($vacation['start_date']); ?></td>
                                                <td><?php echo htmlspecialchars($vacation['end_date']); ?></td>
                                                <td><?php echo htmlspecialchars($vacation['username']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info edit-vacation"
                                                        data-id="<?php echo $vacation['id']; ?>"
                                                        data-title="<?php echo htmlspecialchars($vacation['title']); ?>"
                                                        data-description="<?php echo htmlspecialchars($vacation['description']); ?>"
                                                        data-start="<?php echo htmlspecialchars($vacation['start_date']); ?>"
                                                        data-end="<?php echo htmlspecialchars($vacation['end_date']); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vacation period?');">
                                                        <input type="hidden" name="vacation_id" value="<?php echo $vacation['id']; ?>">
                                                        <button type="submit" name="delete_vacation" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Calendar View -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar"></i> Vacation Calendar</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-select" id="calendarMonth">
                                        <?php
                                        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                                        $currentMonth = date('n') - 1;
                                        foreach ($months as $index => $month) {
                                            $selected = ($index == $currentMonth) ? 'selected' : '';
                                            echo "<option value=\"$index\" $selected>$month</option>";
                                        }
                                        ?>
                                    </select>
                                    <select class="form-select" id="calendarYear">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear - 2; $year <= $currentYear + 2; $year++) {
                                            $selected = ($year == $currentYear) ? 'selected' : '';
                                            echo "<option value=\"$year\" $selected>$year</option>";
                                        }
                                        ?>
                                    </select>
                                    <button class="btn btn-primary" id="showCalendarBtn">
                                        <i class="fas fa-search"></i> Show
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="calendarContainer" class="calendar-container">
                            <!-- Calendar will be generated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            // Initialize date pickers
            $(".date-picker").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // Handle edit vacation button
            $(".edit-vacation").click(function() {
                const id = $(this).data("id");
                const title = $(this).data("title");
                const description = $(this).data("description");
                const startDate = $(this).data("start");
                const endDate = $(this).data("end");

                // Fill the form with vacation data
                $("#vacation_id").val(id);
                $("#title").val(title);
                $("#description").val(description);
                $("#start_date").val(startDate);
                $("#end_date").val(endDate);

                // Change button text and name
                $("#submitBtn").html('<i class="fas fa-save"></i> Update Vacation');
                $("#submitBtn").attr("name", "update_vacation");

                // Scroll to form
                $('html, body').animate({
                    scrollTop: $("#vacationForm").offset().top - 100
                }, 500);
            });

            // Handle reset button
            $("#resetBtn").click(function() {
                // Clear form fields
                $("#vacation_id").val("");
                $("#vacationForm")[0].reset();

                // Reset button text and name
                $("#submitBtn").html('<i class="fas fa-save"></i> Save Vacation');
                $("#submitBtn").attr("name", "add_vacation");
            });

            // Generate calendar on page load
            generateCalendar();

            // Handle show calendar button
            $("#showCalendarBtn").click(function() {
                generateCalendar();
            });

            // Function to generate calendar
            function generateCalendar() {
                const month = parseInt($("#calendarMonth").val());
                const year = parseInt($("#calendarYear").val());

                // Get vacation data
                const vacations = <?php echo json_encode($vacations); ?>;

                // Create calendar
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.

                // Create calendar HTML
                let calendarHTML = '<div class="row mb-2">';
                const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

                // Add day headers
                for (let i = 0; i < 7; i++) {
                    calendarHTML += `<div class="col text-center fw-bold">${dayNames[i]}</div>`;
                }
                calendarHTML += '</div>';

                // Start calendar grid
                calendarHTML += '<div class="row">';

                // Add empty cells for days before the first day of the month
                for (let i = 0; i < startingDay; i++) {
                    calendarHTML += '<div class="col calendar-day"></div>';
                }

                // Add days of the month
                for (let day = 1; day <= daysInMonth; day++) {
                    const currentDate = new Date(year, month, day);
                    const dateString = currentDate.toISOString().split('T')[0]; // YYYY-MM-DD format
                    const isWeekend = currentDate.getDay() === 0 || currentDate.getDay() === 6; // Sunday or Saturday

                    // Check if this day is a vacation day
                    let isVacation = false;
                    let vacationTitle = '';

                    vacations.forEach(function(vacation) {
                        const startDate = new Date(vacation.start_date);
                        const endDate = new Date(vacation.end_date);

                        if (currentDate >= startDate && currentDate <= endDate) {
                            isVacation = true;
                            vacationTitle = vacation.title;
                        }
                    });

                    // Add appropriate classes
                    let dayClass = 'calendar-day';
                    if (isVacation) dayClass += ' vacation';
                    if (isWeekend) dayClass += ' weekend';

                    calendarHTML += `<div class="col ${dayClass}">`;
                    calendarHTML += `<div class="day-number">${day}</div>`;

                    // Add vacation indicator if applicable
                    if (isVacation) {
                        calendarHTML += `<div class="vacation-indicator">${vacationTitle}</div>`;
                    }

                    calendarHTML += '</div>';

                    // Start a new row after Saturday (day 6)
                    if ((startingDay + day) % 7 === 0) {
                        calendarHTML += '</div><div class="row">';
                    }
                }

                // Add empty cells for days after the last day of the month
                const remainingCells = 7 - ((startingDay + daysInMonth) % 7);
                if (remainingCells < 7) {
                    for (let i = 0; i < remainingCells; i++) {
                        calendarHTML += '<div class="col calendar-day"></div>';
                    }
                }

                calendarHTML += '</div>';

                // Update calendar container
                $("#calendarContainer").html(calendarHTML);
            }
        });
    </script>
</body>

</html>