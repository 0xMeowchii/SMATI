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
    <div class="sidebar">
        <div class="sidebar-brand flex-column text-center">
            <img class="mb-3" src="../images/smatilogo.png" alt="logo" width="80px" height="80px">
            <p class="mb-0">Admin</p>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link" href="admin-dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-students.php">
                    <i class="fas fa-user"></i>Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="admin-teachers.php">
                    <i class="fas fa-users"></i>Teachers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-academics.php">
                    <i class="fas fa-chart-bar"></i>Academics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-settings.php">
                    <i class="fas fa-cog"></i>Settings
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="#" id="logout-link">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </li>
        </ul>
    </div>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-users me-2"></i>Teachers Management</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-teachers-btn" data-bs-toggle="modal" data-bs-target="#add-teachers-modal">
                    <i class="fas fa-plus me-1"></i>Add Teacher
                </button>
            </div>
        </div>

        <!-- Student Table -->
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Teachers</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search Teachers...">
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
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>IT Department</td>
                        <td>@exmaple.com</td>
                    </tr>
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
                        <form id="teacher-form">
                            <input type="hidden" id="student-id">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Teacher Information</h4>
                                <div class="col-md-6">
                                    <label for="student-name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="student-name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="student-email" class="form-label">Last Name</label>
                                    <input type="email" class="form-control" id="student-email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="student-id-number" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="student-id-number" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="student-course" class="form-label">Department</label>
                                    <select class="form-select" id="student-course" required>
                                        <option value="">Select Course</option>
                                        <option value="Computer Science">Computer Science</option>
                                        <option value="Electrical Engineering">Electrical Engineering</option>
                                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                                        <option value="Business Administration">Business Administration</option>
                                    </select>
                                </div>
                                <h4 class="pb-2 border-bottom">User Account</h4>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter username">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter password">
                                        <button class="input-group-text"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="save-teacher-btn">Save Teacher</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>