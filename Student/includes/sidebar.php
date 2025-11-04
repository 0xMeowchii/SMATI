<?php
require_once '../database.php';
require_once '../session.php';

// **FIX: Start session with a temporary name first to check cookies**
if (session_status() === PHP_SESSION_NONE) {
    // Check if there's an admin session cookie
    $adminCookie = null;
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, 'student') === 0) {
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
        header("Location: /SMATI/");
        exit();
    }
}

// Check if required session variables exist
if (!isset($_SESSION['id']) || !isset($_SESSION['user_type'])) {
    session_destroy();
    header("Location: /SMATI/");
    exit();
}

// Validate the session
if (!validateSession($_SESSION['user_type'], $_SESSION['id'])) {
    session_destroy();
    header("Location: /SMATI/");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);


// Check if user account is still active
$conn = connectToDB();
$user_id = $_SESSION['id'];
$user_type = $_SESSION['user_type'];
$user_image = '';
$is_active = false;

if ($conn) {
    // Check user status based on user type
    if ($user_type === 'student') {
        $stmt = $conn->prepare("SELECT image, status FROM students WHERE student_id = ?");
    } else if ($user_type === 'teacher') {
        $stmt = $conn->prepare("SELECT image, status FROM teachers WHERE teacher_id = ?");
    } else if ($user_type === 'registrar') {
        $stmt = $conn->prepare("SELECT image, status FROM registrars WHERE registrar_id = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($user_image, $status);
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
        <img class="mb-3 rounded-circle" src="<?php echo !empty($user_image) ? $user_image : '../images/logo5.png';  ?>" alt="Profile" width="80px" height="80px" style="object-fit: cover; cursor: pointer;" id="student_profile_image">
        <p class="mb-0"><?php echo $_SESSION['fullname']; ?></p>
    </div>

    <!-- Close button for mobile -->
    <button class="mobile-close-btn" id="mobileCloseBtn" aria-label="Close Menu">
        <i class="fas fa-times"></i>
    </button>

    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'student-dashboard.php') ? 'active' : ''; ?>" href="student-dashboard.php">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'student-grades.php') ? 'active' : ''; ?>" href="student-grades.php">
                <i class="fa fa-book"></i>My Grades
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

        // Image popup functionality for sidebar profile image
        const studentProfileImage = document.getElementById('student_profile_image');

        if (studentProfileImage) {
            studentProfileImage.addEventListener('click', function() {
                const imageSrc = this.src;

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
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                border: 4px solid white;
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
            });
        }
    });

    // Add CSS for image popup
    const sidebarStyle = document.createElement('style');
    sidebarStyle.textContent = `
    #student_profile_image {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    #student_profile_image:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
`;
    document.head.appendChild(sidebarStyle);
</script>