<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-brand flex-column text-center">
        <img class="mb-3" src="../images/smatilogo.png" alt="logo" width="80px" height="80px">
        <p class="mb-0">Teachers</p>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'teacher-dashboard.php') ? 'active' : ''; ?>" href="teacher-dashboard.php">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
         <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'teacher-subject.php') ? 'active' : ''; ?>" href="teacher-subject.php">
                <i class="fa fa-file"></i>My Subjects
            </a>
        </li> 
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fa fa-file"></i>Grades
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == '') ? 'active' : ''; ?>" href="">
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