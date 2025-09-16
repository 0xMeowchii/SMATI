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
    <link rel="stylesheet" href="../Teacher/teacher.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-user me-2"></i>My Subjects</h4>
        </div>

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

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>Subject Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>School Year & Semester</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                            $conn = connectToDB();
                            $sql = "SELECT * 
                                    FROM subjects s
                                    INNER JOIN teachers t ON s.teacher_id = t.teacher_id
                                    INNER JOIN schoolyear sy ON sy.schoolyear_id = s.schoolyear_id
                                    WHERE s.teacher_id = ? AND s.schoolyear_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ii", $_SESSION['id'], $_GET['sy']);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" .$row['subject']."</td>";
                                echo "<td>" .$row['course']."</td>";
                                echo "<td>" .$row['yearlevel']."</td>";
                                echo "<td>" . $row["schoolyear"] . ", " . $row["semester"] . " Semester" . "</td>";
                                echo "<td>
                                      <a class='btn btn-sm btn-outline-primary' 
                                      href='teacher-student-list.php?subject_id=" . urlencode($row['subject_id']) . 
                                      "&sy=". urlencode($row['schoolyear_id']) . "'>
                                          <i class='fas fa-eye me-2'></i>View
                                      </a>
                                    </td>";
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