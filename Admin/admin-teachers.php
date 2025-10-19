<?php
include '../database.php';
include '../includes/activity_logger.php';
?>
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
            <h4><i class="fas fa-users me-2"></i>Teachers Management</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-teachers-btn" data-bs-toggle="modal" data-bs-target="#add-teachers-modal">
                    <i class="fas fa-plus me-1"></i>Add Teacher
                </button>
            </div>
        </div>


        <!--- query -->
        <?php

        //INSERT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
            $conn = connectToDB();
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $department = $_POST['department'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $status = '1';

            if ($conn) {
                $stmt = $conn->prepare("INSERT INTO teachers (firstname, lastname, email, department, username, password, status) 
                                            VALUES (?, ?, ?, ?, ?, ?,?)");
                $stmt->bind_param("sssssss", $firstname, $lastname, $email, $department, $username, $password, $status);

                if ($stmt->execute()) {
                    $teachername = $lastname . ', ' . $firstname;
                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'CREATE_TEACHER', "Created teacher account: $teachername (Department: $department)");
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Teacher Added Successfully!',
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

        //UPDATE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
            $conn = connectToDB();
            $teacher_id = $_POST['editId'];
            $firstname = $_POST['editFname'];
            $lastname = $_POST['editLname'];
            $email = $_POST['editEmail'];
            $department = $_POST['editDepartment'];
            $username = $_POST['editUsername'];
            $password = $_POST['editPassword'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE teachers 
                                        SET firstname=?,
                                            lastname=?,
                                            email=?,
                                            department=?,
                                            username=?,
                                            password=?
                                        WHERE teacher_id=?");
                $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $department, $username, $password, $teacher_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_TEACHER', "Updated teacher account: Teacher ID = $teacher_id");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Teacher Updated Successfully!',
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
            }
        }

        //DROP QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDrop'])) {
            $conn = connectToDB();
            $teacher_id = $_POST['teacherId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE teachers SET status = '0' WHERE teacher_id=?");
                $stmt->bind_param("i", $teacher_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DROP_TEACHER', "Drop teacher account: Teacher ID = $teacher_id");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Teacher Drop Successfully!',
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
                        <h5>All Teachers</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search Teachers..." id="searchInput">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>TeacherID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                <tbody>

                    <?php
                    $conn = connectToDB();
                    $sql = "SELECT * FROM teachers WHERE status = '1'";
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
                                                <a class='btn btn-sm btn-outline-primary me-1 view-teacher-btn'
                                                data-id='" . $row["teacher_id"] . "'
                                                data-name='" . $row["lastname"] . ", " . $row["firstname"] . "'
                                                data-department='" . $row["department"] . "'
                                                data-email='" . $row["email"] . "'
                                                data-username='" . $row["username"] . "'
                                                data-createdAt='" . $row["createdAt"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#viewTeacherModal'>
                                                    <i class='fas fa-eye'></i>
                                                </a>

                                                <a class='btn btn-sm btn-outline-secondary me-1 edit-teacher-btn'
                                                data-id='" . $row["teacher_id"] . "'
                                                data-fname='" . $row["firstname"] . "'
                                                data-lname='" . $row["lastname"] . "'
                                                data-department='" . $row["department"] . "'
                                                data-email='" . $row["email"] . "'
                                                data-username='" . $row["username"] . "'
                                                data-password='" . $row["password"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#editTeacherModal'>
                                                    <i class='fas fa-edit'></i>
                                                </a>

                                                <a class='btn btn-sm btn-outline-danger me-1 drop-teacher-btn'
                                                data-id='" . $row["teacher_id"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#dropTeacherModal'>
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

        <!-- Add Teacher Modal -->
        <div class="modal fade" id="add-teachers-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="teacherModalTitle">Add New Teacher</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                            <input type="hidden" id="student-id">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Teacher Information</h4>
                                <div class="col-md-6">
                                    <label for="student-name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="student-email" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="student-id-number" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="student-course" class="form-label">Department</label>
                                    <select class="form-select" id="department" name="department" required>
                                        <option value="">Select Department</option>
                                        <option value="IT">IT Department</option>
                                        <option value="Faculty">Faculty Department</option>
                                    </select>
                                </div>
                                <h4 class="pb-2 border-bottom">User Account</h4>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter username" name="username">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Enter password" name="password" id="password">
                                        <span class="input-group-text" id="password-toggle"
                                            onmousedown="document.getElementById('password').type='text'"
                                            onmouseup="document.getElementById('password').type='password'"
                                            onmouseleave="document.getElementById('password').type='password'"><i class="fas fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="save-teacher-btn" name="btnAdd">Save Teacher</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Teacher Modal -->
        <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editTeacherModal">Edit Teacher</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Teacher Information</h4>
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="editFname" id="editFname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="editLname" id="editLname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="editEmail" id="editEmail" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <select class="form-select" name="editDepartment" id="editDepartment" required>
                                        <option value="">Select Department</option>
                                        <option value="IT">IT Department</option>
                                        <option value="Faculty">Faculty Department</option>
                                    </select>
                                </div>
                                <h4 class="pb-2 border-bottom">User Account</h4>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter username" name="editUsername" id="editUsername" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Enter password" name="editPassword" id="editPassword" required>
                                        <span class="input-group-text password-toggle" id="password-toggle"
                                            onmousedown="document.getElementById('editPassword').type='text'"
                                            onmouseup="document.getElementById('editPassword').type='password'"
                                            onmouseleave="document.getElementById('editPassword').type='password'">
                                            <i class="fas fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnEdit">Update Teacher</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Teacher Modal -->
        <div class="modal fade" id="viewTeacherModal" tabindex="-1" aria-labelledby="viewTeacherModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="viewTeacherModal">Teacher Details</h3>
                    </div>
                    <div class="modal-body">
                        <?php
                        echo "<p><strong>Teacher ID: </strong><span id='modalTeacherId'></span></p>
                              <p><strong>Name: </strong><span id='modalTeacherName'></span></p>
                              <p><strong>Department: </strong><span id='modalTeacherDepartment'></span></p>
                              <p><strong>Email: </strong><span id='modalTeacherEmail'></span></p>";
                        echo "<h3 class='pb-3 pt-3 border-bottom'>User Acccount</h3>
                              <p><strong>Username: </strong><span id='modalTeacherUsername'></span></p>
                              <p><strong>createdAt: </strong><span id='createdAt'></span></p>";

                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drop Teacher Modal -->
        <div class="modal fade" id="dropTeacherModal" tabindex="-1" role="dialog" aria-labelledby="dropTeacherModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dropTeacherModal">Confirm Drop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to drop this teacher?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="teacherId" id="teacherId">
                            <button type="submit" class="btn btn-danger" name="btnDrop">Yes</button>
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
        document.querySelectorAll('.view-teacher-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('modalTeacherId').textContent = btn.getAttribute('data-id');
                document.getElementById('modalTeacherName').textContent = btn.getAttribute('data-name');
                document.getElementById('modalTeacherDepartment').textContent = btn.getAttribute('data-department');
                document.getElementById('modalTeacherEmail').textContent = btn.getAttribute('data-email');
                document.getElementById('modalTeacherUsername').textContent = btn.getAttribute('data-username');
                document.getElementById('createdAt').textContent = btn.getAttribute('data-createdAt');
            });
        });

        document.querySelectorAll('.edit-teacher-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editFname').value = btn.getAttribute('data-fname');
                document.getElementById('editLname').value = btn.getAttribute('data-lname');
                document.getElementById('editEmail').value = btn.getAttribute('data-email');
                document.getElementById('editDepartment').value = btn.getAttribute('data-department');
                document.getElementById('editUsername').value = btn.getAttribute('data-username');
                document.getElementById('editPassword').value = btn.getAttribute('data-password');
            });
        });

        document.querySelectorAll('.drop-teacher-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('teacherId').value = btn.getAttribute('data-id');
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('password-toggle');
            const passwordInput = document.getElementById('editPassword');

            // Touch support for mobile devices
            passwordToggle.addEventListener('touchstart', function(e) {
                e.preventDefault();
                passwordInput.type = 'text';
            });

            passwordToggle.addEventListener('touchend', function(e) {
                e.preventDefault();
                passwordInput.type = 'password';
            });

            // Change icon when revealing password
            passwordToggle.addEventListener('mousedown', function() {
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            });

            passwordToggle.addEventListener('mouseup', function() {
                this.innerHTML = '<i class="fas fa-eye"></i>';
            });

            passwordToggle.addEventListener('mouseleave', function() {
                this.innerHTML = '<i class="fas fa-eye"></i>';
            });
        });

        // Real-time search functionality for students table
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.querySelector('tbody');
            const tableRows = Array.from(tableBody.querySelectorAll('tr'));

            // Function to perform the search
            function performSearch(searchTerm) {
                const query = searchTerm.toLowerCase().trim();
                let visibleRows = 0;

                tableRows.forEach(function(row) {
                    // Get all text content from the row (excluding action buttons)
                    const cells = row.querySelectorAll('td');
                    let rowText = '';

                    // Combine text from StudentID, Name, Course, and Email columns (skip Action column)
                    for (let i = 0; i < cells.length - 1; i++) {
                        rowText += cells[i].textContent.toLowerCase() + ' ';
                    }

                    // Check if search term matches any part of the row text
                    if (query === '' || rowText.includes(query)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Optional: Show/hide "No results" message
                showNoResultsMessage(visibleRows === 0 && query !== '');
            }

            // Function to show/hide no results message
            function showNoResultsMessage(show) {
                let noResultsRow = document.getElementById('no-results-row');

                if (show && !noResultsRow) {
                    // Create no results row if it doesn't exist
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'no-results-row';
                    noResultsRow.innerHTML = `
                <td colspan="5" class="text-center py-4" style="color: #6c757d;">
                    <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                    <br>
                    No teachers found matching your search
                </td>
            `;
                    tableBody.appendChild(noResultsRow);
                } else if (!show && noResultsRow) {
                    // Remove no results row if it exists
                    noResultsRow.remove();
                }
            }

            // Add event listener for real-time search
            searchInput.addEventListener('input', function(e) {
                performSearch(e.target.value);
            });

            // Add event listener for paste events
            searchInput.addEventListener('paste', function(e) {
                // Small delay to ensure pasted content is processed
                setTimeout(function() {
                    performSearch(searchInput.value);
                }, 10);
            });



            // Optional: Add search icon click functionality
            const searchIcon = document.querySelector('#searchInput + .input-group-text');
            if (searchIcon) {
                searchIcon.addEventListener('click', function() {
                    searchInput.focus();
                });
            }
        });
    </script>
</body>

</html>