<?php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand flex-column text-center">
        <img class="mb-3" src="../images/logo5.png" alt="logo" width="80px" height="80px">
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
            <a class="nav-link <?php echo ($current_page == 'admin-grades.php') ? 'active' : ''; ?>" href="admin-grades.php">
                <i class="fa fa-file"></i>Grades
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-announcements.php') ? 'active' : ''; ?>" href="admin-announcements.php">
                <i class="fa fa-calendar"></i>Announcements
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-settings.php') ? 'active' : ''; ?>" href="admin-settings.php">
                <i class="fas fa-cog"></i>Settings
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link text-danger" id="logoutBtn">
                <i class="fas fa-sign-out-alt"></i>Logout
            </a>
        </li>
    </ul>
</div>

<!-- SweetAlert2 Script for Logout Confirmation -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of the system.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary mx-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to logout.php
                    window.location.href = 'logout.php';
                }
            });
        });
    }
});
</script>