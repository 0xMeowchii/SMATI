<?php
require_once 'includes/session.php';
include '../database.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
</head>

<body>
    <!-- Sidebar -->
    <?php
    include('includes/sidebar.php');
    ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-users me-2"></i>Student</h4>
        </div>

        <!-- query -->
        <?php

        //UPDATE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
            $conn = connectToDB();
            $student_id = $_POST['editId'];
            $email = $_POST['editEmail'];
            $hasPassword = !empty($_POST['editPassword']);
            if ($hasPassword) {
                $password = password_hash($_POST['editPassword'], PASSWORD_DEFAULT);
            }


            if ($conn) {
                // Check for existing email and username (excluding current student)
                $checkStmt = $conn->prepare("SELECT student_id FROM students WHERE (email = ?) AND student_id != ?");
                $checkStmt->bind_param("si", $email, $student_id);
                $checkStmt->execute();
                $checkStmt->store_result();

                if ($checkStmt->num_rows > 0) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Email already exists!',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
                } else {

                    if ($hasPassword) {
                        $stmt = $conn->prepare("UPDATE students 
                                        SET email=?,
                                            password=?
                                        WHERE student_id=?");
                        $stmt->bind_param("ssi", $email, $password, $student_id);

                        if ($stmt->execute()) {

                            echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Student Updated Successfully!',
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
                    } else {
                        $stmt = $conn->prepare("UPDATE students 
                                        SET email=?
                                        WHERE student_id=?");
                        $stmt->bind_param("si", $email, $student_id);

                        if ($stmt->execute()) {

                            echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Student Updated Successfully!',
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
                }
                $checkStmt->close();
            }
            $conn->close();
        }

        ?>

        <!-- Student Table -->
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>Student Account</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>StudentID</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM students";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["student_id"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>
                                    <a class='btn btn-sm btn-outline-primary me-1 edit-student-btn'
                                    data-id='" . $row["student_id"] . "'
                                    data-email='" . $row["email"] . "'
                                    data-bs-toggle='modal' 
                                    data-bs-target='#edit-student-modal'>
                                         <i class='fas fa-edit me-1'></i>Edit
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

        <!-- Edit Admin Modal -->
        <div class="modal fade" id="edit-student-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Account Details</h3>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" id="editForm">
                            <div class="input-group mb-4">
                                <input type="hidden" id="editId" name="editId">
                                <span class="input-group-text fw-semibold">Email:</span>
                                <input type="email" class="form-control" placeholder="Enter email" name="editEmail" id="editEmail" required>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text fw-semibold">Password:</span>
                                <input type="password" class="form-control" placeholder="Enter new password" name="editPassword" id="editPassword">
                                <span class="input-group-text password-toggle" id="password-toggle"
                                    onmousedown="document.getElementById('editPassword').type='text'"
                                    onmouseup="document.getElementById('editPassword').type='password'"
                                    onmouseleave="document.getElementById('editPassword').type='password'">
                                    <i class="fas fa-eye"></i></span>
                            </div>
                            <div id="editError">

                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/script1.js"></script>
    <script>
        document.querySelectorAll('.edit-student-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editEmail').value = btn.getAttribute('data-email');
            });
        });
    </script>
</body>

</html>