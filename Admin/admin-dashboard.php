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
                <a class="nav-link active" href="admin-dashboard.php">
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

    </main>
</body>
</html>