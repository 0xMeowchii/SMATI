<?php
include '../database.php';
?>

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
    <link rel="stylesheet" href="./admin.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-user me-2"></i>Students Management</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-student-btn" data-bs-toggle="modal" data-bs-target="#add-students-modal">
                    <i class="fas fa-plus me-1"></i>Add Student
                </button>
            </div>
        </div>

        <!-- query -->
        <?php

        //INSERT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
            $conn = connectToDB();
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $course = $_POST['course'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $status = '1';

            if ($conn) {
                $stmt = $conn->prepare("INSERT INTO students (firstname, lastname, email, course, username, password, status) 
                                            VALUES (?, ?, ?, ?, ?, ?,?)");
                $stmt->bind_param("sssssss", $firstname, $lastname, $email, $course, $username, $password, $status);

                if ($stmt->execute()) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Student Added Successfully!',
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
            $student_id = $_POST['editId'];
            $firstname = $_POST['editFname'];
            $lastname = $_POST['editLname'];
            $email = $_POST['editEmail'];
            $course = $_POST['editCourse'];
            $username = $_POST['editUsername'];
            $password = $_POST['editPassword'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE students 
                                        SET firstname=?,
                                            lastname=?,
                                            email=?,
                                            course=?,
                                            username=?,
                                            password=?
                                        WHERE student_id=?");
                $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $course, $username, $password, $student_id);

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
                $conn->close();
            }
        }

        //DROP QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDrop'])) {
            $conn = connectToDB();
            $student_id = $_POST['studentId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE students SET status = '0' WHERE student_id=?");
                $stmt->bind_param("i", $student_id);

                if ($stmt->execute()) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Student Drop Successfully!',
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
                        <h5>All Students</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Students...">
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
                            <th>Name</th>
                            <th>Course</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM students WHERE status = '1'";
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
                                                <a class='btn btn-sm btn-outline-primary me-1 view-student-btn'
                                                data-id='" . $row["student_id"] . "'
                                                data-name='" . $row["lastname"] . ", " . $row["firstname"] . "'
                                                data-course='" . $row["course"] . "'
                                                data-email='" . $row["email"] . "'
                                                data-username='" . $row["username"] . "'
                                                data-createdAt='" . $row["createdAt"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#viewStudentModal'>
                                                    <i class='fas fa-eye'></i>
                                                </a>

                                                <a class='btn btn-sm btn-outline-secondary me-1 edit-student-btn'
                                                data-id='" . $row["student_id"] . "'
                                                data-fname='" . $row["firstname"] . "'
                                                data-lname='" . $row["lastname"] . "'
                                                data-course='" . $row["course"] . "'
                                                data-email='" . $row["email"] . "'
                                                data-username='" . $row["username"] . "'
                                                data-password='" . $row["password"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#editStudentModal'>
                                                    <i class='fas fa-edit'></i>
                                                </a>

                                                <a class='btn btn-sm btn-outline-danger me-1 drop-student-btn'
                                                data-id='" . $row["student_id"] . "'
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
                </table>
            </div>
        </div>

        <!-- Add Student Modal -->
        <div class="modal fade" id="add-students-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Add New Student</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Student Information</h4>
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="firstname" id="firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="lastname" id="lastname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Course</label>
                                    <select class="form-select" name="course" id="course" required>
                                        <option value="">Select Course</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSHM">BSHM</option>
                                    </select>
                                </div>
                                <h4 class="pb-2 border-bottom">User Account</h4>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter username" name="username" id="username" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Enter password" name="password" id="password" required>
                                        <span class="input-group-text" id="password-toggle"
                                            onmousedown="document.getElementById('password').type='text'"
                                            onmouseup="document.getElementById('password').type='password'"
                                            onmouseleave="document.getElementById('password').type='password'">
                                            <i class="fas fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnAdd">Save Student</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Student Modal -->
        <div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editStudentModal">Edit Student</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Student Information</h4>
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
                                    <label for="course" class="form-label">Course</label>
                                    <select class="form-select" name="editCourse" id="editCourse" required>
                                        <option value="">Select Course</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSHM">BSHM</option>
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
                                <button type="submit" class="btn btn-primary" name="btnEdit">Update Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- View Student Modal -->
        <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="viewStudentModal">Student Details</h3>
                    </div>
                    <div class="modal-body">
                        <?php
                        echo "<p><strong>Student ID: </strong><span id='modalStudentId'></span></p>
                              <p><strong>Name: </strong><span id='modalStudentName'></span></p>
                              <p><strong>Course: </strong><span id='modalStudentCourse'></span></p>
                              <p><strong>Email: </strong><span id='modalStudentEmail'></span></p>";
                        echo "<h3 class='pb-3 pt-3 border-bottom'>User Acccount</h3>
                              <p><strong>Username: </strong><span id='modalStudentUsername'></span></p>
                              <p><strong>createdAt: </strong><span id='createdAt'></span></p>";

                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drop Student Modal -->
        <div class="modal fade" id="dropStudentModal" tabindex="-1" role="dialog" aria-labelledby="dropStudentModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dropStudentModal">Confirm Drop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to drop this student?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="studentId" id="studentId">
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
        document.querySelectorAll('.view-student-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('modalStudentId').textContent = btn.getAttribute('data-id');
                document.getElementById('modalStudentName').textContent = btn.getAttribute('data-name');
                document.getElementById('modalStudentCourse').textContent = btn.getAttribute('data-course');
                document.getElementById('modalStudentEmail').textContent = btn.getAttribute('data-email');
                document.getElementById('modalStudentUsername').textContent = btn.getAttribute('data-username');
                document.getElementById('createdAt').textContent = btn.getAttribute('data-createdAt');
            });
        });
        document.querySelectorAll('.edit-student-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editFname').value = btn.getAttribute('data-fname');
                document.getElementById('editLname').value = btn.getAttribute('data-lname');
                document.getElementById('editEmail').value = btn.getAttribute('data-email');
                document.getElementById('editCourse').value = btn.getAttribute('data-course');
                document.getElementById('editUsername').value = btn.getAttribute('data-username');
                document.getElementById('editPassword').value = btn.getAttribute('data-password');
            });
        });

        document.querySelectorAll('.drop-student-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('studentId').value = btn.getAttribute('data-id');
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
                    No students found matching your search
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