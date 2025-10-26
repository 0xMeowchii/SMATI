<?php
session_name('superuser');
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /SMATI/Super User/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-brand flex-column text-center">
        <img class="mb-3" src="../images/smatilogo.png" alt="logo" width="80px" height="80px">
        <p class="mb-0">Super User</p>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-list.php') ? 'active' : ''; ?>" href="admin-list.php">
                <i class="fas fa-universal-access"></i>Admin
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'teacher-list.php') ? 'active' : ''; ?>" href="teacher-list.php">
                <i class="fa fa-users"></i>Teacher
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'student-list.php') ? 'active' : ''; ?>" href="student-list.php">
                <i class="fa fa-user"></i>Student
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'backup.php') ? 'active' : ''; ?>" href="backup.php">
                <i class="fa fa-database"></i>Back Up
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