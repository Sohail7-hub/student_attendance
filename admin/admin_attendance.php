<?php
include '../config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get class types and years for dropdown filters
$class_types_query = "SELECT DISTINCT class_type FROM students ORDER BY class_type";
$class_types_result = mysqli_query($conn, $class_types_query);

$class_years_query = "SELECT DISTINCT class_year FROM students ORDER BY class_year";
$class_years_result = mysqli_query($conn, $class_years_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Attendance Management</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .attendance-table th {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 10;
        }

        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }

        .status-present {
            color: #28a745;
            font-weight: bold;
        }

        .status-absent {
            color: #dc3545;
            font-weight: bold;
        }

        .status-leave {
            color: #ffc107;
            font-weight: bold;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .badge {
            font-size: 0.9rem;
            font-weight: bold;
            min-width: 60px;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <h2><i class="fas fa-clipboard-list"></i> Attendance Records</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Attendance Records</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="admin_attendance.php">
                    <i class="fas fa-clipboard-check"></i> Attendance Records
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="vacation_management.php">
                    <i class="fas fa-calendar-alt"></i> Vacation Calendar
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Attendance Records</h5>
            </div>
            <div class="card-body filter-section">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="classTypeSelect" class="form-label">Class Type</label>
                        <select class="form-select" id="classTypeSelect" required>
                            <option value="">Select Class Type</option>
                            <?php while ($row = mysqli_fetch_assoc($class_types_result)): ?>
                                <option value="<?php echo htmlspecialchars($row['class_type']); ?>">
                                    <?php echo htmlspecialchars($row['class_type']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="classYearSelect" class="form-label">Class Year</label>
                        <select class="form-select" id="classYearSelect" required>
                            <option value="">Select Class Year</option>
                            <?php while ($row = mysqli_fetch_assoc($class_years_result)): ?>
                                <option value="<?php echo htmlspecialchars($row['class_year']); ?>">
                                    <?php echo htmlspecialchars($row['class_year']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="text" class="form-control date-picker" id="startDate" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="text" class="form-control date-picker" id="endDate" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-3">
                        <label for="studentSelect" class="form-label">Student (Optional)</label>
                        <select class="form-select" id="studentSelect" disabled>
                            <option value="">Select Student</option>
                        </select>
                    </div>
                    <div class="col-md-9 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button type="button" id="resetBtn" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-table"></i> Attendance Records</h5>
            </div>
            <div class="card-body">
                <div id="loadingSpinner" class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading attendance records...</p>
                </div>
                <div id="noRecords" class="no-records">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <p>Please select class type and year to view attendance records.</p>
                </div>
                <div class="table-responsive" id="attendanceTableContainer" style="display: none;">
                    <table class="table table-striped table-hover attendance-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th>Class</th>
                                <th>Attendance %</th>
                                <th>Absents</th>
                                <th>Date Range</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <!-- Data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Attendance Details Modal -->
    <div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="studentDetailsModalLabel">Student Attendance Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="student-info mb-3">
                        <h4 id="modalStudentName"></h4>
                        <p><strong>Student ID:</strong> <span id="modalStudentId"></span></p>
                    </div>
                    <div class="attendance-summary mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5>Overall Present</h5>
                                        <h3 id="presentCount">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h5>Overall Absent</h5>
                                        <h3 id="absentCount">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h5>Overall Leave</h5>
                                        <h3 id="leaveCount">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5>Total Days</h5>
                                        <h3 id="totalDays">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody id="recordAttendanceDetails">
                                <!-- Student attendance details will be loaded here -->
                            </tbody>
                        </table>
                        <div id="paginationControls" class="d-flex justify-content-between align-items-center mt-3" style="display: none !important;">
                            <div class="text-muted">
                                <span id="paginationInfo"></span>
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    <li class="page-item">
                                        <button class="page-link" id="prevPage" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </button>
                                    </li>
                                    <li class="page-item">
                                        <button class="page-link" id="nextPage" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Record Check Modal -->
    <div class="modal fade" id="studentRecordModal" tabindex="-1" aria-labelledby="studentRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentRecordModalLabel">Student Attendance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Student Name:</strong> <span id="recordStudentName"></span></p>
                            <p><strong>Student ID:</strong> <span id="recordStudentId"></span></p>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">Present</h5>
                                            <p class="card-text" id="totalPresent">0</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">Absent</h5>
                                            <p class="card-text" id="totalAbsent">0</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">Leave</h5>
                                            <p class="card-text" id="totalLeave">0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="recordStartDate" class="form-label">Start Date:</label>
                            <input type="date" class="form-control" id="recordStartDate">
                        </div>
                        <div class="col-md-6">
                            <label for="recordEndDate" class="form-label">End Date:</label>
                            <input type="date" class="form-control" id="recordEndDate">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="viewReport(window.currentStudentId, $('#recordStartDate').val(), $('#recordEndDate').val())">
                                Filter Records
                            </button>
                        </div>
                    </div>
                    <div id="attendanceDetails" class="table-responsive">
                        <!-- Attendance records will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentPage = 1;

        function viewReport(studentId, customStartDate = null, customEndDate = null, page = 1) {
            currentPage = page;
            // Get current month and year if custom dates are not provided
            let startDate, endDate;
            if (customStartDate && customEndDate) {
                startDate = customStartDate;
                endDate = customEndDate;
            } else {
                const today = new Date();
                const month = (today.getMonth() + 1).toString().padStart(2, '0');
                const year = today.getFullYear();
                startDate = `${year}-${month}-01`;
                endDate = `${year}-${month}-${new Date(year, month, 0).getDate()}`;
            }

            // Set the date range in the modal
            $('#recordStartDate').val(startDate);
            $('#recordEndDate').val(endDate);

            // Store the current student ID for later use
            window.currentStudentId = studentId;

            // Show loading state
            $('#attendanceDetails').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            // Remove existing total days information
            $('.modal-body .row.mb-3:first .col-md-12').remove();

            // Fetch student details and attendance records
            $.ajax({
                url: 'get_student_info.php',
                type: 'POST',
                data: {
                    student_id: studentId,
                    start_date: startDate,
                    end_date: endDate,
                    page: page
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Error: ' + response.error);
                        return;
                    }

                    // Update modal with student information
                    $('#recordStudentName').text($(`#attendanceTableBody tr[data-student-id="${studentId}"] td:first-child`).text());
                    $('#recordStudentId').text(studentId);

                    // Update attendance summary
                    $('#totalPresent').text(`${response.summary.present || 0} (${response.summary.attendance_rate}%)`);
                    $('#totalAbsent').text(response.summary.absent || 0);
                    $('#totalLeave').text(response.summary.leave || 0);
                    
                    // Add total days information
                    $('.modal-body .row.mb-3:first').append(`
                        <div class="col-md-12 mt-3">
                            <p class="text-info"><strong>Total Days:</strong> ${response.summary.total_days}</p>
                        </div>
                    `);

                    // Generate attendance details table
                    let detailsHtml = '<table class="table table-striped"><thead><tr><th>Date</th><th>Status</th><th>Recorded By</th><th>Type</th></tr></thead><tbody>';

                    if (response.records && response.records.length > 0) {
                        response.records.forEach(function(record) {
                            let statusClass = '';
                            if (record.status.toLowerCase() === 'present') statusClass = 'text-success';
                            else if (record.status.toLowerCase() === 'absent') statusClass = 'text-danger';
                            else if (record.status.toLowerCase() === 'leave') statusClass = 'text-warning';

                            detailsHtml += `
                                <tr>
                                    <td>${record.date}</td>
                                    <td class="${statusClass}">${record.status}</td>
                                    <td>${record.recorded_by}</td>
                                    <td>${record.type}</td>
                                </tr>
                            `;
                        });
                    } else {
                        detailsHtml += '<tr><td colspan="4" class="text-center">No records found</td></tr>';
                    }

                    detailsHtml += '</tbody></table>';

                    // Add pagination if available
                    if (response.pagination) {
                        detailsHtml += '<div class="d-flex justify-content-between align-items-center mt-3">';
                        detailsHtml += `<div>Page ${response.pagination.current_page} of ${response.pagination.total_pages}</div>`;
                        detailsHtml += '<div class="btn-group">';
                        if (response.pagination.current_page > 1) {
                            detailsHtml += `<button type="button" class="btn btn-outline-primary" onclick="viewReport('${studentId}', '${startDate}', '${endDate}', ${response.pagination.current_page - 1})">Previous</button>`;
                        }
                        if (response.pagination.current_page < response.pagination.total_pages) {
                            detailsHtml += `<button type="button" class="btn btn-outline-primary" onclick="viewReport('${studentId}', '${startDate}', '${endDate}', ${response.pagination.current_page + 1})">Next</button>`;
                        }
                        detailsHtml += '</div></div>';
                    }

                    // Update the modal content
                    $('#attendanceDetails').html(detailsHtml);

                    // Show the modal
                    $('#studentRecordModal').modal('show');
                },
                error: function(xhr, status, error) {
                    alert('Error fetching student records: ' + error);
                }
            });
        }
        

        $(document).ready(function() {
            // Initialize date pickers
            $(".date-picker").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // Function to update records based on date range
            window.updateRecordDateRange = function() {
                const startDate = $('#recordStartDate').val();
                const endDate = $('#recordEndDate').val();
                
                if (!startDate || !endDate) {
                    alert('Please select both start and end dates');
                    return;
                }
                
                if (window.currentStudentId) {
                    viewReport(window.currentStudentId, startDate, endDate);
                }
            };

            // Handle class type and year selection changes
            $("#classTypeSelect, #classYearSelect").change(function() {
                const classType = $("#classTypeSelect").val();
                const classYear = $("#classYearSelect").val();

                if (classType && classYear) {
                    // Enable student select and load students
                    $("#studentSelect").prop("disabled", false);
                    loadStudents(classType, classYear);
                } else {
                    // Disable and reset student select
                    $("#studentSelect").prop("disabled", true).html('<option value="">Select Student</option>');
                }
            });

            // Handle form submission
            $("#filterForm").submit(function(e) {
                e.preventDefault();
                loadAttendanceRecords();
            });

            // Handle reset button
            $("#resetBtn").click(function() {
                $("#filterForm")[0].reset();
                $("#studentSelect").prop("disabled", true).html('<option value="">Select Student</option>');
                $("#attendanceTableContainer").hide();
                $("#noRecords").show();
            });

            // Handle pagination buttons
            $('#prevPage').click(function() {
                if (currentPage > 1) {
                    viewReport(window.currentStudentId, $('#recordStartDate').val(), $('#recordEndDate').val(), currentPage - 1);
                }
            });

            $('#nextPage').click(function() {
                viewReport(window.currentStudentId, $('#recordStartDate').val(), $('#recordEndDate').val(), currentPage + 1);
            });

            // Function to load students based on class type and year
            function loadStudents(classType, classYear) {
                $.ajax({
                    url: "get_students.php",
                    type: "GET",
                    data: {
                        class_type: classType,
                        class_year: classYear
                    },
                    dataType: "json",
                    success: function(data) {
                        let options = '<option value="">Select Student</option>';

                        if (Array.isArray(data)) {
                            data.forEach(function(student) {
                                options += `<option value="${student.student_id}">${student.name}</option>`;
                            });
                        }

                        $("#studentSelect").html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading students:", error);
                        alert("Failed to load students. Please try again.");
                    }
                });
            }

            // Function to view student details
            function viewStudentDetails(studentId, studentName) {
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();

                // Update modal title and student info
                $("#modalStudentName").text(studentName);
                $("#modalStudentId").text(studentId);

                // Show loading state in modal
                $("#modalAttendanceDetails").html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
                $("#presentCount").text('...');
                $("#absentCount").text('...');
                $("#leaveCount").text('...');

                // Show the modal
                $("#studentDetailsModal").modal('show');

                // Fetch student attendance details
                $.ajax({
                    url: "get_student_info.php",
                    type: "POST",
                    data: {
                        student_id: studentId,
                        start_date: startDate,
                        end_date: endDate
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.error) {
                            $("#modalAttendanceDetails").html(`<tr><td colspan="4" class="text-center text-danger">${response.error}</td></tr>`);
                            return;
                        }

                        // Update attendance counts
                        $("#presentCount").text(response.summary.present || 0);
                        $("#absentCount").text(response.summary.absent || 0);
                        $("#leaveCount").text(response.summary.leave || 0);

                        // Generate attendance details table
                        let detailsHtml = '';
                        response.records.forEach(function(record) {
                            let statusClass = '';
                            if (record.status.toLowerCase() === 'present') statusClass = 'status-present';
                            else if (record.status.toLowerCase() === 'absent') statusClass = 'status-absent';
                            else if (record.status.toLowerCase() === 'leave') statusClass = 'status-leave';

                            detailsHtml += `
                                <tr>
                                    <td>${record.date}</td>
                                    <td class="${statusClass}">${record.status}</td>
                                    <td>${record.recorded_by}</td>
                                    <td>${record.type || 'Regular'}</td>
                                </tr>`;
                        });

                        $("#modalAttendanceDetails").html(detailsHtml || '<tr><td colspan="4" class="text-center">No attendance records found</td></tr>');
                    },
                    error: function(xhr, status, error) {
                        $("#modalAttendanceDetails").html('<tr><td colspan="4" class="text-center text-danger">Failed to load attendance details</td></tr>');
                        console.error("Error loading student details:", error);
                    }
                });
            }

            // Function to load attendance records
            function loadAttendanceRecords() {
                const classType = $("#classTypeSelect").val();
                const classYear = $("#classYearSelect").val();
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
                const studentId = $("#studentSelect").val();

                if (!classType || !classYear) {
                    alert("Please select both Class Type and Class Year");
                    return;
                }

                // Show loading spinner
                $("#noRecords").hide();
                $("#attendanceTableContainer").hide();
                $("#loadingSpinner").show();

                $.ajax({
                    url: "get_filtered_students.php",
                    type: "POST",
                    data: {
                        class_type: classType,
                        class_year: classYear,
                        start_date: startDate,
                        end_date: endDate,
                        student_id: studentId
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#loadingSpinner").hide();

                        if (response.error) {
                            alert("Error: " + response.error);
                            $("#noRecords").show();
                            return;
                        }

                        if (response.status === "empty" || !response.students || response.students.length === 0) {
                            $("#noRecords").html(`
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p>${response.message || "No attendance records found for the selected criteria."}</p>
                            `).show();
                            return;
                        }

                        // Populate table with attendance records
                        let tableHtml = '';
                        response.students.forEach(function(record) {
                            let statusClass = '';
                            if (record.status) {
                                if (record.status.toLowerCase() === 'present') {
                                    statusClass = 'status-present';
                                } else if (record.status.toLowerCase() === 'absent') {
                                    statusClass = 'status-absent';
                                } else if (record.status.toLowerCase() === 'leave') {
                                    statusClass = 'status-leave';
                                }
                            }
                            
                            tableHtml += `
                                <tr>
                                    <td>${record.student_name}</td>
                                    <td>${record.student_id}</td>
                                    <td>${record.class_type} - ${record.class_year}</td>
                                    <td>
                                        <span class="badge ${record.attendance_percentage >= 75 ? 'bg-success' : record.attendance_percentage >= 50 ? 'bg-warning' : 'bg-danger'} p-2">
                                            ${record.attendance_percentage}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger p-2">${record.absent_count}</span>
                                    </td>
                                    <td>${startDate} - ${endDate}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm me-2" onclick="viewReport('${record.student_id}')">Record Check</button>
                                    </td>
                                </tr>
                            `;
                        });

                        $("#attendanceTableBody").html(tableHtml);
                        $("#attendanceTableContainer").show();

                        // Attach event listeners to view buttons
                        $(".view-details").click(function() {
                            const studentId = $(this).data("student-id");
                            const studentName = $(this).data("student-name");
                            showStudentDetails(studentId, studentName, response.students);
                        });
                    },
                    error: function(xhr, status, error) {
                        $("#loadingSpinner").hide();
                        $("#noRecords").html(`
                            <i class="fas fa-exclamation-triangle fa-2x mb-3 text-danger"></i>
                            <p>Error loading attendance records. Please try again.</p>
                            <p class="text-muted small">${error}</p>
                        `).show();
                        console.error("AJAX Error:", error);
                    }
                });
            }

            // Function to show student details in modal
            function showStudentDetails(studentId, studentName, allRecords) {
                // Filter records for this student
                const studentRecords = allRecords.filter(record => record.student_id == studentId);

                // Set student info in modal
                $("#modalStudentName").text(studentName);
                $("#modalStudentId").text(studentId);

                // Count attendance statuses
                let presentCount = 0;
                let absentCount = 0;
                let leaveCount = 0;

                studentRecords.forEach(function(record) {
                    const status = record.status.toLowerCase();
                    if (status === 'present') presentCount++;
                    else if (status === 'absent') absentCount++;
                    else if (status === 'leave') leaveCount++;
                });

                // Update attendance summary
                $("#presentCount").text(presentCount);
                $("#absentCount").text(absentCount);
                $("#leaveCount").text(leaveCount);

                // Populate attendance details table
                let detailsHtml = '';
                studentRecords.forEach(function(record) {
                    let statusClass = '';
                    if (record.status.toLowerCase() === 'present') {
                        statusClass = 'status-present';
                    } else if (record.status.toLowerCase() === 'absent') {
                        statusClass = 'status-absent';
                    } else if (record.status.toLowerCase() === 'leave') {
                        statusClass = 'status-leave';
                    }

                    detailsHtml += `
                        <tr>
                            <td>${record.date}</td>
                            <td class="${statusClass}">${record.status}</td>
                            <td>${record.recorded_by}</td>
                            <td>${record.type}</td>
                        </tr>
                    `;
                });

                $("#modalAttendanceDetails").html(detailsHtml);

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
                modal.show();
            }
        });
    </script>
</body>

</html>