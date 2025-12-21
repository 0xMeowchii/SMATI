<?php
include '../database.php';
include '../includes/activity_logger.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
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
    <?php
    include('includes/sidebar.php');

    if (!($_SESSION['username'] === 'admin')) {
        exit;
    }


    //UPDATE QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
        $conn = connectToDB();
        $admin_id = $_POST['editId'];
        $email = $_POST['editEmail'];
        $password = $_POST['editPassword'];

        // Check if password field has value
        $hasPassword = !empty($_POST['editPassword']);
        if ($hasPassword) {
            $password = password_hash($_POST['editPassword'], PASSWORD_DEFAULT);
        }

        if ($conn) {

            $checkStmt = $conn->prepare("SELECT admin_id FROM admin WHERE email = ? AND admin_id != ?");
            $checkStmt->bind_param("si", $email, $admin_id);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Admin already exists!',
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

                        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_ADMIN', "Updated admin account: $email");

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

                        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_ADMIN', "Updated admin account: $email");

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

    //DROP QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDrop'])) {
        $conn = connectToDB();
        $admin_id = $_POST['adminId'];
        $email = $_POST['dropEmail'];

        if ($conn) {
            $stmt = $conn->prepare("UPDATE admin SET status = '0' WHERE admin_id=?");
            $stmt->bind_param("i", $admin_id);

            if ($stmt->execute()) {

                logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DROP_ADMIN', "Drop admin Account: $email");

                echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Admin Drop Successfully!',
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

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-user me-2"></i>Admin Management</h4>
        </div>

        <!-- Student Table -->
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All School Head/Guidance</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search admin...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive flex-grow-1 overflow-auto" style="max-height:600px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>UserType</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM admin 
                                WHERE status = '1' AND (username = 'schoolhead' OR username = 'guidance')
                                ORDER BY admin_id DESC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["username"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>";
                                echo "<a class='btn btn-sm btn-outline-primary me-1 view-admin-btn'
                                        data-id='" . $row["admin_id"] . "'
                                        data-email='" . $row["email"] . "'
                                        data-username='" . $row["username"] . "'
                                        data-createdAt='" . (new DateTime($row['createdAt']))->format('m-d-Y h:i A') . "'
                                        data-bs-toggle='modal' 
                                        data-bs-target='#viewAdminModal'>
                                            <i class='fas fa-eye'></i>
                                        </a>";

                                if (isset($_SESSION['username']) && $_SESSION['username'] == 'admin') {
                                    echo "<a class='btn btn-sm btn-outline-secondary me-1 edit-admin-btn'
                                        data-id='" . $row["admin_id"] . "'
                                        data-email='" . $row["email"] . "'
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editAdminModal'>
                                            <i class='fas fa-edit'></i>
                                        </a>";

                                    echo "<a class='btn btn-sm btn-outline-danger me-1 drop-admin-btn'
                                            data-id='" . $row["admin_id"] . "'
                                            data-email='" . $row["email"] . "'>
                                                <i class='fas fa-trash'></i>
                                            </a>";
                                } else {
                                    echo "<a class='btn btn-sm btn-outline-secondary me-1 disabled' >
                                                <i class='fas fa-edit'></i>
                                            </a>";
                                    echo "<a class='btn btn-sm btn-outline-danger me-1 disabled' >
                                            <i class='fas fa-trash'></i>
                                        </a>";
                                }

                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<td colspan='5' class='text-center py-4' style='color: #6c757d;'>";
                            echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                            echo "<br>";
                            echo "No students found.";
                            echo "</td>";
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Admin Modal -->
        <div class="modal fade" id="editAdminModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editAdminModal">Edit Admin</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" id="editForm">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3 mb-3">
                                <h4 class="pb-2 border-bottom">Admin Information</h4>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter email" name="editEmail" id="editEmail" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Enter new password" name="editPassword" id="editPassword">
                                        <span class="input-group-text password-toggle" id="editPasswordToggle" title="Show Password"
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
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- View Admin Modal -->
        <div class="modal fade" id="viewAdminModal" tabindex="-1" aria-labelledby="viewAdminModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="viewAdminModal">Admin Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="border-bottom pb-2 mb-3 mt-4">User Account</h5>
                                <input type="hidden" name="viewId" id="viewId">
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>User Type:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p id="viewUserType"></p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Email:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p id="viewEmail"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Created At:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p id="viewCreatedAt"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drop Admin Modal -->
        <div class="modal fade" id="dropAdminModal" tabindex="-1" role="dialog" aria-labelledby="dropAdminModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dropAdminModal">Confirm Drop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to drop this admin?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="adminId" id="adminId">
                            <input type="hidden" name="dropEmail" id="dropEmail">
                            <button type="submit" class="btn btn-danger" name="btnDrop">Yes</button>
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
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.view-admin-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('viewId').value = btn.getAttribute('data-id');
                document.getElementById('viewEmail').textContent = btn.getAttribute('data-email');
                document.getElementById('viewUserType').textContent = btn.getAttribute('data-username');
                document.getElementById('viewCreatedAt').textContent = btn.getAttribute('data-createdAt');
            });
        });

        document.querySelectorAll('.edit-admin-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editEmail').value = btn.getAttribute('data-email');
            });
        });

        document.querySelectorAll('.drop-admin-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('adminId').value = btn.getAttribute('data-id');
                document.getElementById('dropEmail').value = btn.getAttribute('data-email');
            });
        });
    </script>
</body>

</html>