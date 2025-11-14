<?php
include('../database.php');
include '../includes/activity_logger.php';
include 'grade-approval-requests.php';
include 'set-grade-deadline.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
        .request-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: white;
            transition: box-shadow 0.2s;
        }

        .request-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .request-card.pending {
            border-left: 4px solid #ffc107;
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .teacher-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .teacher-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .request-meta {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .reason-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 3px solid #0d6efd;
        }

        .stats-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            background: #e7f3ff;
            color: #0d6efd;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 20px;
        }

        /* Add to your existing styles */
        .no-results-message {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .no-results-message i {
            font-size: 3rem;
            opacity: 0.3;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-chart-bar me-2"></i>Academics Management</h4>
        </div>

        <?php

        //INSERT SY QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
            $conn = connectToDB();
            $schoolyear = $_POST['schoolyear'];
            $semester = $_POST['semester'];
            $status = '1';

            if ($conn) {

                $checkStmt = $conn->prepare("SELECT * FROM schoolyear WHERE schoolyear = ? AND semester = ?");
                $checkStmt->bind_param("ss", $schoolyear, $semester);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'S.Y. & Semester already exists!',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
                } else {

                    $stmt = $conn->prepare("INSERT INTO schoolyear (schoolyear, semester, status) VALUES (?, ? , ?)");
                    $stmt->bind_param("sss", $schoolyear, $semester, $status);

                    if ($stmt->execute()) {

                        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'CREATE_SCHOOLYEAR', "created new Schoolyear & Semester: $schoolyear, $semester Semester");

                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'S.Y. Added Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            });
                        </script>";
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '" . addslashes($stmt->error) . "',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
                    }

                    $stmt->close();
                }

                $checkStmt->close();
            } else {
                echo "<script>alert('Database connection failed');</script>";
            }
            $conn->close();
        }

        //UPDATE SY QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdate'])) {
            $conn = connectToDB();
            $sy_id = $_POST['editSyId'];
            $schoolyear = $_POST['editSchoolyear'];
            $semester = $_POST['editSemester'];

            if ($conn) {

                $checkStmt = $conn->prepare("SELECT * FROM schoolyear WHERE schoolyear = ? AND semester = ?");
                $checkStmt->bind_param("ss", $schoolyear, $semester);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'S.Y. & Semester already exists!',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
                } else {
                    $stmt = $conn->prepare("UPDATE schoolyear 
                                        SET schoolyear = ?, 
                                            semester = ?
                                        WHERE schoolyear_id = ?");
                    $stmt->bind_param("ssi", $schoolyear, $semester, $sy_id);

                    if ($stmt->execute()) {

                        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_SCHOOLYEAR', "Updated Schoolyear Details: $schoolyear, $semester Semester");

                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Schoolyear & Semester Updated Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            });
                        </script>";
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '" . addslashes($stmt->error) . "',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
                    }
                    $stmt->close();
                }

                $checkStmt->close();
            } else {
                echo "<script>alert('Database connection failed');</script>";
            }
            $conn->close();
        }

        //DELETE SY QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
            $conn = connectToDB();
            $schoolyear_id = $_POST['id'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE schoolyear SET status = '0' WHERE schoolyear_id=?");
                $stmt->bind_param("i", $schoolyear_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DROP_SCHOOLYEAR', "drop a schoolyear & semester.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'S.Y. Drop Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            });
                        </script>";
                } else {
                    echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '" . addslashes($stmt->error) . "',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
                }

                $stmt->close();
                $conn->close();
            } else {
                echo "<script>alert('Database connection failed');</script>";
            }
        }

        //UPDATE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
            $conn = connectToDB();
            $subject_id = $_POST['editId'];
            $subjectname = $_POST['editName'];
            $subjectcode = $_POST['editCode'];
            $yearlevel = $_POST['editYearlevel'];
            $schoolyear_id = $_POST['editSchoolyear'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE subjects 
                                        SET subject_code=?, 
                                            subject=?,
                                            course=?,
                                            yearlevel=?,
                                            schoolyear_id=?
                                        WHERE subject_id=?");
                $stmt->bind_param("ssssii", $subjectcode, $subjectname, $course, $yearlevel, $schoolyear_id, $subject_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_SUBJECT', "Updated Subject Details: $subjectname");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Subject Updated Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            });
                        </script>";
                } else {
                    echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '" . addslashes($stmt->error) . "',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
                }
                $stmt->close();
                $conn->close();
            }
        }

        //DROP QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDrop'])) {
            $conn = connectToDB();
            $subject_id = $_POST['subjectId'];
            $subjectname = $_POST['dropSubjectname'];
            $schoolyear = $_POST['dropSchoolyear'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE subjects SET status = '0' WHERE subject_id=?");
                $stmt->bind_param("i", $subject_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DROP_SUBJECT', "Drop Subject: $subjectname, $schoolyear");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Subject Drop Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            });
                        </script>";
                } else {
                    echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '" . addslashes($stmt->error) . "',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
                }
                $stmt->close();
                $conn->close();
            } else {
                echo "<script>alert('Database connection failed');</script>";
            }
        }
        ?>

        <!-- Academics Table -->
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Subjects</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Subjects...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive flex-grow-1 overflow-auto" style="max-height:500px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Teacher</th>
                            <th>Year Level</th>
                            <th>School Year & Semester</th>
                            <th>Action</th>
                        </tr>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * 
                            FROM subjects s
                            INNER JOIN teachers t ON s.teacher_id = t.teacher_id
                            INNER JOIN schoolyear sy ON sy.schoolyear_id = s.schoolyear_id
                            WHERE s.status='1' ORDER BY sy.schoolyear DESC, s.subject ASC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['subject_code'] . " - " . $row["subject"] . "</td>";
                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                echo "<td>" . $row["yearlevel"] . "</td>";
                                echo "<td>" . $row["schoolyear"] . ", " . $row["semester"] . " Semester" . "</td>";
                                echo "<td>
                                <a class='btn btn-sm btn-outline-primary me-1 view-subject-btn'
                                data-name='" . $row['subject'] . "'
                                data-code='" . $row['subject_code'] . "'
                                data-teacher='" . $row['lastname'] . ", " . $row['firstname'] . "'
                                data-yearlevel='" . $row['yearlevel'] . "'
                                data-schoolyear='" . $row['schoolyear'] . ", " . $row['semester'] . " Semester" . "'
                                data-createdAt='" . (new DateTime($row['subject_created']))->format('m-d-Y h:i A') . "'
                                data-bs-toggle='modal' 
                                data-bs-target='#viewSubjectModal'>
                                    <i class='fas fa-eye'></i>
                                </a>

                                <a class='btn btn-sm btn-outline-secondary me-1 edit-subject-btn'
                                data-id='" . $row['subject_id'] . "'
                                data-name='" . $row['subject'] . "'
                                data-code='" . $row['subject_code'] . "'
                                data-yearlevel='" . $row['yearlevel'] . "'
                                data-schoolyear='" . $row['schoolyear_id'] . "'
                                data-bs-toggle='modal' 
                                data-bs-target='#editSubjectModal'>
                                    <i class='fas fa-edit'></i>
                                </a>

                                <a class='btn btn-sm btn-outline-danger me-1 drop-subject-btn'
                                data-id='" . $row['subject_id'] . "'
                                data-name='" . $row['subject'] . "'
                                data-schoolyear='" . $row['schoolyear'] . ", " . $row['semester'] . " Semester" . "'
                                data-bs-toggle='modal' 
                                 data-bs-target='#dropSubjectModal'>
                                    <i class='fas fa-trash'></i>
                                </a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                            echo "<i class='fas fa-search mb-2' style='font-size: 3rem; opacity: 0.5;'></i>";
                            echo "<br>";
                            echo "No Subjects found.";
                            echo "</td>";
                        }
                        ?>
                    </tbody>
                    </thead>
                </table>
            </div>
        </div>

        <div class="col mt-5">
            <div class="page-header">
                <h5><i class="fa fa-check-circle me-2"></i>Grade Submission Approvals</h5>
                <div class="col-12 col-md-6">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="searchSubmission" placeholder="Search Submissions...">
                        <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                    </div>
                </div>
            </div>
            <div class="container flex-grow-1 overflow-auto" style="max-height:500px;">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="approvalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-black" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            <i class="fas fa-clock me-2"></i>Pending Requests
                            <?php if (count($pending_requests) > 0): ?>
                                <span class="badge bg-warning"><?php echo count($pending_requests); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-black" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                            <i class="fas fa-history me-2"></i>History
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="approvalTabsContent">
                    <!-- Pending Requests Tab -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        <?php if (count($pending_requests) > 0): ?>
                            <?php foreach ($pending_requests as $request): ?>
                                <div class="request-card pending" data-request-id="<?php echo $request['request_id']; ?>">
                                    <div class="request-header">
                                        <div class="teacher-info">
                                            <div class="teacher-avatar">
                                                <?php echo strtoupper(substr($request['teacher_firstname'], 0, 1) . substr($request['teacher_lastname'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">
                                                    <?php echo htmlspecialchars($request['teacher_firstname'] . ' ' . $request['teacher_lastname']); ?>
                                                </h5>
                                                <div class="request-meta">
                                                    <i class="fas fa-book me-1"></i>
                                                    <?php echo htmlspecialchars($request['subject_code'] . ' - ' . $request['subject']); ?>
                                                    <span class="mx-2">•</span>
                                                    <?php echo htmlspecialchars($request['student_set']); ?>
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo htmlspecialchars($request['schoolyear'] . ', ' . $request['semester'] . ' Semester'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <span class="stats-badge">
                                            <i class="fas fa-upload"></i>
                                            Current Submissions: <?php echo $request['submission_count']; ?>/3
                                        </span>
                                    </div>

                                    <div class="reason-box">
                                        <strong><i class="fas fa-comment-dots me-2"></i>Teacher's Reason:</strong>
                                        <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($request['reason'])); ?></p>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Requested <?php echo date('M d, Y h:i A', strtotime($request['created_at'])); ?>
                                        </small>
                                        <div class="action-buttons">
                                            <button class="btn btn-success btn-sm approve-btn"
                                                data-request-id="<?php echo $request['request_id']; ?>"
                                                data-teacher-name="<?php echo htmlspecialchars($request['teacher_firstname'] . ' ' . $request['teacher_lastname']); ?>">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm reject-btn"
                                                data-request-id="<?php echo $request['request_id']; ?>"
                                                data-teacher-name="<?php echo htmlspecialchars($request['teacher_firstname'] . ' ' . $request['teacher_lastname']); ?>">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <h5>No Pending Requests</h5>
                                <p>All grade submission approval requests have been processed.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- History Tab -->
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <?php if (count($history) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Teacher</th>
                                            <th>Subject</th>
                                            <th>School Year</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Processed By</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($history as $item): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($item['teacher_firstname'] . ' ' . $item['teacher_lastname']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['subject'] . ' - ' . $item['student_set']); ?></td>
                                                <td><?php echo htmlspecialchars($item['schoolyear'] . ', ' . $item['semester'] . ' Sem'); ?></td>
                                                <td>
                                                    <small><?php echo htmlspecialchars(substr($item['reason'], 0, 50)) . (strlen($item['reason']) > 50 ? '...' : ''); ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($item['status'] == 'approved'): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Approved
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>Rejected
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>ADMIN
                                                </td>
                                                <td>
                                                    <small><?php echo date('M d, Y h:i A', strtotime($item['updated_at'])); ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                <h5>No History Yet</h5>
                                <p>Processed approval requests will appear here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- subject deadline -->
        <div class="col mt-5">
            <div class="page-header">
                <h5><i class="fa fa-calendar-alt me-2"></i>Set Grade Submission Deadlines</h5>
                <div class="col-12 col-md-6">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="searchDeadlines" placeholder="Search Subjects...">
                        <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                    </div>
                </div>
            </div>

            <div class="container flex-grow-1 overflow-auto" style="max-height:500px;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>School Year</th>
                                <th>Current Deadline</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tblDeadline">
                            <?php if (!empty($subjects)): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['teacher_firstname'] . ' ' . $subject['teacher_lastname']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['schoolyear'] . ', ' . $subject['semester'] . ' Sem'); ?></td>
                                        <td>
                                            <?php
                                            if ($subject['submission_due_date']) {
                                                echo date('M d, Y h:i A', strtotime($subject['submission_due_date']));
                                            } else {
                                                echo '<em class="text-muted">Not set</em>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm set-deadline-btn"
                                                data-subject-id="<?php echo $subject['subject_id']; ?>"
                                                data-sy-id="<?php echo $subject['schoolyear_id']; ?>"
                                                data-subject-name="<?php echo htmlspecialchars($subject['subject']); ?>"
                                                data-current-deadline="<?php echo $subject['submission_due_date'] ?? ''; ?>">
                                                <i class="fas fa-calendar-plus me-1"></i>
                                                <?php echo $subject['submission_due_date'] ? 'Update' : 'Set'; ?> Deadline
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="fas fa-book-open mb-3" style="font-size: 3rem; color: #6c757d; opacity: 0.5;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">No Subjects Found</h5>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Schoolyear and Sem -->
        <div class="col mt-5 ">
            <div class="page-header">
                <h4><i class="fas fa-cog me-2"></i>School Year & Semester</h4>
                <div class="action-buttons">
                    <button class="btn btn-primary" id="add-schoolyear-btn" data-bs-toggle="modal" data-bs-target="#add-schoolyear-modal">
                        <i class="fas fa-plus me-1"></i>Add S.Y. & Sem
                    </button>
                </div>
            </div>
            <div class="table-responsive flex-grow-1 overflow-auto" style="max-height: 300px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM schoolyear WHERE status = '1' ORDER BY schoolyear_id DESC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["schoolyear"] . "</td>";
                                echo "<td>" . $row["semester"] . "</td>";
                                echo "<td>

                                                <a class='btn btn-sm btn-outline-secondary me-1 edit-schoolyear-btn'
                                                data-id='" . $row["schoolyear_id"] . "'
                                                data-schoolyear='" . $row["schoolyear"] . "'
                                                data-sem='" . $row["semester"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#edit-schoolyear-modal'>
                                                    <i class='fa fa-edit'></i>
                                                </a>

                                                <a class='btn btn-sm btn-outline-danger me-1 delete-schoolyear-btn'
                                                data-id='" . $row["schoolyear_id"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#deleteSchoolyearModal'>
                                                    <i class='fa fa-trash'></i>
                                                </a>

                                                  </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                            echo "<i class='fas fa-search mb-2' style='font-size: 3rem; opacity: 0.5;'></i>";
                            echo "<br>";
                            echo "School hasn't started yet.";
                            echo "</td>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add SchoolYear Modal -->
        <div class="modal fade" id="add-schoolyear-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add School Year</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">School Year</label>
                                    <select class="form-select" name="schoolyear" id="schoolyear" required>
                                        <option value="">Select School Year</option>
                                        <option value="2025-2026">2025-2026</option>
                                        <option value="2026-2027">2026-2027</option>
                                        <option value="2027-2028">2027-2028</option>
                                        <option value="2028-2029">2028-2029</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Semester</label>
                                    <select class="form-select" name="semester" id="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="1st">1st</option>
                                        <option value="2nd">2nd</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnAdd">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit schoolyear sem -->
        <div class="modal fade" id="edit-schoolyear-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit School Year</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <input type="hidden" name="editSyId" id="editSyId">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">School Year</label>
                                    <select class="form-select" name="editSchoolyear" id="editSchoolyear" required>
                                        <option value="">Select School Year</option>
                                        <option value="2025-2026">2025-2026</option>
                                        <option value="2026-2027">2026-2027</option>
                                        <option value="2027-2028">2027-2028</option>
                                        <option value="2028-2029">2028-2029</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Semester</label>
                                    <select class="form-select" name="editSemester" id="editSemester" required>
                                        <option value="">Select Semester</option>
                                        <option value="1st">1st</option>
                                        <option value="2nd">2nd</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnUpdate">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Schoolyear Modal -->
        <div class="modal fade" id="deleteSchoolyearModal" tabindex="-1" role="dialog" aria-labelledby="deleteSchoolyearModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteSchoolyearModal">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this SchoolYear?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="id" id="id">
                            <button type="submit" class="btn btn-danger" name="btnDelete">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Subject Modal -->
        <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editSubjectModal">Edit Subject</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Subject Code</label>
                                    <input type="text" class="form-control" id="editCode" name="editCode" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" id="editName" name="editName" required>
                                </div>
                                <div>
                                    <label class="form-label">Year Level</label>
                                    <div class="d-flex gap-3">
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit1st-outlined" value="1st">
                                        <label class="btn btn-outline-success" for="edit1st-outlined">1st Year</label>
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit2nd-outlined" value="2nd">
                                        <label class="btn btn-outline-success" for="edit2nd-outlined">2nd Year</label>
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit3rd-outlined" value="3rd">
                                        <label class="btn btn-outline-success" for="edit3rd-outlined">3rd Year</label>
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit4th-outlined" value="4th">
                                        <label class="btn btn-outline-success" for="edit4th-outlined">4th Year</label>
                                    </div>
                                </div>
                                <div>
                                    <label for="student-course" class="form-label">School Year & Semester</label>
                                    <select class="form-select" id="editSchoolyear" name="editSchoolyear" required>
                                        <option value="">Select School Year & Semester</option>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM schoolyear WHERE status = '1' ORDER BY schoolyear_id DESC";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                $schoolyear = $row['schoolyear'] . ", " . $row['semester'] . ' Semester';
                                                echo "<option value='" . $row['schoolyear_id'] . "'>" . $schoolyear . "</option>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnEdit">Update Subject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Subject Modal -->
        <div class="modal fade" id="viewSubjectModal" tabindex="-1" aria-labelledby="viewSubjectModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="viewSubjectModal">Subject Details</h3>
                    </div>
                    <div class="modal-body">
                        <?php
                        echo "<p><strong>Subject Name: </strong><span id='modalCode'></span> - <span id='modalSubjectName'></span></p>
                              <p><strong>Teacher: </strong><span id='modalSubjectTeacher'></span></p>
                              <p><strong>Year Level: </strong><span id='modalSubjectYearlevel'></span></p>
                              <p><strong>School Year & Semester: </strong><span id='modalSubjectSchoolyear'></span></p>
                              <p class='pt-3 border-top'><strong>CreatedAt: </strong><span id='createdAt'></span></p>";
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drop Subject Modal -->
        <div class="modal fade" id="dropSubjectModal" tabindex="-1" role="dialog" aria-labelledby="dropSubjectModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dropSubjectModal">Confirm Drop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to drop this Subject?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="subjectId" id="subjectId">
                            <input type="hidden" name="dropSubjectname" id="dropSubjectname">
                            <input type="hidden" name="dropSchoolyear" id="dropSchoolyear">
                            <button type="submit" class="btn btn-danger" name="btnDrop">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_SESSION['deadline_set_success']) && $_SESSION['deadline_set_success']): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: 'Deadline set successfully!',
                    icon: 'success',
                    confirmButtonColor: '#0d6efd',
                    timer: 2000
                });
            });
        </script>
        <?php unset($_SESSION['deadline_set_success']); ?>
    <?php endif; ?>
    <script>
        document.querySelectorAll('.set-deadline-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const subjectId = this.dataset.subjectId;
                const syId = this.dataset.syId;
                const subjectName = this.dataset.subjectName;
                const currentDeadline = this.dataset.currentDeadline;

                // Format current deadline for datetime-local input
                let defaultValue = '';
                if (currentDeadline) {
                    const date = new Date(currentDeadline);
                    defaultValue = date.toISOString().slice(0, 16);
                }

                Swal.fire({
                    title: 'Set Deadline',
                    html: `
                        <p class="mb-3">Set submission deadline for:<br><strong>${subjectName}</strong></p>
                        <input type="datetime-local" id="deadline-input" class="form-control" value="${defaultValue}">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Set Deadline',
                    confirmButtonColor: '#0d6efd',
                    preConfirm: () => {
                        const deadline = document.getElementById('deadline-input').value;
                        if (!deadline) {
                            Swal.showValidationMessage('Please select a date and time');
                        }
                        return deadline;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('set_deadline', '1');
                        formData.append('subject_id', subjectId);
                        formData.append('schoolyear_id', syId);
                        formData.append('due_date', result.value);

                        // Submit form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        for (let [key, value] of formData.entries()) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = value;
                            form.appendChild(input);
                        }
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });


        // Approve request
        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const requestId = this.dataset.requestId;
                const teacherName = this.dataset.teacherName;

                Swal.fire({
                    title: 'Approve Request?',
                    html: `Grant <strong>${teacherName}</strong> permission to submit grades again?<br><br>
                           <small class="text-muted">This will reset their submission count to 0, giving them 3 new submissions.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processRequest(requestId, 'approve', teacherName);
                    }
                });
            });
        });

        // Reject request
        document.querySelectorAll('.reject-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const requestId = this.dataset.requestId;
                const teacherName = this.dataset.teacherName;

                Swal.fire({
                    title: 'Reject Request?',
                    html: `Deny <strong>${teacherName}</strong>'s request for additional submissions?<br><br>
                           <small class="text-muted">The teacher will remain locked at 2 submissions.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processRequest(requestId, 'reject', teacherName);
                    }
                });
            });
        });

        function processRequest(requestId, action, teacherName) {
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('request_id', requestId);
            formData.append('action', action);

            fetch('api/process_approval.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: action === 'approve' ? 'Approved!' : 'Rejected!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            // Remove the card from view
                            const card = document.querySelector(`[data-request-id="${requestId}"]`);
                            if (card) {
                                card.style.transition = 'all 0.3s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'translateX(-20px)';
                                setTimeout(() => {
                                    card.remove();

                                    // Check if no more pending requests
                                    const remainingCards = document.querySelectorAll('.request-card.pending');
                                    if (remainingCards.length === 0) {
                                        document.getElementById('pending').innerHTML = `
                                        <div class="empty-state">
                                            <i class="fas fa-check-circle"></i>
                                            <h5>No Pending Requests</h5>
                                            <p>All grade submission approval requests have been processed.</p>
                                        </div>
                                    `;
                                    }

                                    // Update badge count
                                    const badge = document.querySelector('#pending-tab .badge');
                                    if (badge) {
                                        const currentCount = parseInt(badge.textContent);
                                        if (currentCount <= 1) {
                                            badge.remove();
                                        } else {
                                            badge.textContent = currentCount - 1;
                                        }
                                    }
                                    window.location.reload();
                                }, 300);
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to process request. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        // Apply to all inputs except those containing specific words in ID
        document.querySelectorAll('input[type="text"]').forEach(input => {
            const excludePatterns = ['username', 'email', 'editUsername', 'editEmail'];
            const shouldExclude = excludePatterns.some(pattern => input.id.includes(pattern));

            if (!shouldExclude) {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        });
        document.querySelectorAll('.view-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('modalSubjectName').textContent = btn.getAttribute('data-name');
                document.getElementById('modalCode').textContent = btn.getAttribute('data-code');
                document.getElementById('modalSubjectTeacher').textContent = btn.getAttribute('data-teacher');
                document.getElementById('modalSubjectYearlevel').textContent = btn.getAttribute('data-yearlevel');
                document.getElementById('modalSubjectSchoolyear').textContent = btn.getAttribute('data-schoolyear');
                document.getElementById('createdAt').textContent = btn.getAttribute('data-createdAt');
            });
        });

        document.querySelectorAll('.edit-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editName').value = btn.getAttribute('data-name');
                document.getElementById('editCode').value = btn.getAttribute('data-code');

                // Fixed: Properly set radio button value
                const yearLevel = btn.getAttribute('data-yearlevel');
                const radioButton = document.querySelector(`input[name="editYearlevel"][value="${yearLevel}"]`);
                if (radioButton) {
                    radioButton.checked = true;
                }

                document.getElementById('editSchoolyear').value = btn.getAttribute('data-schoolyear');
            });
        });

        document.querySelectorAll('.drop-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('dropSubjectname').value = btn.getAttribute('data-name');
                document.getElementById('subjectId').value = btn.getAttribute('data-id');
                document.getElementById('dropSchoolyear').value = btn.getAttribute('data-schoolyear');
            });
        });

        document.querySelectorAll('.edit-schoolyear-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editSyId').value = btn.getAttribute('data-id');
                document.getElementById('editSchoolyear').value = btn.getAttribute('data-schoolyear');
                document.getElementById('editSemester').value = btn.getAttribute('data-sem');
            });
        });

        document.querySelectorAll('.delete-schoolyear-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('id').value = btn.getAttribute('data-id');
            });
        });

        // Search functionality for Subjects table
        document.addEventListener('DOMContentLoaded', function() {
            // ===== SUBJECTS SEARCH =====
            const searchInput = document.getElementById('searchInput');
            const subjectsTableBody = document.querySelector('.table-responsive table tbody');
            const subjectsTableRows = Array.from(subjectsTableBody.querySelectorAll('tr'));

            function performSubjectsSearch(searchTerm) {
                const query = searchTerm.toLowerCase().trim();
                let visibleRows = 0;

                subjectsTableRows.forEach(function(row) {
                    const cells = row.querySelectorAll('td');
                    let rowText = '';

                    for (let i = 0; i < cells.length - 1; i++) {
                        rowText += cells[i].textContent.toLowerCase() + ' ';
                    }

                    if (query === '' || rowText.includes(query)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                showNoResultsMessage(visibleRows === 0 && query !== '', 'subjects');
            }

            function showNoResultsMessage(show, type) {
                let noResultsRow = document.getElementById(`no-results-${type}`);
                let tableBody;

                switch (type) {
                    case 'subjects':
                        tableBody = subjectsTableBody;
                        break;
                    case 'submissions':
                        tableBody = document.querySelector('#pending table tbody') || document.querySelector('#history table tbody');
                        break;
                    case 'deadlines':
                        tableBody = document.getElementById('tblDeadline');
                        break;
                }

                if (!tableBody) return;

                if (show && !noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = `no-results-${type}`;
                    noResultsRow.innerHTML = `
                <td colspan="7" class="text-center py-4" style="color: #6c757d;">
                    <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                    <br>
                    No ${type} found matching your search
                </td>
            `;
                    tableBody.appendChild(noResultsRow);
                } else if (!show && noResultsRow) {
                    noResultsRow.remove();
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    performSubjectsSearch(e.target.value);
                });

                searchInput.addEventListener('paste', function(e) {
                    setTimeout(function() {
                        performSubjectsSearch(searchInput.value);
                    }, 10);
                });
            }

            // ===== GRADE SUBMISSION APPROVALS SEARCH =====
            const searchSubmission = document.getElementById('searchSubmission');

            function performSubmissionSearch(searchTerm) {
                const query = searchTerm.toLowerCase().trim();

                // Search in Pending Requests tab
                const pendingCards = document.querySelectorAll('#pending .request-card');
                let pendingVisible = 0;

                pendingCards.forEach(card => {
                    const cardText = card.textContent.toLowerCase();
                    if (query === '' || cardText.includes(query)) {
                        card.style.display = 'block';
                        pendingVisible++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Search in History tab table
                const historyRows = document.querySelectorAll('#history table tbody tr');
                let historyVisible = 0;

                historyRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    if (query === '' || rowText.includes(query)) {
                        row.style.display = '';
                        historyVisible++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show no results messages
                const pendingContainer = document.getElementById('pending');
                const historyContainer = document.getElementById('history');

                showNoResultsInContainer(pendingVisible === 0 && query !== '', pendingContainer, 'pending submissions');
                showNoResultsInContainer(historyVisible === 0 && query !== '', historyContainer, 'history records');
            }

            function showNoResultsInContainer(show, container, type) {
                if (!container) return;

                let noResultsDiv = container.querySelector('.no-results-message');

                if (show && !noResultsDiv) {
                    noResultsDiv = document.createElement('div');
                    noResultsDiv.className = 'empty-state no-results-message';
                    noResultsDiv.innerHTML = `
                <i class="fas fa-search"></i>
                <h5>No ${type} found</h5>
                <p>No ${type} match your search criteria</p>
            `;
                    container.appendChild(noResultsDiv);
                } else if (!show && noResultsDiv) {
                    noResultsDiv.remove();
                }
            }

            if (searchSubmission) {
                searchSubmission.addEventListener('input', function(e) {
                    performSubmissionSearch(e.target.value);
                });

                searchSubmission.addEventListener('paste', function(e) {
                    setTimeout(function() {
                        performSubmissionSearch(searchSubmission.value);
                    }, 10);
                });
            }

            // ===== DEADLINES SEARCH =====
            const searchDeadlines = document.getElementById('searchDeadlines');
            const deadlinesTableBody = document.getElementById('tblDeadline');
            const deadlinesTableRows = deadlinesTableBody ? Array.from(deadlinesTableBody.querySelectorAll('tr')) : [];

            function performDeadlinesSearch(searchTerm) {
                const query = searchTerm.toLowerCase().trim();
                let visibleRows = 0;

                deadlinesTableRows.forEach(function(row) {
                    const cells = row.querySelectorAll('td');
                    let rowText = '';

                    for (let i = 0; i < cells.length - 1; i++) {
                        rowText += cells[i].textContent.toLowerCase() + ' ';
                    }

                    if (query === '' || rowText.includes(query)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                showNoResultsMessage(visibleRows === 0 && query !== '', 'deadlines');
            }

            if (searchDeadlines && deadlinesTableBody) {
                searchDeadlines.addEventListener('input', function(e) {
                    performDeadlinesSearch(e.target.value);
                });

                searchDeadlines.addEventListener('paste', function(e) {
                    setTimeout(function() {
                        performDeadlinesSearch(searchDeadlines.value);
                    }, 10);
                });
            }

            // Optional: Add search icon click functionality for all search inputs
            const searchIcons = document.querySelectorAll('.input-group-text');
            searchIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    if (input) {
                        input.focus();
                    }
                });
            });
        });
    </script>
</body>

</html>