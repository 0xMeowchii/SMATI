<?php

include 'database.php';
include 'includes/activity_logger.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header.php'; ?>
    <style>
        .form-check-input:checked {
            background-color: var(--navy-blue);
            border-color: var(--navy-blue);
        }

        .form-check-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.15);
        }
    </style>
</head>

<body class="student-mode animated-background">

    <?php

    require_once 'session.php';
    require_once 'LoginSecurity.php';

    // Check both session types and redirect accordingly
    if (checkExistingSession('student')) {
        header("Location: ./Student/student-dashboard.php");
        exit();
    }

    if (checkExistingSession('teacher')) {
        header("Location: ./Teacher/teacher-dashboard.php");
        exit();
    }

    // No sessions found, initialize guest session
    initGuestSession();

    // Initialize variables
    $showSuccess = false;
    $errors = [];
    $dashboard = '';

    // LOGIN QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
        $id = $_POST['loginId'] ?? '';
        $password = $_POST['loginPassword'] ?? '';
        $loginType = $_POST['user_type'] ?? 'student';

        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            $secret_key = "6LdxyvQrAAAAAFje30yKm8Zyt_d3lrJv7wjzcZP1";
            $captcha_response = $_POST['g-recaptcha-response'] ?? '';

            $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
            $response_data = json_decode($verify_response);

            if ($response_data->success) {
                $conn = connectToDB();
                $loginSecurity = new LoginSecurity($conn);

                // Check if user is locked out
                $lockoutStatus = $loginSecurity->checkLockout($id);

                if ($lockoutStatus['locked']) {
                    // User is locked out - don't process login, just show lockout message
                    $lockoutMessage = true;
                } else {
                    // Validate inputs
                    if (empty($id)) {
                        $errors[] = "ID or Username is required.";
                    }

                    if (empty($password)) {
                        $errors[] = "Password is required.";
                    }

                    if (empty($errors)) {
                        if ($loginType === 'student') {
                            $stmt = $conn->prepare("SELECT * FROM students WHERE (email = ? OR username = ?)");
                            $stmt->bind_param("ss", $id, $id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                            $dashboard = './Student/student-dashboard.php';
                        } else {

                            $stmt = $conn->prepare("SELECT * FROM teachers WHERE (email = ? OR username = ?)");
                            $stmt->bind_param("ss", $id, $id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                            $dashboard = './Teacher/teacher-dashboard.php';
                        }

                        if ($user) {
                            // Check if account is active (status = 1)
                            if ($user['status'] == '0') {
                                // Account is disabled
                                $errors[] = "Your account has been disabled. Please contact the administrator for assistance.";
                            } else if (password_verify($password, $user['password'])) {
                                // Successful login - clear attempts
                                $loginSecurity->clearAttempts($id);

                                session_destroy();

                                $userType = ($loginType === 'student') ? 'student' : 'teacher';

                                // Get the user's unique ID
                                $userId = ($loginType === 'student') ? $user['student_id'] : $user['teacher_id'];

                                // Start unique session for this specific user
                                startUniqueSession($userType, $userId);

                                $_SESSION['username'] = $user['username'];
                                $_SESSION['firstname'] = $user['firstname'];
                                $_SESSION['fullname'] = $user['lastname'] . ", " . $user['firstname'];
                                $_SESSION['user_type'] = $loginType;
                                $_SESSION['logged_in'] = true;

                                logActivity($conn, $userId, $userType, 'LOGIN', "logged in to the system.");

                                $showSuccess = true;

                                echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                                icon: 'warning',
                                                title: 'Privacy Notice!',
                                                html: 'This system logs users\' IPv4 addresses for security and monitoring in compliance with <strong>Republic Act No. 10173 – Data Privacy Act of 2012</strong>. Logs are viewable only by authorized SMATI administrators, and any external access requires a valid court subpoena.',
                                                confirmButtonColor: '#0d6efd',
                                                confirmButtonText: 'I Understand & Accept',
                                                backdrop: true,
                                                allowOutsideClick: false
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    // Show success message and auto-redirect
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Login Successful!',
                                                        text: 'Redirecting to dashboard...',
                                                        confirmButtonColor: '#0d6efd',
                                                        showConfirmButton: false,
                                                        timer: 2000,
                                                        timerProgressBar: true,
                                                        willClose: () => {
                                                            window.location.href = '$dashboard';
                                                        }
                                                    });

                                                    // Fallback redirect in case willClose doesn't fire
                                                    setTimeout(() => {
                                                        window.location.href = '$dashboard';
                                                    }, 2000);
                                                }
                                            });
                                    });
                                    </script>";
                            } else {
                                // Failed login - record attempt
                                $loginSecurity->recordFailedAttempt($id);
                                $lockoutStatus = $loginSecurity->checkLockout($id);

                                if ($loginType === 'student') {
                                    logActivity($conn, $user['student_id'], 'student', 'FAILED_LOGIN', "failed logged in attempt to the system. {$lockoutStatus['remaining_attempts']} attempt(s) remaining.");
                                } else {
                                    logActivity($conn, $user['teacher_id'], 'teacher', 'FAILED_LOGIN', "failed logged in attempt to the system. {$lockoutStatus['remaining_attempts']} attempt(s) remaining.");
                                }


                                $errors[] = "Incorrect ID/Username or password. {$lockoutStatus['remaining_attempts']} attempt(s) remaining.";
                            }
                        } else {
                            // User not found - record attempt
                            $loginSecurity->recordFailedAttempt($id);
                            $lockoutStatus = $loginSecurity->checkLockout($id);
                            $errors[] = "User not found. {$lockoutStatus['remaining_attempts']} attempt(s) remaining.";
                        }

                        $conn->close();
                    }

                    // Show errors if any
                    if (!empty($errors)) {
                        $errorMessages = implode('<br>', array_map('htmlspecialchars', $errors));
                        echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                html: '$errorMessages',
                                confirmButtonColor: '#0d6efd',
                                confirmButtonText: 'Try Again'
                            });
                        });
                        </script>";
                    }
                }
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: 'reCAPTCHA verification failed. Please try again.',
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'Try Again'
                        });
                    });
                    </script>";
            }
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Verification Required',
                    text: 'Please complete the reCAPTCHA verification.',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Try Again'
                });
            });
            </script>";
        }
    }
    ?>

    <!-- Redesigned Internet Status Notification -->
    <div class="connection-status" id="connectionStatus">
        <div class="status-card" id="statusCard">
            <button class="status-close" id="statusClose">
                <i class="bi bi-x"></i>
            </button>
            <div class="status-header">
                <div class="status-icon" id="statusIcon">
                    <i class="bi bi-wifi"></i>
                </div>
                <h3 class="status-title" id="statusTitle">Connection Restored</h3>
            </div>
            <div class="status-message" id="statusMessage">
                Your internet connection has been restored. You can now continue using the portal.
            </div>
            <div class="status-progress">
                <div class="status-progress-bar" id="statusProgressBar"></div>
            </div>
        </div>
    </div>

    <!-- Elegant Container -->
    <div class="elegant-container" id="authContainer">
        <!-- Offline Overlay -->
        <div class="offline-overlay" id="offlineOverlay">
            <i class="bi bi-wifi-off"></i>
            <h4>No Internet Connection</h4>
            <p>Please check your network connection and try again</p>
            <div class="spinner-border text-light mt-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Left Branding Panel -->
        <div class="branding-panel">
            <div class="branding-header">
                <h1><i class="bi bi-mortarboard-fill me-2"></i>SMATI</h1>
                <p>Education Portal - Secure Login System</p>

                <div class="academic-icons mt-4">
                    <div class="academic-icon">
                        <i class="bi bi-book"></i>
                        <div>Learning</div>
                    </div>
                    <div class="academic-icon">
                        <i class="bi bi-award"></i>
                        <div>Excellence</div>
                    </div>
                    <div class="academic-icon">
                        <i class="bi bi-people"></i>
                        <div>Community</div>
                    </div>
                </div>
            </div>

            <div class="branding-footer">
                <div class="admin-contact">
                    <p class="mb-1">Contact the administration office:</p>
                    <p class="mb-1"><i class="bi bi-telephone me-2"></i>(123) 456-7890</p>
                    <p class="mb-0"><i class="bi bi-envelope me-2"></i>admin@smati.edu</p>
                </div>
            </div>
        </div>

        <!-- Right Login Panel -->
        <div class="login-panel" id="loginPanel">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to access your educational resources</p>
            </div>

            <!-- User Type Selector -->
            <div class="user-type-selector">
                <button class="user-type-btn active" id="studentBtn" data-user-type="student">
                    <i class="bi bi-person-circle me-2"></i>Student Login
                </button>
                <button class="user-type-btn" id="teacherBtn" data-user-type="teacher">
                    <i class="bi bi-person-badge me-2"></i>Teacher Login
                </button>
            </div>



            <!-- Login Form -->
            <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="hidden" id="userType" name="user_type" value="student">
                <div class="mb-3 input-icon">
                    <label for="loginId" class="form-label" id="idLabel">ID # or Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                        <input type="text" class="form-control" id="loginId" name="loginId" placeholder="Enter your ID # or Username" required oncopy="return false" onpaste="return false">
                    </div>
                    <i class="bi bi-check-circle-fill validation-icon" id="loginIdValid"></i>
                    <i class="bi bi-exclamation-circle-fill validation-icon" id="loginIdInvalid"></i>
                    <div class="invalid-feedback" id="loginIdError"></div>
                </div>

                <div class="mb-3 position-relative input-icon">
                    <label for="loginPassword" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Enter your password" required oncopy="return false" onpaste="return false">
                        <span class="password-toggle" id="toggleLoginPassword">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                    <i class="bi bi-check-circle-fill validation-icon" id="loginPasswordValid"></i>
                    <i class="bi bi-exclamation-circle-fill validation-icon" id="loginPasswordInvalid"></i>
                    <div class="capslock-warning" id="loginCapsWarning">
                        <i class="bi bi-exclamation-triangle"></i> Caps Lock is on
                    </div>
                    <div class="invalid-feedback" id="loginPasswordError"></div>
                </div>

                <div class="remember-me-container mb-3">
                    <div class="form-check">
                        <div class="text-muted small">
                            <input class="form-check-input" type="checkbox" required>
                            By logging in, you agree that your IP address may be collected for security and fraud-prevention purposes.
                        </div>
                    </div>

                </div>

                <div class="g-recaptcha" data-sitekey="6LdxyvQrAAAAAMCDZVWlknaTzOMK_q6CT6Wx4min"></div>

                <div class="d-grid gap-2">
                    <button class="btn btn-smati btn-lg" id="loginBtn" type="submit" name="btnLogin">Sign In</button>
                </div>


                <div class="forgot-password">
                    <a href="#" id="showForgotPassword">Forgot Password?</a>
                </div>

                <div class="cooldown-timer" id="cooldownTimer">
                    <i class="bi bi-clock"></i> Please wait <span id="cooldownSeconds">30</span> seconds before trying again
                </div>
        </div>
    </div>

    </form>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="script1.js"></script>
    <script>
        <?php if (isset($lockoutStatus) && $lockoutStatus['locked']): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const remainingTime = <?php echo $lockoutStatus['remaining_time']; ?>;

                Swal.fire({
                    icon: 'warning',
                    title: 'Account Locked',
                    html: `Too many failed attempts.<br>Please wait <b></b> to try again.`,
                    timer: remainingTime * 1000,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        const b = Swal.getHtmlContainer().querySelector('b');
                        const startTime = Date.now();
                        const endTime = startTime + (remainingTime * 1000);

                        const timerInterval = setInterval(() => {
                            const now = Date.now();
                            const timeLeft = Math.max(0, endTime - now);
                            const secondsLeft = Math.ceil(timeLeft / 1000);

                            const mins = Math.floor(secondsLeft / 60);
                            const secs = secondsLeft % 60;
                            b.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;

                            if (timeLeft <= 0) {
                                clearInterval(timerInterval);
                            }
                        }, 100);

                        // Store interval for cleanup
                        Swal.getTimerInterval = () => timerInterval;
                    },
                    willClose: () => {
                        // Clear interval
                        if (Swal.getTimerInterval) {
                            clearInterval(Swal.getTimerInterval());
                        }
                        Swal.fire({
                            icon: 'info',
                            title: 'Lockout Expired',
                            text: 'You can now try logging in again.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            });
        <?php endif; ?>
    </script>
</body>

</html>