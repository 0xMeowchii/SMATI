<?php
require_once '../session.php';

// **FIX: Start session with a temporary name first to check cookies**
if (session_status() === PHP_SESSION_NONE) {
    // Check if there's an admin session cookie
    $adminCookie = null;
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, 'admin_') === 0) {
            $adminCookie = $name;
            break;
        }
    }

    if ($adminCookie) {
        // Found admin cookie, use that session name
        session_name($adminCookie);
        session_start();
    } else {
        // No admin cookie found, redirect to login
        header("Location: /SMATI/Admin/login.php");
        exit();
    }
}

// Now check if session variables exist
if (!isset($_SESSION['id']) || !isset($_SESSION['user_type'])) {
    header("Location: /SMATI/Admin/login.php");
    exit();
}

// Validate session
if (!validateSession($_SESSION['user_type'], $_SESSION['id'])) {
    header("Location: /SMATI/Admin/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Determine if current page belongs to People Management dropdown
$is_people_management = in_array($current_page, [
    'admin-students.php',
    'admin-teachers.php',
    'admin-registrar.php',
    'admin-users.php'
]);

// Determine active dropdown
$active_dropdown = '';
if ($is_people_management) {
    $active_dropdown = 'people-management';
}
?>

<!-- Mobile Menu Toggle Button -->
<button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand flex-column text-center">
        <img class="mb-3" src="../images/logo5.png" alt="logo" width="80px" height="80px">
        <p class="mb-0"><?= $_SESSION['username'] ?></p>
    </div>

    <!-- Close button for mobile -->
    <button class="mobile-close-btn" id="mobileCloseBtn" aria-label="Close Menu">
        <i class="fas fa-times"></i>
    </button>

    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'admin-dashboard.php') ? 'active' : ''; ?>" href="admin-dashboard.php">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
        </li>

        <!-- People Management Dropdown -->
        <li class="nav-item sidebar-dropdown <?php echo ($active_dropdown == 'people-management') ? 'active' : ''; ?>">
            <a class="nav-link <?php echo ($is_people_management ? 'active' : ''); ?>" id="peopleManagementToggle">
                <i class="fas fa-users"></i>User Management
                <span class="dropdown-indicator"></span>
            </a>
            <div class="sidebar-dropdown-content">
                <?php if ($_SESSION['username'] == 'admin'): ?>
                <a class="nav-link <?php echo ($current_page == 'admin-users.php') ? 'active' : ''; ?>" href="admin-users.php">
                    <i class="fas fa-user"></i>Admin
                </a>
             <?php endif; ?>
                <a class="nav-link <?php echo ($current_page == 'admin-students.php') ? 'active' : ''; ?>" href="admin-students.php">
                    <i class="fas fa-user-graduate"></i>Students
                </a>
                <a class="nav-link <?php echo ($current_page == 'admin-teachers.php') ? 'active' : ''; ?>" href="admin-teachers.php">
                    <i class="fas fa-chalkboard-teacher"></i>Teachers
                </a>
                <a class="nav-link <?php echo ($current_page == 'admin-registrar.php') ? 'active' : ''; ?>" href="admin-registrar.php">
                    <i class="fas fa-address-book"></i>Registrar
                </a>
            </div>
        </li>

        <?php if ($_SESSION['username'] == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'admin-academics.php') ? 'active' : ''; ?>" href="admin-academics.php">
                    <i class="fas fa-chart-bar"></i>Academics
                </a>
            </li>
        <?php endif; ?>

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

        <?php if ($_SESSION['username'] == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'admin-settings.php') ? 'active' : ''; ?>" href="admin-settings.php">
                    <i class="fas fa-cog"></i>Settings
                </a>
            </li>
        <?php endif; ?>

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
        // Mobile menu toggle functionality
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileCloseBtn = document.getElementById('mobileCloseBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Function to open sidebar
        function openSidebar() {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Function to close sidebar
        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Event listeners for mobile menu
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', openSidebar);
        }

        if (mobileCloseBtn) {
            mobileCloseBtn.addEventListener('click', closeSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Collapsible dropdown functionality
        const peopleManagementToggle = document.getElementById('peopleManagementToggle');
        const peopleManagementDropdown = peopleManagementToggle?.closest('.sidebar-dropdown');

        if (peopleManagementToggle) {
            peopleManagementToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class on the dropdown
                if (peopleManagementDropdown) {
                    peopleManagementDropdown.classList.toggle('active');

                    // If closing dropdown on mobile, don't close sidebar
                    if (window.innerWidth <= 992 && !peopleManagementDropdown.classList.contains('active')) {
                        e.stopPropagation();
                    }
                }
            });
        }

        // Close sidebar when clicking on dropdown links (mobile only)
        const dropdownLinks = document.querySelectorAll('.sidebar-dropdown-content .nav-link');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar when clicking on regular nav links (mobile only)
        const navLinks = document.querySelectorAll('.sidebar .nav-link:not(.sidebar-dropdown > .nav-link)');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });

        // Auto-expand dropdown if current page is inside it
        if (<?php echo $is_people_management ? 'true' : 'false'; ?> && peopleManagementDropdown) {
            peopleManagementDropdown.classList.add('active');
        }

        // Logout functionality
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
                        window.location.href = 'includes/logout.php';
                    }
                });
            });
        }
    });
</script>