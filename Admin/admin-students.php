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


    //INSERT QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
        $conn = connectToDB();
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $course = $_POST['set'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $status = '1';
        $student_image = '';

        // File upload handling
        $uploadDir = '../images/'; // Directory where you want to store images
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if ($conn) {

            $checkStmt = $conn->prepare("SELECT * FROM students WHERE firstname = ? AND lastname = ?");
            $checkStmt->bind_param("ss", $firstname, $lastname);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Student already exists!',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
            } else {

                // Check if file was uploaded
                if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['student_image'];

                    // Validate file type
                    $fileType = mime_content_type($file['tmp_name']);
                    if (!in_array($fileType, $allowedTypes)) {
                        echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Invalid file type. Please upload JPEG, JPG, or PNG images only.',
                                    confirmButtonColor: '#d33'
                                });
                            </script>";
                    } else if ($file['size'] > $maxFileSize) {
                        echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'File too large. Maximum size is 5MB.',
                                    confirmButtonColor: '#d33'
                                });
                            </script>";
                    } else {
                        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $newFilename = $firstname . '_' . $lastname . '.' . $fileExtension;
                        $destination = $uploadDir . $newFilename;

                        // Move uploaded file
                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            $student_image = $destination; // Store the path for database
                        } else {
                            echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to upload image.',
                                    confirmButtonColor: '#d33'
                                });
                            </script>";
                            return;
                        }

                        // Prepare and execute the INSERT query
                        $stmt = $conn->prepare("INSERT INTO students (firstname, lastname, email, course, username, password, status, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $course, $username, $password, $status, $student_image);

                        if ($stmt->execute()) {
                            $studentName = $lastname . ', ' . $firstname;
                            logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'CREATE_STUDENT', "Created student account: $studentName (Set: $course)");

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
                            // If database insert fails, delete the uploaded file
                            if (!empty($student_image) && file_exists($student_image)) {
                                unlink($student_image);
                            }

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
                } else {
                    // Prepare and execute the INSERT query
                    $stmt = $conn->prepare("INSERT INTO students (firstname, lastname, email, course, username, password, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssss", $firstname, $lastname, $email, $course, $username, $password, $status);

                    if ($stmt->execute()) {
                        $studentName = $lastname . ', ' . $firstname;
                        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'CREATE_STUDENT', "Created student account: $studentName (Set: $course)");

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
                }
            }

            $checkStmt->close();
        } else {
            echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Database connection failed',
                                confirmButtonColor: '#d33'
                            });
                        });
                    </script>";
        }
        $conn->close();
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
        $student_image = ''; // Initialize

        // Check if password field has value
        $hasPassword = !empty($_POST['editPassword']);
        if ($hasPassword) {
            $password = password_hash($_POST['editPassword'], PASSWORD_DEFAULT);
        }

        // File upload handling for edit
        $uploadDir = '../images/';
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxFileSize = 5 * 1024 * 1024;

        if ($conn) {
            // Check for existing email
            $checkStmt = $conn->prepare("SELECT student_id FROM students WHERE (firstname = ? AND lastname = ?) AND student_id != ?");
            $checkStmt->bind_param("ssi", $firstname, $lastname, $student_id);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Student already exists!',
                                confirmButtonColor: '#d33'
                            });
                        });
                    </script>";
            } else {
                // First, get the current image path
                $currentImageStmt = $conn->prepare("SELECT image FROM students WHERE student_id = ?");
                $currentImageStmt->bind_param("i", $student_id);
                $currentImageStmt->execute();
                $currentImageStmt->bind_result($currentImagePath);
                $currentImageStmt->fetch();
                $currentImageStmt->close();

                // Check if new file was uploaded
                if (isset($_FILES['edit_student_image']) && $_FILES['edit_student_image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['edit_student_image'];

                    // Validate file type
                    $fileType = mime_content_type($file['tmp_name']);
                    if (!in_array($fileType, $allowedTypes)) {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Invalid file type. Please upload JPEG, JPG, or PNG images only.',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
                    } else if ($file['size'] > $maxFileSize) {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'File too large. Maximum size is 5MB.',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
                    } else {
                        // Generate unique filename
                        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $newFilename = $firstname . '_' . $lastname . '.' . $fileExtension;
                        $destination = $uploadDir . $newFilename;

                        // Move uploaded file
                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            $student_image = $destination;

                            // Delete old image if it exists
                            if (!empty($currentImagePath) && file_exists($currentImagePath)) {
                                unlink($currentImagePath);
                            }
                        }

                        // Build query based on whether password is provided
                        if ($hasPassword) {
                            $stmt = $conn->prepare("UPDATE students 
                            SET firstname=?,
                                lastname=?,
                                email=?,
                                course=?,
                                username=?,
                                password=?,
                                image=?
                            WHERE student_id=?");
                            $stmt->bind_param("sssssssi", $firstname, $lastname, $email, $course, $username, $password, $student_image, $student_id);
                        } else {
                            $stmt = $conn->prepare("UPDATE students 
                            SET firstname=?,
                                lastname=?,
                                email=?,
                                course=?,
                                username=?,
                                image=?
                            WHERE student_id=?");
                            $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $course, $username, $student_image, $student_id);
                        }

                        if ($stmt->execute()) {
                            logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_STUDENT', "Updated student account: Student ID = $student_id");

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
                } else {
                    // Keep the current image if no new file uploaded
                    $student_image = $currentImagePath;

                    // Build query based on whether password is provided
                    if ($hasPassword) {
                        $stmt = $conn->prepare("UPDATE students 
                        SET firstname=?,
                            lastname=?,
                            email=?,
                            course=?,
                            username=?,
                            password=?
                        WHERE student_id=?");
                        $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $course, $username, $password, $student_id);
                    } else {
                        $stmt = $conn->prepare("UPDATE students 
                        SET firstname=?,
                            lastname=?,
                            email=?,
                            course=?,
                            username=?
                        WHERE student_id=?");
                        $stmt->bind_param("sssssi", $firstname, $lastname, $email, $course, $username, $student_id);
                    }

                    if ($stmt->execute()) {
                        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_STUDENT', "Updated student account: Student ID = $student_id");

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
        } else {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Database connection failed',
                            confirmButtonColor: '#d33'
                        });
                    });
                </script>";
        }
        $conn->close();
    }

    //DROP QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDrop'])) {
        $conn = connectToDB();
        $student_id = $_POST['studentId'];

        if ($conn) {
            $stmt = $conn->prepare("UPDATE students SET status = '0' WHERE student_id=?");
            $stmt->bind_param("i", $student_id);

            if ($stmt->execute()) {

                logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DROP_STUDENT', "Drop Student Account: Student ID = $student_id");

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

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-user me-2"></i>Students Management</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-student-btn" data-bs-toggle="modal" data-bs-target="#add-students-modal">
                    <i class="fas fa-plus me-1"></i>Add Student
                </button>
            </div>
        </div>

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

            <div class="table-responsive flex-grow-1 overflow-auto" style="max-height:600px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Name</th>
                            <th>Set</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM students WHERE status = '1' ORDER BY lastname,firstname ASC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                echo "<td>" . $row["course"] . "</td>";
                                echo "<td>
                                                <a class='btn btn-sm btn-outline-primary me-1 view-student-btn'
                                                data-id='" . $row["student_id"] . "'
                                                data-name='" . $row["lastname"] . ", " . $row["firstname"] . "'
                                                data-course='" . $row["course"] . "'
                                                data-email='" . $row["email"] . "'
                                                data-username='" . $row["username"] . "'
                                                data-createdAt='" . (new DateTime($row['createdAt']))->format('m-d-Y h:i A') . "'
                                                data-image='" . $row["image"] . "'
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
                                                data-image='" . $row["image"] . "'
                                                data-bs-toggle='modal' 
                                                data-bs-target='#editStudentModal'>
                                                    <i class='fas fa-edit'></i>
                                                </a>

                                                <a class='btn btn-sm btn-outline-danger me-1 drop-student-btn'
                                                data-id='" . $row["student_id"] . "'>
                                                    <i class='fas fa-trash'></i>
                                                </a>
                                              </td>";
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

        <!-- Add Student Modal -->
        <div class="modal fade" id="add-students-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Add New Student</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data" id="insertForm">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Student Information</h4>

                                <!-- Student Image Upload -->
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center mb-3">
                                        <div class="border border-2 border-dashed rounded d-flex align-items-center justify-content-center mb-2" style="width: 150px; height: 150px;">
                                            <div class="text-center text-muted" id="imagePreview">
                                                <i class="fas fa-user-graduate fa-3x mb-2"></i>
                                                <div>Student Photo</div>
                                            </div>
                                        </div>
                                        <div class="position-relative mb-2">
                                            <button type="button" class="btn btn-primary btn-sm">
                                                <i class="fas fa-upload me-1"></i> Choose Image
                                            </button>
                                            <input type="file" name="student_image" id="student_image" accept="image/*" class="position-absolute top-0 start-0 w-100 h-100 opacity-0">
                                        </div>

                                        <!-- Image Name Display Input -->
                                        <div class="w-100">
                                            <label for="imageName" class="form-label">Selected Image</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-image"></i></span>
                                                <input type="text" class="form-control" id="imageName" placeholder="No file selected" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="firstname" id="firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="lastname" id="lastname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">ID #</label>
                                    <input type="text" class="form-control" name="email" id="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="set" class="form-label">Set</label>
                                    <select class="form-select" name="set" id="set" required>
                                        <option value="">Select Set</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                                <h4 class="pb-2 border-bottom">User Account</h4>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter username" name="username" id="username" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Enter password" name="password" id="password" required>
                                        <span class="input-group-text" id="password-toggle" style="cursor: pointer;" title="Show Password"
                                            onmousedown="document.getElementById('password').type='text'"
                                            onmouseup="document.getElementById('password').type='password'"
                                            onmouseleave="document.getElementById('password').type='password'">
                                            <i class="fas fa-eye"></i></span>
                                        <span class="input-group-text generate-password" style="cursor: pointer;" title="Generate Password">
                                            <i class="fas fa-key"></i></span>
                                        <span class="input-group-text copy-password" style="cursor: pointer;" title="Copy to clipboard">
                                            <i class="fas fa-copy"></i>
                                        </span>
                                    </div>
                                    <div id="insertError">

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
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data" id="editForm">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Student Information</h4>

                                <!-- Student Image Upload for Edit -->
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center mb-3">
                                        <div class="border border-2 border-dashed rounded d-flex align-items-center justify-content-center mb-2" style="width: 150px; height: 150px;">
                                            <div class="text-center text-muted" id="editImagePreview">
                                                <i class="fas fa-user-graduate fa-3x mb-2"></i>
                                                <div>Student Photo</div>
                                            </div>
                                        </div>
                                        <div class="position-relative mb-2">
                                            <button type="button" class="btn btn-primary btn-sm">
                                                <i class="fas fa-upload me-1"></i> Change Image
                                            </button>
                                            <input type="file" name="edit_student_image" id="edit_student_image" accept="image/*" class="position-absolute top-0 start-0 w-100 h-100 opacity-0">
                                        </div>

                                        <!-- Image Name Display Input -->
                                        <div class="w-100">
                                            <label for="editImageName" class="form-label">Selected Image</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-image"></i></span>
                                                <input type="text" class="form-control" id="editImageName" placeholder="No file selected" readonly>
                                            </div>
                                        </div>
                                        <!-- Current Image Info -->
                                        <div class="w-100 mt-2">
                                            <small class="text-muted" id="currentImageInfo">No current image</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="editFname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="editFname" id="editFname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editLname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="editLname" id="editLname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editEmail" class="form-label">ID #</label>
                                    <input type="text" class="form-control" name="editEmail" id="editEmail" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="editCourse" class="form-label">Set</label>
                                    <select class="form-select" name="editCourse" id="editCourse" required>
                                        <option value="">Select Set</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                                <h4 class="pb-2 border-bottom">User Account</h4>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter username" name="editUsername" id="editUsername" required>
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
                                <button type="submit" class="btn btn-primary" name="btnEdit">Update Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- View Student Modal -->
        <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h3 class="modal-title" id="viewStudentModal">Student Details</h3>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Student Image Column -->
                            <div class="col-md-4 text-center mb-3">
                                <div class="student-image-container">
                                    <div id="modalStudentImage" class="border rounded d-flex align-items-center justify-content-center mx-auto" style="width: 200px; height: 200px; background-color: #f8f9fa;">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-user-graduate fa-4x mb-2"></i>
                                            <div>No Image</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Details Column -->
                            <div class="col-md-8">
                                <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>ID #:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <input type="hidden" id="modalStudentId">
                                        <p id="modalStudentEmail"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Name:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p id="modalStudentName"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Set:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p id="modalStudentCourse"></p>
                                    </div>
                                </div>
                                <h5 class="border-bottom pb-2 mb-3 mt-4">User Account</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Username:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p id="modalStudentUsername"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Created At:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p id="createdAt"></p>
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
        // Apply to all inputs except those containing specific words in ID
        document.querySelectorAll('input[type="text"]').forEach(input => {
            const excludePatterns = ['username', 'email', 'editUsername', 'editEmail'];
            const shouldExclude = excludePatterns.some(pattern => input.id.includes(pattern));

            if (!shouldExclude) {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        });

        document.querySelectorAll('.view-student-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('modalStudentId').value = btn.getAttribute('data-id');
                document.getElementById('modalStudentName').textContent = btn.getAttribute('data-name');
                document.getElementById('modalStudentCourse').textContent = btn.getAttribute('data-course');
                document.getElementById('modalStudentEmail').textContent = btn.getAttribute('data-email');
                document.getElementById('modalStudentUsername').textContent = btn.getAttribute('data-username');
                document.getElementById('createdAt').textContent = btn.getAttribute('data-createdAt');

                // Handle student image
                const imagePath = btn.getAttribute('data-image');
                const studentImageContainer = document.getElementById('modalStudentImage');

                if (imagePath && imagePath !== '' && imagePath !== 'null') {
                    studentImageContainer.innerHTML = `<img src="${imagePath}" class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;" alt="Student Photo">`;
                } else {
                    studentImageContainer.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-user-graduate fa-4x mb-2"></i>
                    <div>No Image</div>
                </div>
            `;
                }
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

                // Get the image path from data attribute
                const imagePath = btn.getAttribute('data-image');
                const editImagePreview = document.getElementById('editImagePreview');
                const currentImageInfo = document.getElementById('currentImageInfo');

                // Display current image if exists
                if (imagePath && imagePath !== '') {
                    editImagePreview.innerHTML = `<img src="${imagePath}" class="img-fluid h-100" alt="Student Photo">`;
                    currentImageInfo.textContent = `Current image: ${imagePath.split('/').pop()}`;
                } else {
                    editImagePreview.innerHTML = `
                <i class="fas fa-user-graduate fa-3x mb-2"></i>
                <div>Student Photo</div>
            `;
                    currentImageInfo.textContent = 'No current image';
                }

                // Reset file input
                document.getElementById('edit_student_image').value = '';
                document.getElementById('editImageName').value = '';
            });
        });

        // Image preview for edit modal
        document.getElementById('edit_student_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('editImagePreview');
            const imageNameInput = document.getElementById('editImageName');

            if (file) {
                const reader = new FileReader();

                reader.addEventListener('load', function() {
                    preview.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = reader.result;
                    img.className = 'img-fluid h-100';
                    preview.appendChild(img);
                });

                reader.readAsDataURL(file);
                imageNameInput.value = file.name;
            } else {
                // If no file selected, show current image again
                const currentImagePath = document.querySelector('.edit-student-btn[data-id="' + document.getElementById('editId').value + '"]').getAttribute('data-image');
                if (currentImagePath && currentImagePath !== '') {
                    preview.innerHTML = `<img src="${currentImagePath}" class="img-fluid h-100" alt="Student Photo">`;
                } else {
                    preview.innerHTML = `
                <i class="fas fa-user-graduate fa-3x mb-2"></i>
                <div>Student Photo</div>
            `;
                }
                imageNameInput.value = '';
            }
        });

        document.querySelectorAll('.drop-student-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('studentId').value = btn.getAttribute('data-id');
            });
        });

        // Image preview functionality
        document.getElementById('student_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const imageNameInput = document.getElementById('imageName');

            if (file) {
                const reader = new FileReader();

                reader.addEventListener('load', function() {
                    preview.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = reader.result;
                    img.className = 'img-fluid h-100';
                    preview.appendChild(img);
                });

                reader.readAsDataURL(file);
                imageNameInput.value = file.name;
            } else {
                preview.innerHTML = `
                    <i class="fas fa-user-graduate fa-3x mb-2"></i>
                    <div>Student Photo</div>
                `;
                imageNameInput.value = '';
            }
        });

        // Function to create image popup when clicking on uploaded images
        function initializeImagePopup() {
            // Add click event to view modal image
            document.addEventListener('click', function(e) {
                // Check if clicked element is an image inside the modal student image container
                if (e.target.closest('#modalStudentImage img')) {
                    const img = e.target.closest('#modalStudentImage img');
                    showImagePopup(img.src);
                }

                // Check if clicked element is an image in the edit modal preview
                if (e.target.closest('#editImagePreview img')) {
                    const img = e.target.closest('#editImagePreview img');
                    showImagePopup(img.src);
                }

                // Check if clicked element is an image in the add modal preview
                if (e.target.closest('#imagePreview img')) {
                    const img = e.target.closest('#imagePreview img');
                    showImagePopup(img.src);
                }
            });
        }

        // Function to show image in popup
        function showImagePopup(imageSrc) {
            // Create popup overlay
            const popupOverlay = document.createElement('div');
            popupOverlay.className = 'image-popup-overlay';
            popupOverlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                cursor: zoom-out;
            `;

            // Create popup content
            const popupContent = document.createElement('div');
            popupContent.style.cssText = `
                position: relative;
                max-width: 90%;
                max-height: 90%;
                display: flex;
                justify-content: center;
                align-items: center;
            `;

            // Create image element
            const popupImage = document.createElement('img');
            popupImage.src = imageSrc;
            popupImage.style.cssText = `
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            `;

            // Create close button
            const closeButton = document.createElement('button');
            closeButton.innerHTML = '&times;';
            closeButton.style.cssText = `
                position: absolute;
                top: -40px;
                right: -40px;
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                font-size: 30px;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                justify-content: center;
                align-items: center;
                transition: background 0.3s ease;
            `;

            // Add hover effect to close button
            closeButton.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 255, 255, 0.3)';
            });
            closeButton.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 255, 255, 0.2)';
            });

            // Close popup function
            function closePopup() {
                document.body.removeChild(popupOverlay);
                document.removeEventListener('keydown', handleKeyPress);
            }

            // Handle keyboard events
            function handleKeyPress(e) {
                if (e.key === 'Escape') {
                    closePopup();
                }
            }

            // Add event listeners
            closeButton.addEventListener('click', closePopup);
            popupOverlay.addEventListener('click', function(e) {
                if (e.target === popupOverlay) {
                    closePopup();
                }
            });
            document.addEventListener('keydown', handleKeyPress);

            // Assemble and append to body
            popupContent.appendChild(popupImage);
            popupContent.appendChild(closeButton);
            popupOverlay.appendChild(popupContent);
            document.body.appendChild(popupOverlay);

            // Add animation
            popupOverlay.style.opacity = '0';
            popupOverlay.style.transition = 'opacity 0.3s ease';

            setTimeout(() => {
                popupOverlay.style.opacity = '1';
            }, 10);
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {

            initializeImagePopup();

            // Also add cursor pointer to clickable images
            const style = document.createElement('style');
            style.textContent = `
                #modalStudentImage img,
                #editImagePreview img,
                #imagePreview img {
                    cursor: zoom-in;
                    transition: transform 0.2s ease;
                }
                #modalStudentImage img:hover,
                #editImagePreview img:hover,
                #imagePreview img:hover {
                    transform: scale(1.02);
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>

</html>