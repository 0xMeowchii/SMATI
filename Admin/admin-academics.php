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
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-chart-bar me-2"></i>Academics Management</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-subject-btn" data-bs-toggle="modal" data-bs-target="#add-subjects-modal">
                    <i class="fas fa-plus me-1"></i>Add Subject
                </button>
            </div>
        </div>

        <?php

        //INSERT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
            $conn = connectToDB();
            $subjectname = $_POST['subjectname'];
            $course = $_POST['course'];
            $teacher_id = $_POST['teacher'];
            $yearlevel = $_POST['yearlevel'];
            $schoolyear_id = $_POST['schoolyear'];

            if ($conn) {
                $stmt = $conn->prepare("INSERT INTO subjects (subject, course, teacher_id, yearlevel, schoolyear_id) 
                                            VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisi", $subjectname, $course, $teacher_id, $yearlevel, $schoolyear_id);

                if ($stmt->execute()) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Subject Created Successfully!',
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

        <!-- Student Table -->
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Subjects</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search Subjects...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Teacher</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>School Year & Semester</th>
                        <th>Action</th>
                    </tr>
                <tbody>
                    <?php
                    $conn = connectToDB();
                    $sql = "SELECT * 
                                FROM teachers t
                                INNER JOIN subjects s
                                INNER JOIN schoolyear
                                ON t.teacher_id = s.subject_id";
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
                                <a class='btn btn-sm btn-outline-primary me-1 view-student-btn'
                                
                                data-bs-toggle='modal' 
                                data-bs-target='#viewStudentModal'>
                                    <i class='fas fa-eye'></i>
                                </a>

                                <a class='btn btn-sm btn-outline-secondary me-1 edit-student-btn'
                                
                                data-bs-toggle='modal' 
                                data-bs-target='#editStudentModal'>
                                    <i class='fas fa-edit'></i>
                                </a>

                                <a class='btn btn-sm btn-outline-danger me-1 drop-student-btn'
                                
                                data-bs-toggle='modal' 
                                 data-bs-target='#dropStudentModal'>
                                    <i class='fas fa-trash'></i>
                                </a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "0 results";
                    }
                    ?>
                </tbody>
                </thead>
            </table>
        </div>

        <!-- Add Subject Modal -->
        <div class="modal fade" id="add-subjects-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Add New Subject</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" id="subjectname" name="subjectname" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Course</label>
                                    <select class="form-select" id="course" name="course" required>
                                        <option value="">Select Course</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSHM">BSHM</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Assign a Teacher</label>
                                    <select class="form-select" id="teacher" name="teacher" required>
                                        <option value="">Select Teacher</option>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM teachers WHERE status = '1'";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                $fullname = $row['lastname'] . ", " . $row['firstname'];
                                                echo "<option value='" . $row['teacher_id'] . "'>" . $fullname . "</option>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Year Level</label>
                                    <div class="d-flex gap-3">
                                        <input type="radio" class="btn-check" name="yearlevel" id="1st-outlined" value="1st">
                                        <label class="btn btn-outline-success" for="1st-outlined">1st Year</label>
                                        <input type="radio" class="btn-check" name="yearlevel" id="2nd-outlined" value="2nd">
                                        <label class="btn btn-outline-success" for="2nd-outlined">2nd Year</label>
                                        <input type="radio" class="btn-check" name="yearlevel" id="3rd-outlined" value="3rd">
                                        <label class="btn btn-outline-success" for="3rd-outlined">3rd Year</label>
                                        <input type="radio" class="btn-check" name="yearlevel" id="4th-outlined" value="4th">
                                        <label class="btn btn-outline-success" for="4th-outlined">4th Year</label>
                                    </div>
                                </div>
                                <div>
                                    <label for="student-course" class="form-label">School Year & Semester</label>
                                    <select class="form-select" id="schoolyear" name="schoolyear" required>
                                        <option value="">Select School Year & Semester</option>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM schoolyear";
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
                                <button type="submit" class="btn btn-primary" name="btnAdd">Save Subject</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>