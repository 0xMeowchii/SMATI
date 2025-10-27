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
        <div class="page-header row">
            <div class="col-10">
                <h4><i class="fas fa-user me-2"></i>My Grades</h4>
            </div>
            <div class="col-2">
                <a class="link-underline link-underline-opacity-0" href="student-concern-form.php?id=<?php echo $_SESSION['id'] ?>">
                    <i class="fas fa-edit me-1"></i>Submit Concern
                </a>
            </div>
        </div>

        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Grades</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search S.Y. & Semester...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>School Year & Semester</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM schoolyear ORDER BY schoolyear_id DESC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['schoolyear'] . ", " . $row['semester'] . " Semester" . "</td>";
                                echo "<td>
                                        <a class='btn btn-sm btn-outline-primary'
                                         href='student-grades-list.php?sy=" . $row['schoolyear_id'] . "'>
                                        <i class='fas fa-eye me-2'></i>View
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


    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>