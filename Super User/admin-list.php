<?php
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
            <h4><i class="fas fa-universal-access me-2"></i>Admin</h4>
        </div>

        <!-- query -->
        <?php

        //UPDATE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
            $conn = connectToDB();
            $admin_id = $_POST['editId'];
            $email = $_POST['editEmail'];
            $hasPassword = !empty($_POST['editPassword']);
            if ($hasPassword) {
                $password = password_hash($_POST['editPassword'], PASSWORD_DEFAULT);
            }

            if ($conn) {
                // Check for existing email and username (excluding current student)
                $checkStmt = $conn->prepare("SELECT admin_id FROM admin WHERE (email = ?) AND admin_id != ?");
                $checkStmt->bind_param("si", $email, $admin_id);
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
                        $stmt = $conn->prepare("UPDATE admin 
                                    SET email=?,
                                        password=?
                                    WHERE admin_id=?");
                        $stmt->bind_param("ssi", $email, $password, $admin_id);

                        if ($stmt->execute()) {
                            echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Admin Updated Successfully!',
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
                        $stmt = $conn->prepare("UPDATE admin 
                                    SET email=?
                                    WHERE admin_id=?");
                        $stmt->bind_param("si", $email, $admin_id);

                        if ($stmt->execute()) {
                            echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Admin Updated Successfully!',
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
                        <h5>Admin Account</h5>
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
                            <th>Username</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="flex-grow-1 overflow-auto" style="max-height: 400px;">
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM admin";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["username"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>
                                    <a class='btn btn-sm btn-outline-primary me-1 edit-admin-btn'
                                    data-id='" . $row["admin_id"] . "'
                                    data-email='" . $row["email"] . "'
                                    data-bs-toggle='modal' 
                                    data-bs-target='#edit-admin-modal'>
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
        <div class="modal fade" id="edit-admin-modal" tabindex="-1" aria-hidden="true">
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
                                <span class="input-group-text password-toggle" id="password-toggle" title="Show Password"
                                    onmousedown="document.getElementById('editPassword').type='text'"
                                    onmouseup="document.getElementById('editPassword').type='password'"
                                    onmouseleave="document.getElementById('editPassword').type='password'">
                                    <i class="fas fa-eye"></i></span>
                                <span class="input-group-text generate-password" style="cursor: pointer;" title="Generate Password">
                                    <i class="fas fa-key"></i></span>
                                <span class="input-group-text copy-password" style="cursor: pointer;" title="Copy to clipboard">
                                    <i class="fas fa-copy"></i>
                                </span>
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
    <script>
        document.querySelectorAll('.edit-admin-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editEmail').value = btn.getAttribute('data-email');
            });
        });
    </script>
</body>

</html>