<?php 
include 'includes/session.php';
include('../database.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include 'includes/header.php' ?>
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-user me-2"></i>My Grades</h4>
        </div>

        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Grades</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Subjects...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Prelim</th>
                        <th>Midterm</th>
                        <th>Finals</th>
                        <th>Average</th>
                        <th>Equivalent</th>
                        <th>Remarks</th>
                        <th>Comment</th>
                    </thead>
                    <tbody>
                          <?php
                            $conn = connectToDB();
                            $sql = "SELECT * 
                                    FROM grades g
                                    INNER JOIN teachers t ON g.teacher_id = t.teacher_id
                                    INNER JOIN subjects s ON g.subject_id = s.subject_id
                                    WHERE g.student_id = ? AND g.schoolyear_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ii", $_SESSION['id'],$_GET['sy']);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" .$row['subject']."</td>";
                                echo "<td>" .$row["lastname"] . ", " . $row["firstname"] . "</td>";
                                echo "<td>" .$row['prelim']."</td>";
                                echo "<td>" .$row['midterm']."</td>";
                                echo "<td>" .$row['finals']."</td>";
                                echo "<td>" .$row['average']."</td>";
                                echo "<td>" .$row['equivalent']."</td>";
                                echo "<td>" .$row['remarks']."</td>";
                                echo "<td>" .$row['comment']."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>