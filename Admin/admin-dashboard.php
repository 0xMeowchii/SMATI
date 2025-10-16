<?php
include '../database.php';

//ACTIVE STUDENTS
$conn = connectToDB();
$sql = "SELECT * FROM students WHERE status = '1'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$activeStudents = $result->num_rows;

//TOTAL STUDENTS
$conn = connectToDB();
$sql = "SELECT * FROM students";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$totalStudents = $result->num_rows;

//ACTIVE TEACHERS
$conn = connectToDB();
$sql = "SELECT * FROM teachers WHERE status = '1'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$activeTeachers = $result->num_rows;

//TOTAL TEACHERS
$conn = connectToDB();
$sql = "SELECT * FROM teachers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$totalTeachers = $result->num_rows;

//SET A
$conn = connectToDB();
$sql = "SELECT * FROM students WHERE status = '1' AND course = 'A'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$setA = $result->num_rows;

//SET B
$conn = connectToDB();
$sql = "SELECT * FROM students WHERE status = '1' AND course = 'B'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$setB = $result->num_rows;

//TOTAL ANNOUNCEMENT
$conn = connectToDB();
$sql = "SELECT * FROM announcements";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$totalAnnouncements = $result->num_rows;

//TOTAL ACTIVITY LOGS
$conn = connectToDB();
$sql = "SELECT * FROM activity_logs WHERE DATE(created_at) = CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$todayActivitylogs = $result->num_rows;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="./css/darkmode-variable.css">
    <link rel="stylesheet" href="./css/admin.css">
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .stat-card:hover .text-primary {
            color: #4cc9f0 !important;
        }

        .list-card {
            transition: all 0.3s ease;
        }

        .list-card:hover {
            transform: translateX(3px);
            background: rgba(67, 97, 238, 0.05);
        }

        .logs-card {
            transition: all 0.3s ease;
        }

        .logs-card:hover {
            transform: translateX(3px);
            background: rgba(67, 97, 238, 0.05);
        }

        .overflow-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .overflow-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Search input styling */
        #activitylogSearch:focus {
            border-color: #4361ee !important;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25) !important;
        }

        /* No results message styling */
        .no-activity-results {
            color: #6c757d;
            font-style: italic;
        }
    </style>

</head>

