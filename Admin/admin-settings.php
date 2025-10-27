<?php
require_once 'includes/session.php';
include('../database.php');
include '../includes/activity_logger.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
        /* Custom styles for what Bootstrap doesn't provide */
        .custom-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .custom-card .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 12px 12px 0 0 !important;
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
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-cog me-2"></i>System Settings</h4>
        </div>

        <!--- query -->
        <?php

        //INSERT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
            $conn = connectToDB();
            $schoolyear = $_POST['schoolyear'];
            $semester = $_POST['semester'];

            if ($conn) {
                $stmt = $conn->prepare("INSERT INTO schoolyear (schoolyear, semester) VALUES (?, ?)");
                $stmt->bind_param("ss", $schoolyear, $semester);

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
                $conn->close();
            } else {
                echo "<script>alert('Database connection failed');</script>";
            }
        }
        //DELETE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
            $conn = connectToDB();
            $schoolyear_id = $_POST['id'];

            if ($conn) {
                $stmt = $conn->prepare("DELETE FROM schoolyear WHERE schoolyear_id=?");
                $stmt->bind_param("i", $schoolyear_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DELETE_SCHOOLYEAR', "deleted schoolyear & semester.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'S.Y. Deleted Successfully!',
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

        //RESTORE STUDENT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRestore'])) {
            $conn = connectToDB();
            $student_id = $_POST['studentId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE students SET status = '1' WHERE student_id=?");
                $stmt->bind_param("i", $student_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'RETRIEVE_ACCOUNT', "retrieved student account: Student ID = $student_id");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Student Restored Successfully!',
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

        //RESTORE TEACHER QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRestore1'])) {
            $conn = connectToDB();
            $teacher_id = $_POST['teacherId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE teachers SET status = '1' WHERE teacher_id=?");
                $stmt->bind_param("i", $teacher_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'RETRIEVE_ACCOUNT', "retrieved teacher account: Teacher ID = $teacher_id");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Teacher Restored Successfully!',
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

        //RESTORE SUBJECT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRestore2'])) {
            $conn = connectToDB();
            $subject_id = $_POST['subjectId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE subjects SET status = '1' WHERE subject_id=?");
                $stmt->bind_param("i", $subject_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'RETRIEVE_SUBJECT', "retrieved subject from the archive.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Subject Restored Successfully!',
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

        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 custom-card mb-4">
                    <div class="card-header px-4 py-3 bg-primary text-white">
                        <h5 class="card-title mb-0">Database Management</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label">Backup Data</label>
                            <p class="card-text text-muted small mb-2">Create a complete backup of all system data</p>
                            <button class="btn btn-outline-primary w-100" id="backup-btn">
                                <i class="fas fa-database me-2"></i>Create Backup
                            </button>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Restore Data</label>
                            <p class="text-muted small mb-2">Restore system from a previous backup</p>
                            <input type="file" class="form-control mb-2" id="restore-file">
                            <button class="btn btn-outline-secondary w-100" id="restore-btn">
                                <i class="fas fa-upload me-2"></i>Restore Backup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 custom-card mb-4">
                    <div class="card-header px-4 py-3 bg-success text-white">
                        <h5 class="card-title mb-0">System Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label">System Version</label>
                            <input type="text" class="form-control" value="SMATI - EduPortal v1.0.0" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Last Backup</label>
                            <input type="text" class="form-control" id="last-backup" readonly
                                title="Click for more details" style="cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--- Archives -->
        <div class="col">
            <div class="card border-0 custom-card mb-4">
                <div class="card-header px-4 py-3 bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-archive me-1"></i>Archives
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Nav tabs -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active text-black" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" aria-selected="true">
                                        <i class="bi bi-person me-1"></i>Students
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-black" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" aria-selected="false">
                                        <i class="bi bi-people me-1"></i>Teachers
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-black" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab" aria-selected="false">
                                        <i class="bi bi-journal-bookmark"></i>Subjects
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                                <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                            </div>
                        </div>
                    </div>


                    <div class="tab-content flex-grow-1 overflow-auto" id="myTabContent" style="max-height: 300px;">

                        <!-- Students Tab -->
                        <div class="tab-pane fade show active" id="students" role="tabpanel" aria-labelledby="students-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">StudentID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Set</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM students WHERE status = '0'";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["student_id"] . "</td>";
                                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                                echo "<td>" . $row["course"] . "</td>";
                                                echo "<td>" . $row["email"] . "</td>";
                                                echo "<td>
                                                <a class='btn btn-sm btn-outline-success me-1 restore-student-btn'
                                                data-id='" . $row["student_id"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#restoreStudentModal'>
                                                    <i class='fa fa-refresh'></i>
                                                </a>
                                                  </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                                            echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                                            echo "<br>";
                                            echo "No students found matching your search";
                                            echo "</td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Teachers Tab -->
                        <div class="tab-pane fade" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">TeacherID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Department</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM teachers WHERE status = '0'";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["teacher_id"] . "</td>";
                                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                                echo "<td>" . $row["department"] . "</td>";
                                                echo "<td>" . $row["email"] . "</td>";
                                                echo "<td>
                                                <a class='btn btn-sm btn-outline-success me-1 restore-teacher-btn'
                                                data-id='" . $row["teacher_id"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#restoreTeacherModal'>
                                                    <i class='fa fa-refresh'></i>
                                                </a>
                                                  </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                                            echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                                            echo "<br>";
                                            echo "No Teacher found matching your search";
                                            echo "</td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <!-- Subjects Tab -->
                        <div class="tab-pane fade" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Subject Name</th>
                                            <th scope="col">Teachers</th>
                                            <th scope="col">Year Level</th>
                                            <th scope="col">School Year & Semester</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * 
                                                FROM subjects s
                                                INNER JOIN teachers t ON s.teacher_id = t.teacher_id
                                                INNER JOIN schoolyear sy ON sy.schoolyear_id = s.schoolyear_id
                                                WHERE s.status='0'";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["subject"] . "</td>";
                                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                                echo "<td>" . $row["yearlevel"] . "</td>";
                                                echo "<td>" . $row["schoolyear"] . ", " . $row["semester"] . " Semester" . "</td>";
                                                echo "<td>
                                                <a class='btn btn-sm btn-outline-success me-1 restore-subject-btn'
                                                data-id='" . $row["subject_id"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#restoreSubjectModal'>
                                                    <i class='fa fa-refresh'></i>
                                                </a>
                                                  </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                                            echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                                            echo "<br>";
                                            echo "No subject found matching your search";
                                            echo "</td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- schoolyear & semester -->
        <div class="col mt-3 ">
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
                        $sql = "SELECT * FROM schoolyear";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["schoolyear"] . "</td>";
                                echo "<td>" . $row["semester"] . "</td>";
                                echo "<td>
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
                            echo "0 results";
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
                                <h4 class="pb-2 border-bottom">School Year</h4>
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

        <!-- Restore Student Modal -->
        <div class="modal fade" id="restoreStudentModal" tabindex="-1" role="dialog" aria-labelledby="restoreStudentModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="restoreStudentModal">Confirm Restore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Restore this Student?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="studentId" id="studentId">
                            <button type="submit" class="btn btn-success" name="btnRestore">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore Teacher Modal -->
        <div class="modal fade" id="restoreTeacherModal" tabindex="-1" role="dialog" aria-labelledby="restoreTeacherModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="restoreTeacherModal">Confirm Restore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Restore this Teacher?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="teacherId" id="teacherId">
                            <button type="submit" class="btn btn-success" name="btnRestore1">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore Subject Modal -->
        <div class="modal fade" id="restoreSubjectModal" tabindex="-1" role="dialog" aria-labelledby="restoreSubjectModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="restoreSubjectModal">Confirm Restore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Restore this Subject?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="subjectId" id="subjectId">
                            <button type="submit" class="btn btn-success" name="btnRestore2">Yes</button>
                        </form>
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
                            <p class="text-muted">Chooses your authentication method to proceed.</p>
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
        </form>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.restore-student-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('studentId').value = btn.getAttribute('data-id');
            });
        });
        document.querySelectorAll('.restore-teacher-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('teacherId').value = btn.getAttribute('data-id');
            });
        });
        document.querySelectorAll('.restore-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('subjectId').value = btn.getAttribute('data-id');
            });
        });
        document.querySelectorAll('.delete-schoolyear-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('id').value = btn.getAttribute('data-id');
            });
        });

        function searchTables(searchTerm) {
            // Get all table rows from all three tables
            const studentRows = document.querySelectorAll('#students tbody tr');
            const teacherRows = document.querySelectorAll('#teachers tbody tr');
            const subjectRows = document.querySelectorAll('#subjects tbody tr');

            // Convert search term to lowercase for case-insensitive search
            const searchText = searchTerm.toLowerCase().trim();

            // Function to search within a table
            function searchTable(rows, searchText) {
                let hasVisibleRows = false;

                rows.forEach(row => {
                    // Skip if this is the "no results" row
                    if (row.querySelector('td[colspan]')) {
                        row.style.display = 'none';
                        return;
                    }

                    let rowText = '';
                    // Get all text content from the row (excluding action buttons)
                    const cells = row.querySelectorAll('td:not(:last-child)');
                    cells.forEach(cell => {
                        rowText += ' ' + cell.textContent.toLowerCase();
                    });

                    // Show or hide row based on search match
                    if (searchText === '' || rowText.includes(searchText)) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                return hasVisibleRows;
            }

            // Search each table
            const hasStudentResults = searchTable(studentRows, searchText);
            const hasTeacherResults = searchTable(teacherRows, searchText);
            const hasSubjectResults = searchTable(subjectRows, searchText);

            // Handle empty states for each table
            handleEmptyState('students', hasStudentResults, searchText);
            handleEmptyState('teachers', hasTeacherResults, searchText);
            handleEmptyState('subjects', hasSubjectResults, searchText);
        }

        // Function to handle empty state display
        function handleEmptyState(tableId, hasResults, searchText) {
            const tableBody = document.querySelector(`#${tableId} tbody`);
            const existingEmptyRow = tableBody.querySelector('tr.empty-search-row');

            // Remove existing empty row if it exists
            if (existingEmptyRow) {
                existingEmptyRow.remove();
            }

            // If no results and there's a search term, show empty state
            if (!hasResults && searchText !== '') {
                const emptyRow = document.createElement('tr');
                emptyRow.className = 'empty-search-row';
                emptyRow.innerHTML = `
            <td colspan="6" class="text-center py-4" style="color: #6c757d;">
                <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                <br>
                No ${getTableName(tableId)} found matching "${searchText}"
            </td>
        `;
                tableBody.appendChild(emptyRow);
            }
        }

        // Helper function to get table name for empty state message
        function getTableName(tableId) {
            const tableNames = {
                'students': 'students',
                'teachers': 'teachers',
                'subjects': 'subjects'
            };
            return tableNames[tableId] || 'records';
        }

        // Debounce function for better performance
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Event listener for search input with debouncing
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');

            if (searchInput) {
                // Debounced search (300ms delay)
                const debouncedSearch = debounce(function(value) {
                    searchTables(value);
                }, 300);

                // Search on input with debouncing
                searchInput.addEventListener('input', function() {
                    debouncedSearch(this.value);
                });

                // Also search on Enter key (immediately)
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchTables(this.value);
                    }
                });

                // Clear search when Escape key is pressed
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        searchTables('');
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const backupBtn = document.getElementById('backup-btn');
            const restoreBtn = document.getElementById('restore-btn');
            const authForm = document.getElementById('authForm');
            const authMethod = document.getElementById('authMethod');
            const pinInput = document.querySelector('input[name="authPIN"]');

            let currentAction = null; // Track whether it's backup or restore

            // Prevent non-numeric input
            pinInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Also prevent paste of non-numeric content
            pinInput.addEventListener('paste', function(e) {
                const pasteData = e.clipboardData.getData('text');
                if (!/^\d+$/.test(pasteData)) {
                    e.preventDefault();
                }
            });


            // Function to switch authentication methods
            window.switchAuthMethod = function(method) {
                // Update tabs
                document.getElementById('password-tab').classList.toggle('active', method === 'password');
                document.getElementById('pin-tab').classList.toggle('active', method === 'pin');

                // Update form fields
                document.getElementById('authPassword').classList.toggle('d-none', method !== 'password');
                document.getElementById('authPassword').classList.toggle('d-block', method === 'password');
                document.getElementById('authPIN').classList.toggle('d-none', method !== 'pin');
                document.getElementById('authPIN').classList.toggle('d-block', method === 'pin');

                // Update hidden field
                document.getElementById('authMethod').value = method;

                // Clear fields when switching
                document.getElementById('authKey').value = '';
                document.querySelector('input[name="authPIN"]').value = '';
            };

            // Backup button click - show auth modal
            backupBtn.addEventListener('click', function() {
                currentAction = 'backup';
                const authModal = new bootstrap.Modal(document.getElementById('authModal'));
                authModal.show();

                // Reset to default method when modal opens
                switchAuthMethod('password');
            });

            // Restore button click - show auth modal
            restoreBtn.addEventListener('click', function() {
                const fileInput = document.getElementById('restore-file');

                if (!fileInput.files || fileInput.files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please select a backup file first',
                        confirmButtonColor: '#0d6efd'
                    });
                    return;
                }

                currentAction = 'restore';
                const authModal = new bootstrap.Modal(document.getElementById('authModal'));
                authModal.show();

                // Reset to default method when modal opens
                switchAuthMethod('password');
            });

            // Authentication form submission
            authForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = document.getElementById('btnAuth');

                // Validate form before submission
                const currentMethod = document.getElementById('authMethod').value;
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
                            const authModal = bootstrap.Modal.getInstance(document.getElementById('authModal'));
                            authModal.hide();
                            authForm.reset();

                            // Execute the appropriate action based on currentAction
                            if (currentAction === 'backup') {
                                showBackupConfirmation();
                            } else if (currentAction === 'restore') {
                                showRestoreConfirmation();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Authentication Failed',
                                text: data.message
                            });
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
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Authenticate';
                    });
            });

            function showBackupConfirmation() {
                Swal.fire({
                    title: 'Create Backup?',
                    text: 'Create a backup of the database? This may take a few moments.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, create backup',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        createBackup();
                    }
                });
            }

            function showRestoreConfirmation() {
                Swal.fire({
                    title: 'Restore Database?',
                    html: '<strong>WARNING:</strong> This will overwrite all current data with the backup file.<br><br>A safety backup will be created first.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, restore it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performRestore();
                    }
                });
            }

            function createBackup() {
                const loadingAlert = Swal.fire({
                    title: 'Creating Backup...',
                    text: 'Please wait while we create your database backup.',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch('backup.php', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                title: 'Backup Created!',
                                html: `
                    <div style="text-align: left;">
                        <p><strong>Status:</strong> ${data.success}</p>
                        ${data.filename ? `<p><strong>Filename:</strong> ${data.filename}</p>` : ''}
                        ${data.size ? `<p><strong>Size:</strong> ${data.size}</p>` : ''}
                    </div>
                `,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                if (typeof loadLastBackup === 'function') {
                                    loadLastBackup();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Backup Failed',
                                text: data.message || 'Unknown error occurred',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        Swal.fire({
                            title: 'Request Failed',
                            text: 'Error creating backup: ' + error,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    });
            }

            function performRestore() {
                const fileInput = document.getElementById('restore-file');
                const btn = document.getElementById('restore-btn');

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Restoring...';

                const formData = new FormData();
                formData.append('backup_file', fileInput.files[0]);

                fetch('restore.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                confirmButtonColor: '#0d6efd'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Restore Failed',
                                text: data.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-upload me-2"></i>Restore Backup';
                        fileInput.value = '';
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error restoring backup: ' + error,
                            confirmButtonColor: '#d33'
                        });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-upload me-2"></i>Restore Backup';
                    });
            }
        });

        // Load last backup info when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadLastBackup();
        });

        // Function to load last backup info
        function loadLastBackup() {
            fetch('get_last_backup.php', {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('last-backup').value = data.last_backup;
                    }
                })
                .catch(error => {
                    console.error('Error loading last backup:', error);
                    document.getElementById('last-backup').value = 'Error loading info';
                });
        }

        // Show backup details on click
        document.getElementById('last-backup').addEventListener('click', function() {
            fetch('get_last_backup.php', {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.details) {
                        Swal.fire({
                            title: 'Last Backup Details',
                            html: `
            <div style="text-align: left;">
                <p><strong>File:</strong> ${data.details.filename}</p>
                <p><strong>Date:</strong> ${data.details.date}</p>
                <p><strong>Time:</strong> ${data.details.time}</p>
                <p><strong>Size:</strong> ${data.details.size}</p>
                <p><strong>Created by:</strong> ${data.details.username}</p>
            </div>
        `,
                            icon: 'info',
                            confirmButtonText: 'OK'
                        });
                    }
                });
        });
    </script>
</body>

</html>