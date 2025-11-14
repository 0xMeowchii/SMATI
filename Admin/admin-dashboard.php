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

// Students count with fallback logic (today -> yesterday -> overall)
$conn = connectToDB();
$sql = "SELECT 
            COUNT(CASE WHEN action = 'CREATE_STUDENT' AND DATE(created_at) = CURDATE() THEN 1 END) as student_count_today,
            COUNT(CASE WHEN action = 'CREATE_STUDENT' AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 END) as student_count_yesterday,
            COUNT(CASE WHEN action = 'CREATE_STUDENT' AND created_at = (SELECT MAX(created_at) FROM activity_logs WHERE user_type = 'admin' AND action = 'CREATE_STUDENT') THEN 1 END) as student_count_overall,
            MAX(CASE WHEN DATE(created_at) = CURDATE() THEN created_at END) as today_latest,
            MAX(CASE WHEN DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN created_at END) as yesterday_latest,
            MAX(created_at) as overall_latest
        FROM activity_logs
        WHERE user_type = 'admin' 
        AND action IN ('CREATE_STUDENT')";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$todayStudents = 0;
$latestStudentCreated = null;
$dataDate = "today"; // "today", "yesterday", or "overall"

if ($row = $result->fetch_assoc()) {
    // Use today's data if available
    if ($row['student_count_today'] > 0) {
        $todayStudents = $row['student_count_today'];
        $dataDate = "today";
        $latestStudentCreated = $row['today_latest'] ? new DateTime($row['today_latest']) : 0;
    }
    // Otherwise use yesterday's data
    elseif ($row['student_count_yesterday'] > 0) {
        $todayStudents = $row['student_count_yesterday'];
        $dataDate = "yesterday";
        $latestStudentCreated = $row['yesterday_latest'] ? new DateTime($row['yesterday_latest']) : 0;
    }
    // If no data today or yesterday, get overall data
    else {
        $todayStudents = $row['student_count_overall'];
        $dataDate = "overall";
        $latestStudentCreated = $row['overall_latest'] ? new DateTime($row['overall_latest']) : new DateTime();
    }
}

// Students created THIS MONTH
$conn = connectToDB();
$sql_month = "SELECT COUNT(*) as count FROM students WHERE YEAR(createdAt) = YEAR(CURDATE()) AND MONTH(createdAt) = MONTH(CURDATE())";
$stmt_month = $conn->prepare($sql_month);
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$row_month = $result_month->fetch_assoc();
$monthlyStudents = $row_month['count'];

//GET TEACHER/REGISTRAR CREATED WITH SEPARATE COUNTS
$conn = connectToDB();
$sql = "SELECT 
            COUNT(CASE WHEN action = 'CREATE_TEACHER' AND DATE(created_at) = CURDATE() THEN 1 END) as teacher_count_today,
            COUNT(CASE WHEN action = 'CREATE_REGISTRAR' AND DATE(created_at) = CURDATE() THEN 1 END) as registrar_count_today,
            COUNT(CASE WHEN action = 'CREATE_TEACHER' AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 END) as teacher_count_yesterday,
            COUNT(CASE WHEN action = 'CREATE_REGISTRAR' AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 END) as registrar_count_yesterday,
            COUNT(CASE WHEN action = 'CREATE_TEACHER' THEN 1 END) as teacher_count_overall,
            COUNT(CASE WHEN action = 'CREATE_REGISTRAR' THEN 1 END) as registrar_count_overall,
            MAX(CASE WHEN DATE(created_at) = CURDATE() THEN created_at END) as today_latest,
            MAX(CASE WHEN DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN created_at END) as yesterday_latest,
            MAX(created_at) as overall_latest
        FROM activity_logs 
        WHERE user_type = 'admin' 
        AND action IN ('CREATE_TEACHER', 'CREATE_REGISTRAR')";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$teacherCount = 0;
$registrarCount = 0;
$totalCount = 0;
$bothlastCreated = null;
$dataDate = "today"; // "today", "yesterday", or "overall"

