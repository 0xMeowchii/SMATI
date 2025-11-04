<?php
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
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-cog me-2"></i>System Settings</h4>
        </div>

        <!--- query -->
        <?php

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

        //RESTORE REGISTRAR QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRestore3'])) {
            $conn = connectToDB();
            $registrar_id = $_POST['registrarId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE registrars SET status = '1' WHERE registrar_id=?");
                $stmt->bind_param("i", $registrar_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'RETRIEVE_REGISTRAR', "retrieved registrar from the archive.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Registrar Restored Successfully!',
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

        //RESTORE SY QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRestore4'])) {
            $conn = connectToDB();
            $schoolyear_id = $_POST['syId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE schoolyear SET status = '1' WHERE schoolyear_id=?");
                $stmt->bind_param("i", $schoolyear_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'RETREIVE_SY', "retrieved schoolyear from the archive.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'S.Y. Restored Successfully!',
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

                    <div class="col-12">
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
                                <button class="nav-link text-black" id="registrars-tab" data-bs-toggle="tab" data-bs-target="#registrars" type="button" role="tab" aria-selected="false">
                                    <i class="bi bi-file-person"></i>Registrars
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-black" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab" aria-selected="false">
                                    <i class="bi bi-journal-bookmark"></i>Subjects
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-black" id="sy-tab" data-bs-toggle="tab" data-bs-target="#sy" type="button" role="tab" aria-selected="false">
                                    <i class="bi bi-gear"></i>SY & Semester
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
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
                                                data-id='" . $row["student_id"] . "'>
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
                                                data-id='" . $row["teacher_id"] . "'>
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
                                                data-id='" . $row["subject_id"] . "'>
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

                        <!-- Registrars Tab -->
                        <div class="tab-pane fade" id="registrars" role="tabpanel" aria-labelledby="registrars-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">RegistrarID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM registrars WHERE status = '0'";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["registrar_id"] . "</td>";
                                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                                echo "<td>
                                                <a class='btn btn-sm btn-outline-success me-1 restore-registrar-btn'
                                                data-id='" . $row["registrar_id"] . "'>
                                                    <i class='fa fa-refresh'></i>
                                                </a>
                                                  </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                                            echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                                            echo "<br>";
                                            echo "No registrar found matching your search";
                                            echo "</td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SY Tab -->
                        <div class="tab-pane fade" id="sy" role="tabpanel" aria-labelledby="sy-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">RegistrarID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM schoolyear WHERE status = '0'";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["schoolyear"] . "</td>";
                                                echo "<td>" . $row["semester"] . "</td>";
                                                echo "<td>
                                                    <a class='btn btn-sm btn-outline-success me-1 restore-sy-btn'
                                                    data-id='" . $row["schoolyear_id"] . "'>
                                                        <i class='fa fa-refresh'></i>
                                                    </a>
                                                  </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                                            echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                                            echo "<br>";
                                            echo "No school year found matching your search";
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

        <!-- Restore Registrar Modal -->
        <div class="modal fade" id="restoreRegistrarModal" tabindex="-1" role="dialog" aria-labelledby="restoreRegistrar" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="restoreSubjectModal">Confirm Restore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Restore this Registrar?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="registrarId" id="registrarId">
                            <button type="submit" class="btn btn-success" name="btnRestore3">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore schoolyear Modal -->
        <div class="modal fade" id="restoreSYmodal" tabindex="-1" role="dialog" aria-labelledby="restoreSYmodal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="restoreSYmodal">Confirm Restore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Restore this Schoolyear & Semester?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="syId" id="syId">
                            <button type="submit" class="btn btn-success" name="btnRestore4">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOWNLOAD BACKUP MODAL -->
        <div class="modal fade" id="downloadBackupModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Backup Created Successfully!
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-database text-success" style="font-size: 3rem;"></i>
                        </div>
                        <div class="backup-details">
                            <p><strong>Filename:</strong> <span id="backup-filename"></span></p>
                            <p><strong>Size:</strong> <span id="backup-size"></span></p>
                            <p><strong>Created:</strong> <span id="backup-date"></span></p>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Your backup has been saved to the server and is ready for download.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="downloadBackupBtn">
                            <i class="fas fa-download me-2"></i>Download Backup
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
        </form>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Search functionality for archives across all tabs
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    // Get all active tab content
                    const tabPanes = document.querySelectorAll('.tab-pane');

                    tabPanes.forEach(tabPane => {
                        if (tabPane.classList.contains('active')) {
                            searchInTable(tabPane, searchTerm);
                        }
                    });
                });

                // Also search when switching tabs
                const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
                tabButtons.forEach(tabButton => {
                    tabButton.addEventListener('shown.bs.tab', function() {
                        const searchTerm = searchInput.value.toLowerCase().trim();
                        const targetId = this.getAttribute('data-bs-target');
                        const targetPane = document.querySelector(targetId);

                        if (targetPane) {
                            searchInTable(targetPane, searchTerm);
                        }
                    });
                });
            }
        });

        function searchInTable(tabPane, searchTerm) {
            const table = tabPane.querySelector('table');
            if (!table) return;

            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr');
            let hasVisibleRows = false;

            rows.forEach(row => {
                // Skip the "no results" row if it exists
                if (row.querySelector('td[colspan]')) {
                    row.style.display = 'none';
                    return;
                }

                const cells = row.querySelectorAll('td');
                let rowContainsSearchTerm = false;

                cells.forEach(cell => {
                    const cellText = cell.textContent.toLowerCase();
                    if (cellText.includes(searchTerm)) {
                        rowContainsSearchTerm = true;

                        // Highlight the matching text
                        if (searchTerm && searchTerm.length > 0) {
                            highlightText(cell, searchTerm);
                        }
                    }
                });

                if (rowContainsSearchTerm || searchTerm === '') {
                    row.style.display = '';
                    hasVisibleRows = true;
                    // Remove highlights when showing all or no search
                    if (searchTerm === '') {
                        removeHighlights(row);
                    }
                } else {
                    row.style.display = 'none';
                    removeHighlights(row);
                }
            });

            // Show/hide "no results" message
            showNoResultsMessage(tbody, hasVisibleRows, searchTerm);
        }

        function highlightText(element, searchTerm) {
            // First remove any existing highlights
            removeHighlights(element);

            const text = element.textContent;
            const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
            const newText = text.replace(regex, '<mark class="bg-warning">$1</mark>');
            element.innerHTML = newText;
        }

        function removeHighlights(element) {
            const marks = element.querySelectorAll('mark');
            marks.forEach(mark => {
                const parent = mark.parentNode;
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize();
            });
        }

        function showNoResultsMessage(tbody, hasVisibleRows, searchTerm) {
            // Remove existing no results message
            const existingMessage = tbody.querySelector('.no-results-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Add new message if no results and search term is not empty
            if (!hasVisibleRows && searchTerm !== '') {
                const noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-message';
                noResultsRow.innerHTML = `
            <td colspan="100" class="text-center py-4" style="color: #6c757d;">
                <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                <br>
                No results found for "${searchTerm}"
            </td>
        `;
                tbody.appendChild(noResultsRow);
            }
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Clear search when changing tabs (optional)
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(tabButton => {
                tabButton.addEventListener('click', function() {
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        searchInput.value = '';
                        // Trigger search to reset all tables
                        searchInput.dispatchEvent(new Event('input'));
                    }
                });
            });
        });

        // Add this script to replace the existing restore button event listeners
        document.addEventListener('DOMContentLoaded', function() {
            let currentRestoreData = {
                type: null,
                id: null,
                modalId: null
            };

            // Authentication form elements
            const authForm = document.getElementById('authForm');
            const authModal = document.getElementById('authModal');
            const backupBtn = document.getElementById('backup-btn');
            const restoreBtn = document.getElementById('restore-btn');

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

            // Handle all restore button clicks - STUDENTS
            document.querySelectorAll('.restore-student-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    currentRestoreData = {
                        type: 'student',
                        id: btn.getAttribute('data-id'),
                        modalId: 'restoreStudentModal'
                    };

                    // Show authentication modal
                    const modal = new bootstrap.Modal(authModal);
                    modal.show();
                    switchAuthMethod('password');
                });
            });

            // Handle all restore button clicks - TEACHERS
            document.querySelectorAll('.restore-teacher-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    currentRestoreData = {
                        type: 'teacher',
                        id: btn.getAttribute('data-id'),
                        modalId: 'restoreTeacherModal'
                    };

                    const modal = new bootstrap.Modal(authModal);
                    modal.show();
                    switchAuthMethod('password');
                });
            });

            // Handle all restore button clicks - SUBJECTS
            document.querySelectorAll('.restore-subject-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    currentRestoreData = {
                        type: 'subject',
                        id: btn.getAttribute('data-id'),
                        modalId: 'restoreSubjectModal'
                    };

                    const modal = new bootstrap.Modal(authModal);
                    modal.show();
                    switchAuthMethod('password');
                });
            });

            // Handle all restore button clicks - REGISTRARS
            document.querySelectorAll('.restore-registrar-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    currentRestoreData = {
                        type: 'registrar',
                        id: btn.getAttribute('data-id'),
                        modalId: 'restoreRegistrarModal'
                    };

                    const modal = new bootstrap.Modal(authModal);
                    modal.show();
                    switchAuthMethod('password');
                });
            });

            // Handle all restore button clicks - SCHOOL YEAR
            document.querySelectorAll('.restore-sy-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    currentRestoreData = {
                        type: 'schoolyear',
                        id: btn.getAttribute('data-id'),
                        modalId: 'restoreSYmodal'
                    };

                    const modal = new bootstrap.Modal(authModal);
                    modal.show();
                    switchAuthMethod('password');
                });
            });

            // Keep backup functionality
            if (backupBtn) {
                backupBtn.addEventListener('click', function() {
                    currentRestoreData = {
                        type: 'backup'
                    };
                    const modal = new bootstrap.Modal(authModal);
                    modal.show();
                    switchAuthMethod('password');
                });
            }

            // Keep restore backup functionality
            if (restoreBtn) {
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
                    currentRestoreData = {
                        type: 'restore-backup'
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

                            // Handle different restore types
                            if (currentRestoreData.type === 'backup') {
                                showBackupConfirmation();
                            } else if (currentRestoreData.type === 'restore-backup') {
                                showRestoreConfirmation();
                            } else if (currentRestoreData.modalId) {
                                // Set the ID in the hidden input field
                                const idFieldMap = {
                                    'restoreStudentModal': 'studentId',
                                    'restoreTeacherModal': 'teacherId',
                                    'restoreSubjectModal': 'subjectId',
                                    'restoreRegistrarModal': 'registrarId',
                                    'restoreSYmodal': 'syId'
                                };

                                const fieldId = idFieldMap[currentRestoreData.modalId];
                                if (fieldId) {
                                    document.getElementById(fieldId).value = currentRestoreData.id;
                                }

                                // Show the restore confirmation modal
                                setTimeout(() => {
                                    const restoreModal = new bootstrap.Modal(document.getElementById(currentRestoreData.modalId));
                                    restoreModal.show();
                                }, 300);
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

                fetch('backup1.php', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            // Populate modal with backup details
                            document.getElementById('backup-filename').textContent = data.filename;
                            document.getElementById('backup-size').textContent = data.size;
                            document.getElementById('backup-date').textContent = new Date().toLocaleString();

                            // Store filename for download
                            document.getElementById('downloadBackupBtn').setAttribute('data-filename', data.filename);

                            // Show download modal
                            const downloadModal = new bootstrap.Modal(document.getElementById('downloadBackupModal'));
                            downloadModal.show();

                            // Update last backup info
                            if (typeof loadLastBackup === 'function') {
                                loadLastBackup();
                            }
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
                            icon: 'error'
                        });
                    });
            }

            // Handle download button click
            document.getElementById('downloadBackupBtn').addEventListener('click', function() {
                const filename = this.getAttribute('data-filename');
                if (filename) {
                    // Create temporary link and trigger download
                    const link = document.createElement('a');
                    link.href = 'download_backup.php?file=' + encodeURIComponent(filename);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Download Started',
                        text: 'Your backup file is being downloaded.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Close modal after a short delay
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('downloadBackupModal'));
                        if (modal) modal.hide();
                    }, 1500);
                }
            });

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
                            }).then(() => location.reload());
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