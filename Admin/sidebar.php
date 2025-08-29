<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-brand flex-column text-center">
        <img class="mb-3" src="../images/smatilogo.png" alt="logo" width="80px" height="80px">
        <p class="mb-0">Admin</p>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-dashboard.php') ? 'active' : ''; ?>" href="admin-dashboard.php">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-students.php') ? 'active' : ''; ?>" href="admin-students.php">
                <i class="fas fa-user"></i>Students
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-teachers.php') ? 'active' : ''; ?>" href="admin-teachers.php">
                <i class="fas fa-users"></i>Teachers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-academics.php') ? 'active' : ''; ?>" href="admin-academics.php">
                <i class="fas fa-chart-bar"></i>Academics
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fa fa-file"></i>Grades
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-settings.php') ? 'active' : ''; ?>" href="admin-settings.php">
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