if ($row = $result->fetch_assoc()) {
    // Use today's data if available
    if ($row['teacher_count_today'] > 0 || $row['registrar_count_today'] > 0) {
        $teacherCount = $row['teacher_count_today'];
        $registrarCount = $row['registrar_count_today'];
        $dataDate = "today";
        $bothlastCreated = $row['today_latest'] ? new DateTime($row['today_latest']) : 0;
    }
    // Otherwise use yesterday's data
    elseif ($row['teacher_count_yesterday'] > 0 || $row['registrar_count_yesterday'] > 0) {
        $teacherCount = $row['teacher_count_yesterday'];
        $registrarCount = $row['registrar_count_yesterday'];
        $dataDate = "yesterday";
        $bothlastCreated = $row['yesterday_latest'] ? new DateTime($row['yesterday_latest']) : 0;
    }
    // If no data today or yesterday, get overall data
    else {
        $teacherCount = $row['teacher_count_overall'];
        $registrarCount = $row['registrar_count_overall'];
        $dataDate = "overall";
        $bothlastCreated = $row['overall_latest'] ? new DateTime($row['overall_latest']) : new DateTime();
    }

    $totalCount = $teacherCount + $registrarCount;
}

// Teachers created THIS MONTH
$conn = connectToDB();
$sql_month = "SELECT COUNT(*) as count FROM teachers WHERE YEAR(createdAt) = YEAR(CURDATE()) AND MONTH(createdAt) = MONTH(CURDATE())";
$stmt_month = $conn->prepare($sql_month);
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$row_month = $result_month->fetch_assoc();
$monthlyTeachers = $row_month['count'];


// Registrars created THIS MONTH
$conn = connectToDB();
$sql_month = "SELECT COUNT(*) as count FROM registrars WHERE YEAR(createdAt) = YEAR(CURDATE()) AND MONTH(createdAt) = MONTH(CURDATE())";
$stmt_month = $conn->prepare($sql_month);
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$row_month = $result_month->fetch_assoc();
$monthlyRegistrars = $row_month['count'];

//TOTAL REGISTRARS
$conn = connectToDB();
$sql = "SELECT * FROM registrars";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$totalRegistrars = $result->num_rows;

//ACTIVE TEACHERS
$conn = connectToDB();
$sql = "SELECT * FROM teachers WHERE status = '1'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$activeTeachers = $result->num_rows;

//ACTIVE REGISTRARS
$conn = connectToDB();
$sql = "SELECT * FROM registrars WHERE status = '1'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$activeRegistrars = $result->num_rows;


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

