<?php
include 'database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
    $userType = $_POST['user_type'] ?? 'student';
    session_name($userType);
}

session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMATI - Education Portal Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Add reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="student-mode animated-background">

    <?php

    // Initialize variables
    $showSuccess = false;
    $errors = [];
    $dashboard = '';

    // LOGIN QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
        $id = $_POST['loginId'] ?? '';
        $password = $_POST['loginPassword'] ?? '';
        $userType = $_POST['user_type'] ?? 'student';

        // Check if reCAPTCHA response exists
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

            $secret_key = "6LdxyvQrAAAAAFje30yKm8Zyt_d3lrJv7wjzcZP1";
            $captcha_response = $_POST['g-recaptcha-response'] ?? '';

            $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
            $response_data = json_decode($verify_response);

            // FIX: Changed $response_data->$success to $response_data->success
            if ($response_data->success) {
                // Validate inputs
                if (empty($id)) {
                    $errors[] = "ID is required.";
                }

                if (empty($password)) {
                    $errors[] = "Password is required.";
                }

                if (empty($errors)) {
                    $conn = connectToDB();
                    if ($userType === 'student') {
                        // Student login query
                        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? OR username = ?");
                        $stmt->bind_param("is", $id , $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user = $result->fetch_assoc();

                        $id_field = 'student_id';
                        $dashboard = './Student/student-dashboard.php';
                    } else {
                        // Teacher login query
                        $stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ? OR username = ?");
                        $stmt->bind_param("is", $id, $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user = $result->fetch_assoc();

                        $id_field = 'teacher_id';
                        $dashboard = './Teacher/teacher-dashboard.php';
                    }

                    if ($user) {
                        if (password_verify($password, $user['password'])) {
                            $_SESSION['id'] = $user[$id_field];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['firstname'] = $user['firstname'];
                            $_SESSION['fullname'] = $user['lastname'] . ", " . $user['firstname'];
                            $_SESSION['user_type'] = $userType;
                            $_SESSION['logged_in'] = true;
                            $showSuccess = true;

                            echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Login Successful!',
                                    text: 'Welcome back, $userType!',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    willClose: () => {
                                        window.location.href = '$dashboard';
                                    }
                                });
                            });
                        </script>";
                        } else {
                            $errors[] = "Invalid ID or password.";
                        }
                    } else {
                        $errors[] = "Invalid ID or password.";
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
            } else {
                // reCAPTCHA verification failed
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
            // No reCAPTCHA response received
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
                    <label for="loginId" class="form-label" id="idLabel">Student ID or Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                        <input type="text" class="form-control" id="loginId" name="loginId" placeholder="Enter your ID or Username" required oncopy="return false" onpaste="return false">
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

                <div class="remember-me-container">
                    <input type="checkbox" id="rememberMe">
                    <label class="remember-me-label" for="rememberMe">
                        <span class="custom-checkbox"></span>
                        Remember my ID
                    </label>
                    <div class="remember-me-info">
                        <i class="bi bi-info-circle"></i> Your ID will be saved for faster login on this device
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

    <!-- Elegant Account Recovery Modal -->
    <div class="recovery-modal" id="recoveryModal">
        <div class="recovery-content">
            <div class="recovery-header">
                <h3>Account Recovery</h3>
                <div class="recovery-close" id="closeRecoveryModal">
                    <i class="bi bi-x"></i>
                </div>
            </div>
            <div class="recovery-body">
                <p class="text-center mb-4">Select your preferred recovery method</p>

                <div class="recovery-option" id="emailRecovery">
                    <div class="recovery-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="recovery-details">
                        <h5>Email Recovery</h5>
                        <p>Receive recovery instructions via email</p>
                    </div>
                </div>

                <div class="recovery-option" id="phoneRecovery">
                    <div class="recovery-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div class="recovery-details">
                        <h5>SMS Recovery</h5>
                        <p>Get a verification code via SMS</p>
                    </div>
                </div>

                <div class="recovery-option" id="adminRecovery">
                    <div class="recovery-icon">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div class="recovery-details">
                        <h5>Contact Administrator</h5>
                        <p>Direct assistance from the admin office</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="script1.js"></script>
</body>

</html>