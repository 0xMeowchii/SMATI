<?php include('../database.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../Admin/admin.css">
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
                            <input type="text" class="form-control" value="EduExam v1.2.0" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Last Backup</label>
                            <input type="text" class="form-control" id="last-backup" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card border-0 custom-card mb-4">
                <div class="card-header px-4 py-3 bg-secondary text-white">
                    <h5 class="card-title mb-0">Archives</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-black" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" aria-selected="true">
                                <i class="bi bi-people me-1"></i>Students
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-black" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" aria-selected="false">
                                <i class="bi bi-box me-1"></i>Teachers
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-black" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab" aria-selected="false">
                                <i class="bi bi-box me-1"></i>Subjects
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="students" role="tabpanel" aria-labelledby="students-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">StudentID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Course</th>
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

                        <div class="tab-pane fade" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Subject Name</th>
                                            <th scope="col">Teachers</th>
                                            <th scope="col">Course</th>
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
                                                echo "<td>" . $row["course"] . "</td>";
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
        <div class="col mt-3">
            <div class="page-header">
                <h4><i class="fas fa-cog me-2"></i>School Year & Semester</h4>
                <div class="action-buttons">
                    <button class="btn btn-primary" id="add-schoolyear-btn" data-bs-toggle="modal" data-bs-target="#add-schoolyear-modal">
                        <i class="fas fa-plus me-1"></i>Add S.Y. & Sem
                    </button>
                </div>
            </div>
            <div class="table-responsive">
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
    </script>
</body>

</html>