//GET LAST LOGIN
$conn = connectToDB();
$sql = "SELECT created_at 
        FROM activity_logs 
        WHERE user_type = 'admin' AND action = 'LOGIN' 
        ORDER BY created_at DESC 
        LIMIT 1 OFFSET 1";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $secondLastLogin = new DateTime($row['created_at']);
} else {
    $secondLastLogin = new DateTime();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
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

        .auth-tabs {
            display: flex;
            width: 100%;
            border-bottom: 1px solid #dee2e6;
        }

        .auth-tab {
            flex: 1;
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
        }

        .auth-tab.active {
            border-bottom-color: #007bff;
            color: #007bff;
        }

        .otp-input {
            letter-spacing: 30px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 10px 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php

    include('includes/sidebar.php');

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
            'target' => $row['target'],
            'date' => new DateTime($row['createdAt'])
        ];
    }

    //Fetch registrations
    $conn = connectToDB();
    $sql = "SELECT * FROM registrations ORDER BY reg_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = [
            'reg_number' => $row['reg_number'],
            'fullname' => $row['firstname'] . ' ' . $row['lastname'],
            'birthdate' => $row['birthdate'],
            'gender' => $row['gender'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'school_visit' => $row['school_visit'],
            'program' => $row['program'],
            'program_details' => $row['program_details'],
            'reg_date' => new DateTime($row['reg_date'])
        ];
    }

    //Fetch activity logs today
    $conn = connectToDB();
    $sql = "SELECT al.*,
            CASE
                WHEN al.user_type = 'admin' THEN a.username
                WHEN al.user_type = 'teacher' THEN CONCAT(t.lastname, ', ', t.firstname)
                WHEN al.user_type = 'student' THEN CONCAT(s.lastname, ', ', s.firstname)
                WHEN al.user_type = 'registrar' THEN r.username
              END AS user_name
            FROM activity_logs al
            LEFT JOIN admin a ON al.user_id = a.admin_id AND al.user_type = 'admin'
            LEFT JOIN teachers t ON al.user_id = t.teacher_id AND al.user_type = 'teacher'
            LEFT JOIN students s ON al.user_id = s.student_id AND al.user_type = 'student'
            LEFT JOIN registrars r ON al.user_id = r.registrar_id AND al.user_type = 'registrar'
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
                    Last Login: <?php echo $secondLastLogin->format('m-d-Y h:i A'); ?>
                </div>
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
                        <div class="text-muted small bg-body-tertiary border-start border-4 border-primary-subtle rounded-3 fst-italic py-2">
                            <i class="bi bi-arrow-up"></i><strong><?php echo $monthlyStudents; ?></strong> as of <strong>this month </strong>
                        </div>
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
                        <div class="text-muted text-uppercase small mb-3 tracking-wide">Active Students
                        </div>
                        <div class="text-muted small bg-body-tertiary border-start border-4 border-primary-subtle rounded-3 fst-italic py-2">
                            <i class="bi bi-arrow-up"></i><strong><?php echo $todayStudents; ?></strong> as of <strong><?php echo $latestStudentCreated->format('m-d-Y h:i A'); ?></strong>
                        </div>
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
                        <div class="display-6 fw-bold text-primary mb-2"><?php echo $totalTeachers; ?> / <?php echo $totalRegistrars; ?> </div>
                        <div class="text-muted text-uppercase small mb-3 tracking-wide">Total Teachers/Registrars</div>
                        <div class="text-muted small bg-body-tertiary border-start border-4 border-primary-subtle rounded-3 fst-italic py-2">
                            <i class="bi bi-arrow-up"></i><strong><?php echo $monthlyTeachers; ?>/<?php echo $monthlyRegistrars; ?></strong> as of <strong>this month</strong>
                        </div>
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
                        <div class="display-6 fw-bold text-primary mb-2"><?php echo $activeTeachers; ?> / <?php echo $activeRegistrars; ?></div>
                        <div class="text-muted text-uppercase small mb-3 tracking-wide">Active Teachers/Registrars</div>
                        <div class="text-muted small bg-body-tertiary border-start border-4 border-primary-subtle rounded-3 fst-italic py-2">
                            <i class="bi bi-arrow-up"></i><strong><?php echo $teacherCount; ?> / <?php echo $registrarCount; ?></strong> as of <strong><?php echo $bothlastCreated->format('m-d-Y h:i A'); ?></strong>
                        </div>
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
        <div class="row mt-5 g-4">
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


                    </div>
                    <div class="card-footer">
                        <!-- View All Button -->
                        <button class="btn btn-outline-primary w-100" id="view-all-activity-btn">
                            <i class="fas fa-list me-2"></i>View All Activity Logs
                        </button>
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
                        <div class="flex-grow-1 overflow-auto" style="max-height: 500px;">
                            <?php if (!empty($announcements)): ?>
                                <?php foreach ($announcements as $announcement): ?>
                                    <div class="card list-card border-0 bg-light mb-3">
                                        <div class="card-body border-start rounded-4 border-4 border-primary py-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="text-muted small mb-1"><?php echo $announcement['date']->format('m-d-Y h:i A l') ?></div>
                                                <p class="card-text small"><i class="bi bi-person-fill me-1"></i><?php echo $announcement['target'] ?></p>
                                            </div>

                                            <h6 class="card-title mb-1 fw-bold"><?php echo $announcement['title'] ?></h6>
                                            <p class="card-text small mb-1"><?php echo $announcement['details'] ?></p>

                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-inbox mb-3" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                                    </div>
                                    <h4 class="text-muted mb-3">No Annoucement Yet</h4>
                                    <p class="text-muted mb-4">Create your first announcement to get started.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="admin-announcements.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-bullhorn me-2"></i>View All Announcements
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- REG SUMMARY -->
        <div class="filter-section bg-white rounded shadow-sm p-4 mb-4 sticky-top mt-5" style="top: 20px; z-index: 100;">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="searchBar" class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchBar" placeholder="Search by name, program, etc.">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="filterProgram" class="form-label">Filter by Program</label>
                    <select class="form-select" id="filterProgram">
                        <option value="all">All Programs</option>
                        <option value="shs">Senior High School</option>
                        <option value="college">College</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="filterDate" class="form-label">Filter by Date</label>
                    <input type="date" class="form-control" id="filterDate">
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-success" id="downloadAllPdf" title="Download all visible registrations">
                        <i class="fas fa-file-pdf me-1"></i>Download All
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-grow-1 overflow-auto" style="max-height: 500px;">
            <?php if (!empty($registrations)): ?>
                <?php foreach ($registrations as $reg): ?>
                    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary" data-reg-number="<?php echo $reg['reg_number'] ?>"
                        data-program="<?php echo strtolower($reg['program']) ?>"
                        data-reg-date="<?php echo $reg['reg_date']->format('Y-m-d') ?>">
                        <div class=" card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span class="fw-semibold"><?php echo $reg['reg_number'] ?></span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-light download-single-btn" data-reg-number="<?php echo $reg['reg_number'] ?>">
                                    <i class="fas fa-download me-1"></i>Download
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-reg-number="<?php echo $reg['reg_number'] ?>">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5 text-muted">Registration Number: </dt>
                                        <dd class="col-sm-7 fw-semibold"><?php echo $reg['reg_number'] ?></dd>

                                        <dt class="col-sm-5 text-muted">Name:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['fullname'] ?></dd>

                                        <dt class="col-sm-5 text-muted">Date of Birth:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['birthdate'] ?></dd>

                                        <dt class="col-sm-5 text-muted">Gender:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['gender'] ?></dd>

                                        <dt class="col-sm-5 text-muted">Email:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['email'] ?></dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5 text-muted">Phone:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['phone'] ?></dd>

                                        <dt class="col-sm-5 text-muted">Address:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['address'] ?></dd>

                                        <dt class="col-sm-5 text-muted">School Visitation:</dt>
                                        <dd class="col-sm-7">
                                            <?php
                                            if ($reg['school_visit'] === 'yes') {
                                                echo 'Yes, I would like to schedule a school visit';
                                            } elseif ($reg['school_visit'] === 'no') {
                                                echo ' No, I do not need a school visit';
                                            } else {
                                                echo 'I have already visited the school';
                                            }
                                            ?>
                                        </dd>

                                        <dt class="col-sm-5 text-muted">Program:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['program_details'] ?></dd>

                                        <dt class="col-sm-5 text-muted">Registration Date:</dt>
                                        <dd class="col-sm-7"><?php echo $reg['reg_date']->format('m-d-Y h:i A') ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- No Data Message -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox mb-3" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Registrations Yet</h4>
                    <p class="text-muted mb-4">There are no registration records in the system yet.</p>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-refresh me-2"></i>Refresh Page
                    </button>
                </div>
            <?php endif; ?>

            <div class="no-registration-results text-center py-5 d-none" id="noRegistrationResults">
                <div class="mb-4">
                    <i class="fas fa-search mb-3" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                </div>
                <h4 class="text-muted mb-3">No Registrations Found</h4>
                <p class="text-muted mb-4">No registration records match your current search criteria.</p>
                <button type="button" class="btn btn-primary" onclick="clearRegistrationFilters()">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </button>
            </div>
        </div>


        <!-- Activity Logs Modal -->
        <div class="modal modal-lg" id="view-activitylogs-modal">
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
                                    <option value="registrar">Registrar</option>
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
                                                     WHEN al.user_type = 'student' THEN CONCAT(s.lastname, ', ', s.firstname)
                                                    WHEN al.user_type = 'registrar' THEN r.username
                                                END AS user_name
                                                FROM activity_logs al 
                                                LEFT JOIN admin a ON al.user_id = a.admin_id AND al.user_type = 'admin'
                                                LEFT JOIN teachers t ON al.user_id = t.teacher_id AND al.user_type = 'teacher'
                                                LEFT JOIN students s ON al.user_id = s.student_id AND al.user_type = 'student'
                                                LEFT JOIN registrars r ON al.user_id = r.registrar_id AND al.user_type = 'registrar'
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
                        <button id="exportPdf" class="btn btn-success">
                            <i class="fas fa-file-pdf me-2"></i>Export to PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- AUTHENTICATION MODAL -->
        <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Authentication Required</h2>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-envelope-circle-check text-primary" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">SMATI Authentication</h4>
                            <p class="text-muted">Choose your authentication method to proceed.</p>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="auth-tabs" role="tablist">
                                <button class="auth-tab active"
                                    id="password-tab"
                                    type="button"
                                    role="tab"
                                    onclick="switchAuthMethod('password')">
                                    Authentication Key
                                </button>
                                <button class="auth-tab"
                                    id="pin-tab"
                                    type="button"
                                    role="tab"
                                    onclick="switchAuthMethod('pin')">
                                    PIN
                                </button>
                            </div>
                        </div>

                        <form id="authForm">

                            <input type="hidden" id="authMethod" name="authMethod" value="password">

                            <!-- Authentication Key Section -->
                            <div class="d-block" id="authPassword">
                                <label class="form-label">SMATI Authentication Key</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" placeholder="Enter SMATI Key" id="authKey" name="authKey">
                                    <span class="input-group-text"
                                        onmousedown="document.getElementById('authKey').type='text'"
                                        onmouseup="document.getElementById('authKey').type='password'"
                                        onmouseleave="document.getElementById('authKey').type='password'">
                                        <i class="bi bi-eye"></i></span>
                                </div>
                            </div>

                            <!-- PIN Section -->
                            <div class="d-none" id="authPIN">
                                <label class="form-label text-center">Enter 6-digit PIN</label>
                                <input type="password"
                                    class="form-control otp-input"
                                    maxlength="6"
                                    placeholder="000000"
                                    name="authPIN"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnAuth">Authenticate</button>
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
        // Enhanced PDF Download Functions
        function downloadRegistrationPDF(regNumber = null) {
            const {
                jsPDF
            } = window.jspdf;

            if (regNumber) {
                // Single registration - detailed format
                downloadSingleRegistrationPDF(regNumber);
            } else {
                // All registrations - table format
                downloadAllRegistrationsPDF();
            }
        }

        function downloadSingleRegistrationPDF(regNumber) {
            const {
                jsPDF
            } = window.jspdf;

            const centuryGothicNormal = '../fonts/centurygothic.ttf';
            const centuryGothicBold = '../fonts/centurygothic_bold.ttf';

            // Find the specific registration card
            const card = document.querySelector(`[data-reg-number="${regNumber}"]`);
            if (!card) {
                Swal.fire({
                    icon: 'error',
                    title: 'Not Found',
                    text: 'Registration data not found.',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            // Extract data from the card
            const dl = card.querySelectorAll('dl.row.mb-0');
            const leftData = dl[0].querySelectorAll('dd');
            const rightData = dl[1].querySelectorAll('dd');

            const registration = {
                reg_number: card.querySelector('.fw-semibold').textContent,
                name: leftData[1].textContent,
                birthdate: leftData[2].textContent,
                gender: leftData[3].textContent,
                email: leftData[4].textContent,
                phone: rightData[0].textContent,
                address: rightData[1].textContent,
                school_visit: rightData[2].textContent,
                program: rightData[3].textContent,
                reg_date: rightData[4].textContent
            };

            // Create PDF
            const doc = new jsPDF('p', 'mm', 'a4');
            const now = new Date();

            // Add logo
            const logoUrl = '../images/logo5.png';
            try {
                // Add logo to the top left and right
                doc.addImage(logoUrl, 'PNG', 45, 10, 20, 20);
            } catch (e) {
                console.warn('Logo could not be loaded:', e);
            }

            // Add the font to jsPDF (you need to load the font files first)
            doc.addFont(centuryGothicNormal, 'CenturyGothic', 'normal');
            doc.addFont(centuryGothicBold, 'CenturyGothic', 'bold');
            doc.setFont('CenturyGothic');

            // Add title and header
            doc.setFontSize(18);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(13, 110, 253);
            doc.text('REGISTRATION DETAILS', 105, 20, {
                align: 'center'
            });

            doc.setFontSize(10);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(100, 100, 100);
            doc.text(`Generated on: ${now.toLocaleDateString()} ${now.toLocaleTimeString()}`, 105, 27, {
                align: 'center'
            });

            // Registration number
            doc.setFontSize(14);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(0, 0, 0);
            doc.text(`Registration Number: ${registration.reg_number}`, 20, 40);

            // Personal Information Section
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(13, 110, 253);
            doc.text('PERSONAL INFORMATION', 20, 55);

            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            doc.setFont(undefined, 'bold');
            doc.text('Full Name:', 20, 65);
            doc.setFont(undefined, 'normal');
            doc.text(registration.name, 40, 65);
            doc.setFont(undefined, 'bold');
            doc.text('Date of Birth:', 20, 72);
            doc.setFont(undefined, 'normal');
            doc.text(registration.birthdate, 45, 72);
            doc.setFont(undefined, 'bold');
            doc.text('Gender:', 20, 79);
            doc.setFont(undefined, 'normal');
            doc.text(registration.gender, 40, 79);

            // Contact Information Section
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(13, 110, 253);
            doc.text('CONTACT INFORMATION', 20, 94);

            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            doc.setFont(undefined, 'bold');
            doc.text('Email:', 20, 104);
            doc.setFont(undefined, 'normal');
            doc.text(registration.email, 40, 104);
            doc.setFont(undefined, 'bold');
            doc.text('Phone:', 20, 111);
            doc.setFont(undefined, 'normal');
            doc.text(registration.phone, 40, 111);
            doc.setFont(undefined, 'bold');
            doc.text('Address:', 20, 118);
            doc.setFont(undefined, 'normal');
            doc.text(registration.address, 40, 118);


            // Program & Visit Information Section
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(13, 110, 253);
            doc.text('PROGRAM INFORMATION', 20, 140);

            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            doc.setFont(undefined, 'bold');
            doc.text('Program', 20, 150);
            doc.setFont(undefined, 'normal');
            doc.text(registration.program, 40, 150);
            doc.setFont(undefined, 'bold');
            doc.text('Registration Date:', 20, 160);
            doc.setFont(undefined, 'normal');
            doc.text(registration.reg_date, 55, 160);

            // Add border and styling
            doc.setDrawColor(13, 110, 253);
            doc.setLineWidth(0.5);
            doc.rect(15, 5, 180, 175);

            // Add footer
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text('SMATI Registration System - Confidential Document', 105, 185, {
                align: 'center'
            });

            // Save PDF
            const filename = `registration-details-${regNumber}-${now.toISOString().split('T')[0]}.pdf`;
            doc.save(filename);
        }

        function downloadAllRegistrationsPDF() {
            const {
                jsPDF
            } = window.jspdf;

            // Get all visible registration data
            let registrations = [];
            const registrationCards = document.querySelectorAll('.card.border-0.shadow-sm.mb-4');

            registrationCards.forEach(card => {
                if (card.style.display !== 'none') { // Only include visible cards
                    const dl = card.querySelectorAll('dl.row.mb-0');
                    const leftData = dl[0].querySelectorAll('dd');
                    const rightData = dl[1].querySelectorAll('dd');

                    registrations.push({
                        reg_number: card.querySelector('.fw-semibold').textContent,
                        name: leftData[1].textContent,
                        program: rightData[3].textContent,
                        email: leftData[4].textContent,
                        phone: rightData[0].textContent,
                        reg_date: rightData[4].textContent
                    });
                }
            });

            if (registrations.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Data',
                    text: 'No registration data found to export.',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            // Create PDF
            const doc = new jsPDF('p', 'mm', 'a4');
            const now = new Date();

            // Add title
            doc.setFontSize(16);
            doc.text('REGISTRATION LIST REPORT', 105, 15, {
                align: 'center'
            });

            // Add generation info
            doc.setFontSize(10);
            doc.text(`Generated on: ${now.toLocaleDateString()} ${now.toLocaleTimeString()}`, 105, 22, {
                align: 'center'
            });
            doc.text(`Total Registrations: ${registrations.length}`, 105, 28, {
                align: 'center'
            });

            // Prepare table data
            const headers = [
                ['Reg Number', 'Name', 'Program', 'Email', 'Phone', 'Reg Date']
            ];
            const data = registrations.map(reg => [
                reg.reg_number,
                reg.name,
                reg.program,
                reg.email,
                reg.phone,
                reg.reg_date
            ]);

            // Add table
            doc.autoTable({
                head: headers,
                body: data,
                startY: 35,
                styles: {
                    fontSize: 8
                },
                headStyles: {
                    fillColor: [13, 110, 253],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                columnStyles: {
                    0: {
                        cellWidth: 25
                    },
                    1: {
                        cellWidth: 35
                    },
                    2: {
                        cellWidth: 25
                    },
                    3: {
                        cellWidth: 40
                    },
                    4: {
                        cellWidth: 25
                    },
                    5: {
                        cellWidth: 25
                    }
                },
                margin: {
                    left: 10,
                    right: 10
                },
                didDrawPage: function(data) {
                    // Add page numbers
                    doc.setFontSize(8);
                    doc.text(
                        'Page ' + doc.internal.getNumberOfPages(),
                        data.settings.margin.left,
                        doc.internal.pageSize.height - 10
                    );
                }
            });

            // Save PDF
            const filename = `all-registrations-${now.toISOString().split('T')[0]}.pdf`;
            doc.save(filename);
        }

        // Delete Registration Function
        function deleteRegistration(regNumber) {
            Swal.fire({
                title: 'Confirm Deletion',
                text: `Are you sure you want to delete registration ${regNumber}? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the registration.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send delete request
                    fetch('delete_registration.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `reg_number=${encodeURIComponent(regNumber)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message,
                                    confirmButtonColor: '#0d6efd'
                                }).then(() => {
                                    // Remove the card from DOM
                                    const card = document.querySelector(`[data-reg-number="${regNumber}"]`).closest('.card.border-0.shadow-sm.mb-4');
                                    if (card) {
                                        card.remove();
                                    }
                                    // Reload page to refresh data
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                    confirmButtonColor: '#0d6efd'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Request Failed',
                                text: 'Error: ' + error,
                                confirmButtonColor: '#0d6efd'
                            });
                        });
                }
            });
        }

        // Filter and Search Functions for Registration Section
        function filterRegistrations() {
            const searchTerm = document.getElementById('searchBar').value.toLowerCase().trim();
            const programFilter = document.getElementById('filterProgram').value;
            const dateFilter = document.getElementById('filterDate').value;

            const registrationCards = document.querySelectorAll('.card.border-0.shadow-sm.mb-4');
            const noResultsElement = document.getElementById('noRegistrationResults');

            let visibleCount = 0;

            registrationCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                const name = card.querySelectorAll('dd')[1].textContent.toLowerCase();
                const regNumber = card.getAttribute('data-reg-number').toLowerCase();
                const program = card.getAttribute('data-program');
                const regDate = card.getAttribute('data-reg-date');

                // Check search term
                const searchMatch = !searchTerm ||
                    cardText.includes(searchTerm) ||
                    regNumber.includes(searchTerm) ||
                    name.includes(searchTerm);

                // Check program filter
                const programMatch = programFilter === 'all' ||
                    (programFilter === 'shs' && program.includes('shs')) ||
                    (programFilter === 'college' && program.includes('college'));

                // Check date filter
                const dateMatch = !dateFilter || regDate.includes(dateFilter);

                // Show/hide card based on filters
                if (searchMatch && programMatch && dateMatch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0 && (searchTerm || programFilter !== 'all' || dateFilter)) {
                noResultsElement.classList.remove('d-none');
            } else {
                noResultsElement.classList.add('d-none');
            }

            // Update download button state
            const downloadAllBtn = document.getElementById('downloadAllPdf');
            if (downloadAllBtn) {
                downloadAllBtn.disabled = visibleCount === 0;
                if (visibleCount === 0) {
                    downloadAllBtn.title = 'No registrations to download';
                } else {
                    downloadAllBtn.title = `Download ${visibleCount} registration(s)`;
                }
            }
        }

        function clearRegistrationFilters() {
            document.getElementById('searchBar').value = '';
            document.getElementById('filterProgram').value = 'all';
            document.getElementById('filterDate').value = '';
            filterRegistrations();
        }

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
                }, {
                    label: 'Registrars',
                    data: [<?php echo $activeRegistrars ?>],
                    backgroundColor: 'rgba(242, 255, 0, 0.51)',
                    borderColor: 'rgba(155, 159, 34, 1)',
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
            const authForm = document.getElementById('authForm');
            const authModal = document.getElementById('authModal');
            const viewLogsBtn = document.getElementById('view-all-activity-btn');

            document.getElementById('searchBar').addEventListener('input', filterRegistrations);
            document.getElementById('filterProgram').addEventListener('change', filterRegistrations);
            document.getElementById('filterDate').addEventListener('change', filterRegistrations);

            filterRegistrations();

            // Add clear filters functionality (optional)
            document.getElementById('searchBar').addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    filterRegistrations();
                }
            });

            // Single registration download
            document.addEventListener('click', function(e) {
                if (e.target.closest('.download-single-btn')) {
                    const regNumber = e.target.closest('.download-single-btn').getAttribute('data-reg-number');
                    downloadRegistrationPDF(regNumber);
                }
            });

            // Download all registrations
            document.getElementById('downloadAllPdf').addEventListener('click', function() {
                downloadRegistrationPDF();
            });

            // Delete registration
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-btn')) {
                    const regNumber = e.target.closest('.delete-btn').getAttribute('data-reg-number');
                    deleteRegistration(regNumber);
                }
            });

            // Function to switch authentication methods
            window.switchAuthMethod = function(method) {
                document.getElementById('password-tab').classList.toggle('active', method === 'password');
                document.getElementById('pin-tab').classList.toggle('active', method === 'pin');
                document.getElementById('authPassword').classList.toggle('d-none', method !== 'password');
                document.getElementById('authPassword').classList.toggle('d-block', method === 'password');
                document.getElementById('authPIN').classList.toggle('d-none', method !== 'pin');
                document.getElementById('authPIN').classList.toggle('d-block', method === 'pin');
                document.getElementById('authMethod').value = method;
                document.getElementById('authKey').value = '';
                document.querySelector('input[name="authPIN"]').value = '';
            };

            if (viewLogsBtn) {
                viewLogsBtn.addEventListener('click', function() {
                    currentRestoreData = {
                        type: 'logs'
                    };
                    const modal = new bootstrap.Modal(authModal);
                    modal.show();
                    switchAuthMethod('password');
                });
            }

            // Authentication form submission
            authForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = document.getElementById('btnAuth');
                const currentMethod = document.getElementById('authMethod').value;

                // Validate form
                if (currentMethod === 'password' && !document.getElementById('authKey').value.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please enter your Authentication Key',
                        confirmButtonColor: '#0d6efd'
                    });
                    return;
                }

                if (currentMethod === 'pin' && !document.querySelector('input[name="authPIN"]').value.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please enter your 6-digit PIN',
                        confirmButtonColor: '#0d6efd'
                    });
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Authenticating...';

                fetch('authentication.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close auth modal
                            const authModalInstance = bootstrap.Modal.getInstance(authModal);
                            authModalInstance.hide();
                            authForm.reset();

                            if (currentRestoreData.type === 'logs') {
                                setTimeout(() => {
                                    const logsModal = new bootstrap.Modal(document.getElementById("view-activitylogs-modal"));
                                    logsModal.show();
                                }, 300);
                            }
                        } else {
                            // Check if forced logout is required
                            if (data.force_logout) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Authentication Failed',
                                    text: data.message,
                                    allowOutsideClick: false,
                                    confirmButtonColor: '#d33'
                                }).then(() => {
                                    // Redirect to logout
                                    window.location.href = 'includes/logout.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Authentication Failed',
                                    text: data.message
                                });
                            }
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Request Failed',
                            text: 'Error: ' + error
                        });
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Authenticate';
                    });
            });



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

        function exportActivityLogsPDF() {
            const {
                jsPDF
            } = window.jspdf;
            // Create new PDF document
            const doc = new jsPDF('p', 'mm', 'a4');

            // Add title
            doc.setFontSize(16);
            doc.text('Activity Logs Report', 105, 15, {
                align: 'center'
            });

            // Add current date and time
            const now = new Date();
            const currentDateTime = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
            doc.setFontSize(10);
            doc.text('Generated on: ' + currentDateTime, 105, 22, {
                align: 'center'
            });

            // Table headers
            const headers = [
                ['Date/Time', 'User', 'Type', 'Action', 'Description', 'IP Address']
            ];

            // Get ONLY VISIBLE table rows (respects filters/search)
            const modal = document.getElementById('view-activitylogs-modal');
            const rows = modal.querySelectorAll('tbody tr:not(.no-results-message)');
            const data = [];

            rows.forEach(row => {
                // Only include rows that are visible (not hidden by filters)
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    const rowData = [
                        cells[0].textContent,
                        cells[1].textContent,
                        cells[2].textContent,
                        cells[3].textContent,
                        cells[4].textContent,
                        cells[5].textContent
                    ];
                    data.push(rowData);
                }
            });

            // Check if there's data to export
            if (data.length === 0) {
                alert('No data to export! Please check your filters.');
                return;
            }

            // AutoTable configuration
            doc.autoTable({
                head: headers,
                body: data,
                startY: 30,
                styles: {
                    fontSize: 8
                },
                headStyles: {
                    fillColor: [13, 110, 253]
                },
                columnStyles: {
                    0: {
                        cellWidth: 25
                    },
                    1: {
                        cellWidth: 25
                    },
                    2: {
                        cellWidth: 15
                    },
                    3: {
                        cellWidth: 20
                    },
                    4: {
                        cellWidth: 50
                    },
                    5: {
                        cellWidth: 20
                    }
                },
                margin: {
                    left: 10,
                    right: 10
                },
                didDrawPage: function(data) {
                    // Add page numbers
                    doc.setFontSize(8);
                    doc.text(
                        'Page ' + doc.internal.getNumberOfPages(),
                        data.settings.margin.left,
                        doc.internal.pageSize.height - 10
                    );
                }
            });

            // Save the PDF
            doc.save('activity-logs-' + now.toISOString().split('T')[0] + '.pdf');
        }

        // Add event listener to the export button
        document.getElementById('exportPdf').addEventListener('click', exportActivityLogsPDF);
    </script>
</body>

</html>