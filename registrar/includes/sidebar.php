<?php

require_once '../session.php';
// **FIX: Start session with a temporary name first to check cookies**
if (session_status() === PHP_SESSION_NONE) {
    // Check if there's an admin session cookie
    $adminCookie = null;
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, 'registrar') === 0) {
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
        header("Location: /SMATI/registrar/login.php");
        exit();
    }
}

// Check if required session variables exist
if (!isset($_SESSION['id']) || !isset($_SESSION['user_type'])) {
    session_destroy();
   header("Location: /SMATI/registrar/login.php");
    exit();
}

// Validate the session
if (!validateSession($_SESSION['user_type'], $_SESSION['id'])) {
    session_destroy();
   header("Location: /SMATI/registrar/login.php");
    exit();
}

// Check if user account is still active
$conn = connectToDB();
$user_id = $_SESSION['id'];
$user_type = $_SESSION['user_type'];
$is_active = false;

if ($conn) {
    // Check user status based on user type
    if ($user_type === 'student') {
        $stmt = $conn->prepare("SELECT status FROM students WHERE student_id = ?");
    } else if ($user_type === 'teacher') {
        $stmt = $conn->prepare("SELECT status FROM teachers WHERE teacher_id = ?");
    } else if ($user_type === 'registrar') {
        $stmt = $conn->prepare("SELECT status FROM registrars WHERE registrar_id = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($status);
        $stmt->fetch();
        $stmt->close();

        // Check if account is active
        $is_active = ($status == '1');

        if (!$is_active) {
            // Account is no longer active - force logout
            header("Location: includes/logout.php");
            exit();
        }
    }
    $conn->close();
}
$current_page = basename($_SERVER['PHP_SELF']);
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
        <p class="mb-0">Registrar</p>
    </div>

    <!-- Close button for mobile -->
    <button class="mobile-close-btn" id="mobileCloseBtn" aria-label="Close Menu">
        <i class="fas fa-times"></i>
    </button>

    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'registrar-dashboard.php') ? 'active' : ''; ?>" href="registrar-dashboard.php">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'registrar-students.php') ? 'active' : ''; ?>" href="registrar-students.php">
                <i class="fas fa-file"></i>Grades
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

        // Event listeners
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', openSidebar);
        }

        if (mobileCloseBtn) {
            mobileCloseBtn.addEventListener('click', closeSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking on nav links (mobile only)
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });

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