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
    <style>
        /* Custom styles for what Bootstrap doesn't provide */
        .custom-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .custom-card .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 12px 12px 0 0 !important;
        }
    </style>
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
                <a class="nav-link" href="admin-teachers.php">
                    <i class="fas fa-users"></i>Teachers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-academics.php">
                    <i class="fas fa-chart-bar"></i>Academics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="admin-settings.php">
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
            <h4><i class="fas fa-cog me-2"></i>System Settings</h4>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 custom-card mb-4">
                    <div class="card-header px-4 py-3 bg-primary text-white">
                        <h5 class="card-title mb-0">Database Management</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label">Backup Data</label>
                            <p class="card-text text-muted small mb-2">Create a complete backup of all system data</p>
                            <button class="btn btn-outline-primary w-100" id="backup-btn">
                                <i class="fas fa-database me-2"></i>Create Backup
                            </button>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Restore Data</label>
                            <p class="text-muted small mb-2">Restore system from a previous backup</p>
                            <input type="file" class="form-control mb-2" id="restore-file">
                            <button class="btn btn-outline-secondary w-100" id="restore-btn">
                                <i class="fas fa-upload me-2"></i>Restore Backup
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reset System</label>
                            <p class="text-muted small mb-2">Warning: This will delete all data</p>
                            <button class="btn btn-outline-danger w-100" id="reset-btn">
                                <i class="fas fa-trash-alt me-2"></i>Reset All Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 custom-card mb-4">
                    <div class="card-header px-4 py-3 bg-success text-white">
                        <h5 class="card-title mb-0">System Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label">System Version</label>
                            <input type="text" class="form-control" value="EduExam v1.2.0" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Last Backup</label>
                            <input type="text" class="form-control" id="last-backup" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>