<body>
    <!-- Sidebar -->
    <?php

    include('sidebar.php');

    //Fetch announcements
    $conn = connectToDB();
    $sql = "SELECT * FROM announcements ORDER BY announcement_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $announcements = [];
    while ($row = $result->fetch_assoc()) {
        $announcements[] = [
            'title' => $row['title'],
            'details' => $row['details'],
            'date' => new DateTime($row['createdAt'])
        ];
    }

    //Fetch activity logs
    $conn = connectToDB();
    $sql = "SELECT al.*,
            CASE
                WHEN al.user_type = 'admin' THEN a.username
                WHEN al.user_type = 'teacher' THEN CONCAT(t.lastname, ', ', t.firstname)
              END AS user_name
            FROM activity_logs al
            LEFT JOIN admin a ON al.user_id = a.admin_id AND al.user_type = 'admin'
            LEFT JOIN teachers t ON al.user_id = t.teacher_id AND al.user_type = 'teacher'
            WHERE DATE(created_at) = CURDATE()
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = [
            'name' => $row['user_name'],
            'action' => $row['action'],
            'user_type' => $row['user_type'],
            'description' => $row['user_name'] . " " . $row['description'],
            'date' => new DateTime($row['created_at'])
        ];
    }

    ?>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h1>
            <div class="d-flex align-items-center gap-3">
                <div>
                    <i class="bi bi-clock-history me-1"></i>
                    Last Login: <?php echo $_SESSION['last_login'] ?>
                </div>
                <button
                    class="dark-mode-toggle"
                    onclick="toggleDarkMode()"
                    title="Toggle Dark Mode"
                    aria-label="Toggle Dark Mode">
                    <i class="fas fa-moon" id="darkModeIcon"></i>
                </button>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card border-0 shadow-sm hover-lift position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #4361ee 0%, #4cc9f0 100%);"></div>
                    <div class="card-body text-center py-4">
                        <div class="mb-3 text-primary opacity-75">
                            <i class="bi bi-people-fill display-6"></i>
                        </div>
                        <div class="display-6 fw-bold text-primary mb-2"><?php echo $totalStudents; ?></div>
                        <div class="text-muted text-uppercase small mb-3 tracking-wide">Total Students</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card border-0 shadow-sm hover-lift position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #4361ee 0%, #4cc9f0 100%);"></div>
                    <div class="card-body text-center py-4">
                        <div class="mb-3 text-primary opacity-75">
                            <i class="bi bi-person-check-fill display-6"></i>
                        </div>
                        <div class="display-6 fw-bold text-primary mb-2"><?php echo $activeStudents; ?></div>
                        <div class="text-muted text-uppercase small mb-3 tracking-wide">Active Students</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card  border-0 shadow-sm hover-lift position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #4361ee 0%, #4cc9f0 100%);"></div>
                    <div class="card-body text-center py-4">
                        <div class="mb-3 text-primary opacity-75">
                            <i class="bi bi-person-badge display-6"></i>
                        </div>
                        <div class="display-6 fw-bold text-primary mb-2"><?php echo $totalTeachers; ?></div>
                        <div class="text-muted text-uppercase small mb-3 tracking-wide">Total Teachers</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card border-0 shadow-sm hover-lift position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #4361ee 0%, #4cc9f0 100%);"></div>
                    <div class="card-body text-center py-4">
                        <div class="mb-3 text-primary opacity-75">
                            <i class="bi bi-person-check display-6"></i>
                        </div>
                        <div class="display-6 fw-bold text-primary mb-2"><?php echo $activeTeachers; ?></div>
                        <div class="text-muted text-uppercase small mb-3 tracking-wide">Active Teachers</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Graph -->
        <div class="row g-4 mt-4">
            <div class="col-12 col-lg-7">
                <div class="card rounded-4 bg-white shadow">
                    <div class="card-header rounded-top-4 p-3 bg-primary fw-bold text-white">
                        Active Accounts By Category
                    </div>
                    <div class="card-body">
                        <div class="p-2">
                            <canvas id="accountChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="card rounded-4 bg-white shadow">
                    <div class="card-header rounded-top-4 p-3 bg-primary fw-bold text-white">
                        Section Distribution
                    </div>
                    <div class="card-body">
                        <div class="p-2">
                            <canvas id="sectionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row mt-5">
            <!-- Activity Logs Card -->
            <div class="col-12 col-lg-4">
                <div class="card rounded-4 bg-white shadow h-100">
                    <div class="card-header rounded-top-4 bg-primary fw-bold text-white d-flex justify-content-between align-items-center">
                        <span>Activity Logs</span>
                        <span class="badge bg-light text-dark"><?php echo $todayActivitylogs; ?> Activities</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="input-group input-group-md position-relative mb-3">
                            <span class="input-group-text bg-white border-2 border-end-0 rounded-start-4">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"
                                class="form-control rounded-end-4 border-2 border-start-0"
                                id="activitylogSearch"
                                placeholder="Search activity logs...">
                        </div>

                        <!-- Scrollable Activity List -->
                        <div class="flex-grow-1 overflow-auto" style="max-height: 400px;">
                            <?php foreach ($logs as $log): ?>
                                <div class="card logs-card border-0 mb-3">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0 fw-bold"><?php echo $log['action'] ?></h6>
                                            <span class="badge bg-primary rounded-pill"><?php echo $log['user_type'] ?></span>
                                        </div>
                                        <p class="card-text text-muted mb-2"><?php echo $log['description'] ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i><?php echo $log['date']->format('m-d-Y h:i A l') ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- View All Button -->
                        <div class="mt-3 pt-3 border-top">
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#view-activitylogs-modal">
                                <i class="fas fa-list me-2"></i>View All Activity Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Announcements Card -->
            <div class="col-12 col-lg-8">
                <div class="card rounded-4 bg-white shadow h-100">
                    <div class="card-header rounded-top-4 bg-primary fw-bold text-white d-flex justify-content-between align-items-center">
                        <span>Recent Announcement Board</span>
                        <span class="badge bg-light text-dark"><?php echo $totalAnnouncements ?> Announcements</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Scrollable Announcements List -->
                        <div class="flex-grow-1 overflow-auto" style="max-height: 400px;">

                            <?php foreach ($announcements as $announcement): ?>
                                <div class="card list-card border-0 bg-light mb-3">
                                    <div class="card-body border-start rounded-4 border-4 border-primary py-3">
                                        <div class="text-muted small mb-1"><?php echo $announcement['date']->format('m-d-Y h:i A l') ?></div>
                                        <h6 class="card-title mb-1 fw-bold"><?php echo $announcement['title'] ?></h6>
                                        <p class="card-text small mb-0"><?php echo $announcement['details'] ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- View All Button -->
                            <div class="mt-3 pt-3 border-top">
                                <a href="admin-announcements.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-bullhorn me-2"></i>View All Announcements
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs Modal -->
            <div class="modal modal-lg fade" id="view-activitylogs-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Activity Logs</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Filters -->
                            <div class="row mb-3 g-2">
                                <div class="col-md-3">
                                    <select class="form-control rounded-4 border-2" id="filterUserType">
                                        <option value="">All User Types</option>
                                        <option value="admin">Admin</option>
                                        <option value="teacher">Teacher</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control rounded-4 border-2" id="searchQuery" placeholder="Search...">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control rounded-4 border-2" id="filterDate">
                                        <option value="">Sort by: Date</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="7days">Last 7 days</option>
                                        <option value="month">Last month</option>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-hover">
                                    <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>User</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $conn = connectToDB();
                                        $sql = "SELECT al.*,
                                                CASE
                                                    WHEN al.user_type = 'admin' THEN a.username
                                                    WHEN al.user_type = 'teacher' THEN CONCAT(t.lastname, ', ', t.firstname)
                                                END AS user_name
                                                FROM activity_logs al 
                                                LEFT JOIN admin a ON al.user_id = a.admin_id AND al.user_type = 'admin'
                                                LEFT JOIN teachers t ON al.user_id = t.teacher_id AND al.user_type = 'teacher'
                                                ORDER BY id DESC";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        if ($result && $result->num_rows > 0) {

                                            while ($row = $result->fetch_assoc()) {
                                                $date = new DateTime($row['created_at']);
                                                echo "<tr>";
                                                echo "<td>" . $date->format('m-d-Y h:i A l') . "</td>";
                                                echo "<td>" . $row["user_name"] . "</td>";
                                                echo "<td>" . $row['user_type'] . "</td>";
                                                echo "<td>" . $row['action'] . "</td>";
                                                echo "<td>" . $row['description'] . "</td>";
                                                echo "<td>" . $row['ip_address'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr class='no-results-message'>";
                                            echo "<td colspan='6' class='text-center py-4' style='color: #6c757d;'>";
                                            echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                                            echo "<br>No activity logs found.";
                                            echo "</td>";
                                            echo "</tr>";
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./js/darkmode.js"></script>
    <script>
        const barCtx = document.getElementById('accountChart');
        const pieCtx = document.getElementById('sectionChart');

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: [''],
                datasets: [{
                    label: 'Students',
                    data: [<?php echo $activeStudents ?>],
                    backgroundColor: 'rgba(12, 172, 236, 0.3)',
                    borderColor: 'rgba(0, 13, 255, 1)',
                    borderWidth: 2
                }, {
                    label: 'Teachers',
                    data: [<?php echo $activeTeachers ?>],
                    backgroundColor: 'rgba(0, 255, 30, 0.3)',
                    borderColor: 'rgba(13, 115, 6, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Active Accounts'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Accounts'
                        }
                    }
                }
            }
        });

        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Section A', 'Section B'],
                datasets: [{
                    data: [<?php echo $setA; ?>, <?php echo $setB; ?>],
                    backgroundColor: [
                        'rgba(252, 249, 50, 0.5)',
                        'rgba(255, 61, 2, 0.5)'
                    ],
                    borderColor: [
                        '#b58406ff', // Darker yellow border
                        '#b50707ff' // Darker red border
                    ],
                    borderWidth: 4,
                    borderAlign: 'inner',
                    hoverOffset: 15
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Function to filter activity logs
        function filterActivityLogs() {
            const userTypeFilter = document.getElementById('filterUserType').value.toLowerCase();
            const searchQuery = document.getElementById('searchQuery').value.toLowerCase();
            const dateFilter = document.getElementById('filterDate').value;
            const tableRows = document.querySelectorAll('#view-activitylogs-modal tbody tr');

            let visibleRows = 0;
            const now = new Date();

            tableRows.forEach(row => {
                // Skip the "no records" row if it exists
                if (row.cells.length === 1) {
                    row.style.display = 'none';
                    return;
                }

                const userType = row.cells[2].textContent.toLowerCase(); // Type column
                const userName = row.cells[1].textContent.toLowerCase(); // User column
                const action = row.cells[3].textContent.toLowerCase(); // Action column
                const description = row.cells[4].textContent.toLowerCase(); // Description column
                const ipAddress = row.cells[5].textContent.toLowerCase(); // IP Address column
                const dateText = row.cells[0].textContent; // Date column

                // Check user type filter
                const userTypeMatch = !userTypeFilter || userType === userTypeFilter;

                // Check search query across multiple columns
                const searchMatch = !searchQuery ||
                    userName.includes(searchQuery) ||
                    action.includes(searchQuery) ||
                    description.includes(searchQuery) ||
                    ipAddress.includes(searchQuery);

                // Check date filter
                const dateMatch = checkDateFilterModal(dateText, dateFilter, now);

                // Show row only if all filters match
                if (userTypeMatch && searchMatch && dateMatch) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show "no results" message if no rows are visible
            showNoResultsMessage(visibleRows);
        }

        // Helper function to check date filter for modal table
        function checkDateFilterModal(dateText, filter, now) {
            if (!filter || filter === '') return true; // No date filter selected

            // Parse the date from the table cell (format: "mm-dd-yyyy hh:mm AM/PM Day")
            const dateMatch = dateText.match(/(\d{2}-\d{2}-\d{4})/);
            if (!dateMatch) return false;

            const [month, day, year] = dateMatch[1].split('-').map(Number);
            const cardDate = new Date(year, month - 1, day); // month is 0-indexed

            // Reset times to compare only dates
            const cardDateOnly = new Date(cardDate.getFullYear(), cardDate.getMonth(), cardDate.getDate());
            const todayOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            switch (filter) {
                case 'today':
                    return cardDateOnly.getTime() === todayOnly.getTime();

                case 'yesterday':
                    const yesterday = new Date(todayOnly);
                    yesterday.setDate(yesterday.getDate() - 1);
                    return cardDateOnly.getTime() === yesterday.getTime();

                case '7days': // Last 7 days
                    const sevenDaysAgo = new Date(todayOnly);
                    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 6); // -6 to include today (7 days total)
                    return cardDateOnly >= sevenDaysAgo && cardDateOnly <= todayOnly;

                case 'month': // Last Month
                    const firstDayOfLastMonth = new Date(todayOnly.getFullYear(), todayOnly.getMonth() - 1, 1);
                    const lastDayOfLastMonth = new Date(todayOnly.getFullYear(), todayOnly.getMonth(), 0);
                    return cardDateOnly >= firstDayOfLastMonth && cardDateOnly <= lastDayOfLastMonth;

                default:
                    return true;
            }
        }

        // Function to show no results message
        function showNoResultsMessage(visibleRows) {
            const tbody = document.querySelector('#view-activitylogs-modal tbody');
            let noResultsRow = tbody.querySelector('.no-results-message');

            if (visibleRows === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-message';
                    noResultsRow.innerHTML = `
                <td colspan="6" class="text-center py-4" style="color: #6c757d;">
                    <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                    <br>No activity logs found matching your criteria.
                </td>
            `;
                    tbody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }

        // Function to clear all filters
        function clearActivityLogFilters() {
            document.getElementById('filterUserType').value = '';
            document.getElementById('searchQuery').value = '';
            document.getElementById('filterDate').value = '';
            filterActivityLogs();
        }

        // Function to sort table by date (newest first)
        function sortTableByDate() {
            const tbody = document.querySelector('#view-activitylogs-modal tbody');
            const rows = Array.from(tbody.querySelectorAll('tr:not(.no-results-message)'));

            // Remove no results message if present
            const noResultsRow = tbody.querySelector('.no-results-message');
            if (noResultsRow) {
                noResultsRow.remove();
            }

            rows.sort((a, b) => {
                const dateA = parseDateFromTable(a.cells[0].textContent);
                const dateB = parseDateFromTable(b.cells[0].textContent);
                return dateB - dateA; // Newest first
            });

            // Reappend sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        // Helper function to parse date from table cell
        function parseDateFromTable(dateText) {
            const dateMatch = dateText.match(/(\d{2}-\d{2}-\d{4})/);
            if (!dateMatch) return new Date(0); // Return epoch date if parsing fails

            const [month, day, year] = dateMatch[1].split('-').map(Number);
            return new Date(year, month - 1, day);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const filterUserType = document.getElementById('filterUserType');
            const searchQuery = document.getElementById('searchQuery');
            const filterDate = document.getElementById('filterDate');

            if (filterUserType) {
                filterUserType.addEventListener('change', filterActivityLogs);
            }

            if (searchQuery) {
                searchQuery.addEventListener('input', filterActivityLogs);
            }

            if (filterDate) {
                filterDate.addEventListener('change', filterActivityLogs);
            }

            // Initialize filters when modal is shown
            const activityLogsModal = document.getElementById('view-activitylogs-modal');
            if (activityLogsModal) {
                activityLogsModal.addEventListener('show.bs.modal', function() {
                    // Clear filters when modal opens
                    setTimeout(() => {
                        clearActivityLogFilters();
                        sortTableByDate(); // Ensure table is sorted by date when modal opens
                    }, 100);
                });
            }

            // Sort table by date on initial load
            sortTableByDate();
        });

        function searchLogs() {
            const searchInput = document.getElementById('activitylogSearch');
            const searchTerm = searchInput.value.toLowerCase().trim();
            const activityCards = document.querySelectorAll('.logs-card');
            const activityBadge = document.querySelector('.card-header .badge'); // Select the badge element

            let visibleCount = 0;

            activityCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                const userName = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
                const userType = card.querySelector('.badge')?.textContent.toLowerCase() || '';

                // Check if any part of the card matches the search term
                const matches = cardText.includes(searchTerm) ||
                    userName.includes(searchTerm) ||
                    description.includes(searchTerm) ||
                    userType.includes(searchTerm);

                if (matches || searchTerm === '') {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update the badge with the filtered count
            if (activityBadge) {
                if (searchTerm === '') {
                    // If no search term, show the original total
                    activityBadge.textContent = '<?php echo $todayActivitylogs; ?> Activities';
                } else {
                    // Show filtered count
                    activityBadge.textContent = `${visibleCount} Activities`;
                }
            }

            // Show/hide no results message
            showNoActivityResults(visibleCount, searchTerm);
        }

        function showNoActivityResults(visibleCount, searchTerm) {
            const activityContainer = document.querySelector('.flex-grow-1.overflow-auto');
            let noResultsMessage = activityContainer.querySelector('.no-activity-results');

            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.className = 'no-activity-results text-center py-4';
                    noResultsMessage.innerHTML = `
                <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5; color: #6c757d;"></i>
                <br>
                <span style="color: #6c757d;">No activity logs found.</span>
            `;
                    activityContainer.appendChild(noResultsMessage);
                }
                noResultsMessage.style.display = 'block';
            } else if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }



        // Function to clear activity search
        function clearActivitySearch() {
            const searchInput = document.getElementById('activitylogSearch');
            searchInput.value = '';
            searchLogs();
        }

        // Add event listener for the search input
        document.addEventListener('DOMContentLoaded', function() {
            const activitySearchInput = document.getElementById('activitylogSearch');

            if (activitySearchInput) {
                // Search on input
                activitySearchInput.addEventListener('input', searchLogs);

                // Add clear button functionality
                activitySearchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        clearActivitySearch();
                    }
                });
            }
        });
    </script>
</body>

</html>