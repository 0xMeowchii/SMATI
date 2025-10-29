<?php
require_once 'includes/session.php';
include('../database.php');
include '../includes/activity_logger.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header-login.php' ?>
</head>

<body>
    <?php
    $errors = [];
    $showSuccess = false;

    // LOGIN QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
        $conn = connectToDB();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

            $secret_key = "6LdxyvQrAAAAAFje30yKm8Zyt_d3lrJv7wjzcZP1";
            $captcha_response = $_POST['g-recaptcha-response'] ?? '';

            $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
            $response_data = json_decode($verify_response);

            if ($response_data->success) {
                // Validate inputs
                if (empty($email)) {
                    $errors[] = "email is required.";
                }

                if (empty($password)) {
                    $errors[] = "Password is required.";
                }

                if (empty($errors)) {

                    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();

                    if ($user) {
                        if (password_verify($password, $user['password'])) {

                            $_SESSION['id'] = $user['admin_id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['user_type'] = 'admin';

                            $date = new DateTime();
                            $_SESSION['last_login'] = $date->format('m-d-Y h:i A');

                            logActivity($conn, $user['admin_id'], 'admin', 'LOGIN', "logged in to the system.");

                            $showSuccess = true;
                        } else {
                            // Failed login
                            $errors[] = "Invalid email or password.";
                        }
                    } else {
                        $errors[] = "Invalid email or password.";
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


    //CREATE ACCOUNT
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) &&  $_POST['form_action'] === "create_account") {
        $conn = connectToDB();
        $username = 'admin';
        $security_question = $_POST['security_question'];
        $security_answer = $_POST['security_answer'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $authKey = $_POST['authKey'];
        $authPIN = $_POST['authPIN'];
        $authMethod = $_POST['authMethod'];
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

            $secret_key = "6LdxyvQrAAAAAFje30yKm8Zyt_d3lrJv7wjzcZP1";
            $captcha_response = $_POST['g-recaptcha-response'] ?? '';

            $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
            $response_data = json_decode($verify_response);

            if ($response_data->success) {

                if ($conn) {
                    // Check if email already exists
                    $checkStmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
                    $checkStmt->bind_param("s", $email);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();

                    if ($result->num_rows > 0) {
                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Email already exists!',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
                    } else {

                        if ($authMethod === 'password') {

                            $stmt = $conn->prepare("SELECT * FROM auth WHERE password = ?");
                            $stmt->bind_param("s", $authKey);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $Loggedin = $result->fetch_assoc();

                            if ($Loggedin) {

                                $stmt = $conn->prepare("INSERT INTO admin (username, email, security_question, security_answer, password) 
                                VALUES (?, ?, ?, ?, ?)");
                                $stmt->bind_param("sssss", $username, $email, $security_question, $security_answer, $password);

                                if ($stmt->execute()) {
                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success!',
                                                    text: 'Account Created Successfully!',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                }).then(() => {
                                                    window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                                                });
                                            });
                                        </script>";
                                } else {

                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error!',
                                                    text: '" . addslashes($stmt->error) . "',
                                                    confirmButtonColor: '#d33'
                                                });
                                            });
                                        </script>";
                                }
                                $stmt->close();
                            } else {

                                echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Verification Failed',
                                        text: 'Invalid Authentication Key.',
                                        confirmButtonColor: '#0d6efd',
                                        confirmButtonText: 'Try Again'
                                    });
                                });
                            </script>";
                            }
                        } else {

                            $stmt = $conn->prepare("SELECT * FROM auth WHERE pin = ?");
                            $stmt->bind_param("s", $authPIN);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $Loggedin = $result->fetch_assoc();

                            if ($Loggedin) {

                                $stmt = $conn->prepare("INSERT INTO admin (username, email, security_question, security_answer, password) 
                                VALUES (?, ?, ?, ?, ?)");
                                $stmt->bind_param("sssss", $username, $email, $security_question, $security_answer, $password);

                                if ($stmt->execute()) {
                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success!',
                                                    text: 'Account Created Successfully!',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                }).then(() => {
                                                    window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                                                });
                                            });
                                        </script>";
                                } else {
                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error!',
                                                    text: '" . addslashes($stmt->error) . "',
                                                    confirmButtonColor: '#d33'
                                                });
                                            });
                                        </script>";
                                }
                                $stmt->close();
                            } else {

                                echo "<script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Verification Failed',
                                                text: 'Invalid PIN.',
                                                confirmButtonColor: '#0d6efd',
                                                confirmButtonText: 'Try Again'
                                            });
                                        });
                                    </script>";
                            }
                        }
                    }

                    $checkStmt->close();
                    $conn->close();
                } else {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Database connection failed',
                                confirmButtonColor: '#d33'
                            });
                        });
                    </script>";
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

    //RECOVER PASSWORD
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) && $_POST['form_action'] === "recover_password") {
        $conn = connectToDB();
        $security_question = $_POST['forgot_question'];
        $security_answer = $_POST['forgot_answer'];
        $email = $_POST['forgot_email'];
        $authKey = $_POST['authKey'];
        $authPIN = $_POST['authPIN'];
        $authMethod = $_POST['authMethod'];
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

            $secret_key = "6LdxyvQrAAAAAFje30yKm8Zyt_d3lrJv7wjzcZP1";
            $captcha_response = $_POST['g-recaptcha-response'] ?? '';

            $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
            $response_data = json_decode($verify_response);

            if ($response_data->success) {

                if ($conn) {

                    if ($authMethod === 'password') {

                        $stmt = $conn->prepare("SELECT * FROM auth WHERE password = ?");
                        $stmt->bind_param("s", $authKey);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $Loggedin = $result->fetch_assoc();

                        if ($Loggedin) {
                            // Check if email and security credentials match
                            $checkStmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND security_question = ? AND security_answer = ?");
                            $checkStmt->bind_param("sss", $email, $security_question, $security_answer);
                            $checkStmt->execute();
                            $result = $checkStmt->get_result();

                            if ($result->num_rows > 0) {
                                // Generate random password
                                $random_password = generateRandomPassword(12);

                                // Hash the password (recommended for security)
                                $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);

                                // Update password in database
                                $updateStmt = $conn->prepare("UPDATE admin SET password = ?, confirm_password = ? WHERE email = ?");
                                $updateStmt->bind_param("sss", $hashed_password, $hashed_password, $email);

                                if ($updateStmt->execute()) {

                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success!',
                                                    text: 'Password Reset Successfully!',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                }).then(() => {
                                                    // Generate and download PDF
                                                    generatePasswordPDF('" . addslashes($email) . "', '" . addslashes($random_password) . "');
                                                });
                                            });
                                        </script>";
                                } else {

                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error!',
                                                    text: '" . addslashes($updateStmt->error) . "',
                                                    confirmButtonColor: '#d33'
                                                });
                                            });
                                        </script>";
                                }

                                $updateStmt->close();
                            } else {

                                echo "<script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                text: 'Invalid Email or Security Question/Answer!',
                                                confirmButtonColor: '#d33'
                                            });
                                        });
                                    </script>";
                            }

                            $checkStmt->close();
                            $conn->close();
                        } else {

                            echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Verification Failed',
                                            text: 'Invalid Authentication Key.',
                                            confirmButtonColor: '#0d6efd',
                                            confirmButtonText: 'Try Again'
                                        });
                                    });
                                </script>";
                        }
                    } else {

                        $stmt = $conn->prepare("SELECT * FROM auth WHERE pin = ?");
                        $stmt->bind_param("s", $authPIN);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $Loggedin = $result->fetch_assoc();

                        if ($Loggedin) {
                            // Check if email and security credentials match
                            $checkStmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND security_question = ? AND security_answer = ?");
                            $checkStmt->bind_param("sss", $email, $security_question, $security_answer);
                            $checkStmt->execute();
                            $result = $checkStmt->get_result();

                            if ($result->num_rows > 0) {
                                // Generate random password
                                $random_password = generateRandomPassword(12);

                                // Hash the password (recommended for security)
                                $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);

                                // Update password in database
                                $updateStmt = $conn->prepare("UPDATE admin SET password = ?, confirm_password = ? WHERE email = ?");
                                $updateStmt->bind_param("sss", $hashed_password, $hashed_password, $email);

                                if ($updateStmt->execute()) {

                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success!',
                                                    text: 'Password Reset Successfully!',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                }).then(() => {
                                                    // Generate and download PDF
                                                    generatePasswordPDF('" . addslashes($email) . "', '" . addslashes($random_password) . "');
                                                });
                                            });
                                        </script>";
                                } else {

                                    echo "<script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error!',
                                                    text: '" . addslashes($updateStmt->error) . "',
                                                    confirmButtonColor: '#d33'
                                                });
                                            });
                                        </script>";
                                }

                                $updateStmt->close();
                            } else {

                                echo "<script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                text: 'Invalid Email or Security Question/Answer!',
                                                confirmButtonColor: '#d33'
                                            });
                                        });
                                    </script>";
                            }

                            $checkStmt->close();
                            $conn->close();
                        } else {

                            echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Verification Failed',
                                            text: 'Invalid PIN.',
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
                                    title: 'Error!',
                                    text: 'Database connection failed',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
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

    // Function to generate random password
    function generateRandomPassword($length = 12)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*';

        $all = $uppercase . $lowercase . $numbers . $special;
        $password = '';

        // Ensure at least one of each type
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Fill the rest randomly
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    ?>


    <!-- AUTHENTICATION MODAL -->
    <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Authentication Required</h2>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-check text-primary" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">SMATI Authentication</h4>
                        <p class="text-muted">Chooses your authentication method to proceed.</p>
                    </div>

                    <div class="col-12">
                        <ul class="nav nav-tabs nav-justified" id="auth" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="password-tab" type="button" onclick="switchAuthMethod('password')">Authentication Key</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pin-tab" type="button" onclick="switchAuthMethod('pin')">PIN</button>
                            </li>
                        </ul>
                    </div>

                    <form id="authForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">

                        <input type="hidden" id="form_action" name="form_action" value="">
                        <input type="hidden" id="email" name="email">
                        <input type="hidden" id="password" name="password">
                        <input type="hidden" id="question" name="security_question">
                        <input type="hidden" id="answer" name="security_answer">
                        <input type="hidden" id="forgotQuestion" name="forgot_question">
                        <input type="hidden" id="forgotAnswer" name="forgot_answer">
                        <input type="hidden" id="forgotEmail" name="forgot_email">
                        <input type="hidden" id="authMethod" name="authMethod" value="password">
                        <input type="hidden" id="recaptchaResponse" name="g-recaptcha-response">

                        <!-- Authentication Key Section -->
                        <div class="d-block" id="authPassword">
                            <label class="form-label">SMATI Authentication Key</label>
                            <div class="input-group">
                                <input type="password" class="form-control" placeholder="Enter SMATI Key" id="authKey" name="authKey">
                                <span class="input-group-text"
                                    onmousedown="document.getElementById('authKey').type='text'"
                                    onmouseup="document.getElementById('authKey').type='password'"
                                    onmouseleave="document.getElementById('authKey').type='password'">
                                    <i class="bi bi-eye"></i></span>
                            </div>
                        </div>

                        <!-- PIN Section -->
                        <div class="d-none" id="authPIN">
                            <label class="form-label text-center">Enter 6-digit PIN</label>
                            <input type="password"
                                class="form-control otp-input"
                                maxlength="6"
                                placeholder="000000"
                                name="authPIN"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Authenticate</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="landscape-container" id="authContainer">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <div class="logo-container">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h2>SMATI Authentication</h2>
                <p>St. Michael Arcangel Technological Institute, Inc.</p>
                <p>Secure access to your academic resources and institutional services.</p>

                <div class="features-list">
                    <div class="feature-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Advanced security with CAPTCHA verification</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-clock"></i>
                        <span>Real-time form validation</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-key"></i>
                        <span>Secure password recovery</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-person-check"></i>
                        <span>Multi-step registration process</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authentication Section -->
        <div class="auth-section">
            <div class="auth-header">
                <h3>Account Access</h3>
                <p>Sign in to your account or create a new one</p>
            </div>

            <!-- Tabs for Login and Register -->
            <ul class="nav nav-tabs nav-justified" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
                </li>
            </ul>

            <div class="tab-content" id="authTabsContent">
                <!-- Login Tab -->
                <div class="tab-pane fade show active" role="tabpanel" id="login">
                    <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                        <div class="floating-label">
                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder=" " required>
                            <label for="loginEmail">Email address</label>
                            <i class="bi bi-check-circle-fill validation-icon" id="loginEmailValid"></i>
                            <i class="bi bi-exclamation-circle-fill validation-icon" id="loginEmailInvalid"></i>
                        </div>

                        <div class="floating-label position-relative">
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder=" " required>
                            <label for="loginPassword">Password</label>
                            <span class="password-toggle" id="toggleLoginPassword">
                                <i class="bi bi-eye"></i>
                            </span>
                            <i class="bi bi-check-circle-fill validation-icon" id="loginPasswordValid"></i>
                            <i class="bi bi-exclamation-circle-fill validation-icon" id="loginPasswordInvalid"></i>
                            <div class="capslock-warning" id="loginCapsWarning">
                                <i class="bi bi-exclamation-triangle"></i> Caps Lock is on
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <a href="#" id="showForgotPassword">Forgot Password?</a>
                        </div>

                        <!-- CAPTCHA for Login -->
                        <div class="captcha-container">
                            <div class="g-recaptcha" data-sitekey="6LdxyvQrAAAAAMCDZVWlknaTzOMK_q6CT6Wx4min"></div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-smati btn-lg" name="btnLogin">Sign In</button>
                        </div>
                    </form>
                </div>

                <!-- Register Tab -->
                <div class="tab-pane fade" id="register" role="tabpanel">
                    <form id="registerForm1" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                        <div class="step-indicator">
                            <div class="step active" id="step1">1</div>
                            <div class="step" id="step2">2</div>
                            <div class="step" id="step3">3</div>
                        </div>

                        <!-- Step 1: Security Questions -->
                        <div class="form-section active" id="registerStep1">
                            <h5 class="mb-4 text-center">Security Verification</h5>
                            <div class="mb-4">
                                <label for="securityQuestion" class="form-label fw-bold">Security Question</label>
                                <select class="form-select" id="securityQuestion" required>
                                    <option value="" selected disabled>Select a security question</option>
                                    <option value="friend">Who is your best friend?</option>
                                    <option value="birthplace">Where is your place of birth?</option>
                                    <option value="color">What is your favorite color?</option>
                                </select>
                            </div>
                            <div class="floating-label">
                                <input type="text" class="form-control" id="securityAnswer" placeholder=" " required>
                                <label for="securityAnswer">Your Answer</label>
                                <i class="bi bi-check-circle-fill validation-icon" id="securityAnswerValid"></i>
                                <i class="bi bi-exclamation-circle-fill validation-icon" id="securityAnswerInvalid"></i>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="button" class="btn btn-smati" id="toStep2">Next</button>
                            </div>
                        </div>

                        <!-- Step 2: Account Details -->
                        <div class="form-section" id="registerStep2">
                            <h5 class="mb-4 text-center">Account Information</h5>
                            <div class="floating-label">
                                <input type="email" class="form-control" id="registerEmail" placeholder=" " required>
                                <label for="registerEmail">Email address</label>
                                <i class="bi bi-check-circle-fill validation-icon" id="registerEmailValid"></i>
                                <i class="bi bi-exclamation-circle-fill validation-icon" id="registerEmailInvalid"></i>
                            </div>

                            <div class="floating-label position-relative">
                                <input type="password" class="form-control" id="registerPassword" placeholder=" " required>
                                <label for="registerPassword">Password</label>
                                <span class="password-toggle1" id="toggleRegisterPassword">
                                    <i class="bi bi-eye"></i>
                                </span>
                                <i class="bi bi-check-circle-fill validation-icon1" id="registerPasswordValid"></i>
                                <i class="bi bi-exclamation-circle-fill validation-icon1" id="registerPasswordInvalid"></i>
                                <div class="capslock-warning" id="registerCapsWarning">
                                    <i class="bi bi-exclamation-triangle"></i> Caps Lock is on
                                </div>
                                <div class="password-strength mt-2">
                                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                                </div>
                                <div class="form-text">Must be at least 8 characters with uppercase, lowercase, and numbers</div>
                            </div>

                            <div class="floating-label position-relative">
                                <input type="password" class="form-control" id="confirmPassword" placeholder=" " required>
                                <label for="confirmPassword">Confirm Password</label>
                                <span class="password-toggle" id="toggleConfirmPassword">
                                    <i class="bi bi-eye"></i>
                                </span>
                                <i class="bi bi-check-circle-fill validation-icon" id="confirmPasswordValid"></i>
                                <i class="bi bi-exclamation-circle-fill validation-icon" id="confirmPasswordInvalid"></i>
                            </div>

                            <div class="d-grid gap-2 gap-md-0 d-md-flex mt-4">
                                <button type="button" class="btn btn-outline-blue me-md-2" id="backToStep1">Back</button>
                                <button type="button" class="btn btn-smati" id="toStep3">Next</button>
                            </div>
                        </div>

                        <!-- Step 3: Finalize Registration -->
                        <div class="form-section" id="registerStep3">
                            <h5 class="mb-4 text-center">Review Information</h5>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <p><strong>Email:</strong> <span id="reviewEmail"></span></p>
                                    <p><strong>Security Question:</strong> <span id="reviewQuestion"></span></p>
                                </div>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="termsAgree" name="terms_agree" required>
                                <label class="form-check-label" for="termsAgree">
                                    I agree to the <a href="#">Terms and Conditions</a>
                                </label>
                                <div class="captcha-container mt-4">
                                    <div class="g-recaptcha" id="registerCaptcha" data-sitekey="6LdxyvQrAAAAAMCDZVWlknaTzOMK_q6CT6Wx4min"
                                        data-callback="onRegisterCaptchaSuccess"></div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 gap-md-0 d-md-flex">
                                <button type="button" class="btn btn-outline-blue me-md-2" id="backToStep2">Back</button>
                                <button type="submit" class="btn btn-smati" name="btnCreate">Create Account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Forgot Password Form (initially hidden) -->
            <div class="form-section" id="forgotPasswordForm">
                <h5 class="mb-4 text-center">Password Recovery</h5>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Enter your email and security question to reset your password.
                </div>
                <form id="forgotPasswordFormInner1" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <div class="floating-label">
                        <input type="email" class="form-control" id="recoveryEmail" placeholder=" " required>
                        <label for="recoveryEmail">Email address</label>
                        <i class="bi bi-check-circle-fill validation-icon" id="recoveryEmailValid"></i>
                        <i class="bi bi-exclamation-circle-fill validation-icon" id="recoveryEmailInvalid"></i>
                    </div>

                    <div class="mb-4">
                        <label for="recoveryQuestion" class="form-label fw-bold">Security Question</label>
                        <select class="form-select" id="recoveryQuestion" required>
                            <option value="" selected disabled>Select your security question</option>
                            <option value="friend">Who is your best friend?</option>
                            <option value="birthplace">Where is your place of birth?</option>
                            <option value="color">What is your favorite color?</option>
                        </select>
                    </div>

                    <div class="floating-label">
                        <input type="text" class="form-control" id="recoveryAnswer" placeholder=" " required>
                        <label for="recoveryAnswer">Your Answer</label>
                        <i class="bi bi-check-circle-fill validation-icon" id="recoveryAnswerValid"></i>
                        <i class="bi bi-exclamation-circle-fill validation-icon" id="recoveryAnswerInvalid"></i>
                    </div>

                    <div class="captcha-container mt-4">
                        <div class="g-recaptcha" id="forgotCaptcha" data-sitekey="6LdxyvQrAAAAAMCDZVWlknaTzOMK_q6CT6Wx4min"
                            data-callback="onForgotCaptchaSuccess"></div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-smati" id="recoveryButton">Recover Password</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none fw-bold" id="backToLoginFromForgot">Back to Login</a>
                </div>
            </div>



            <div class="footer-links">
                <a href="#">Contact Support</a>
                <p>|</p>
                <p>v1.0.0</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- jsPDF Autotable plugin for better table formatting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="login.js"></script>
    <script>
        function generatePasswordPDF(email, password) {
            // Create new jsPDF instance
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            // Set document properties
            doc.setProperties({
                title: 'Password Recovery',
                subject: 'New Password',
                author: 'Admin System',
                keywords: 'password, recovery',
                creator: 'Admin System'
            });

            // Add title
            doc.setFontSize(20);
            doc.setFont(undefined, 'bold');
            doc.text('Password Recovery', 105, 20, {
                align: 'center'
            });

            // Add horizontal line
            doc.setLineWidth(0.5);
            doc.line(20, 25, 190, 25);

            // Add email
            doc.setFontSize(12);
            doc.setFont(undefined, 'normal');
            doc.text('Email:', 20, 40);
            doc.setFont(undefined, 'bold');
            doc.text(email, 45, 40);

            // Add new password
            doc.setFont(undefined, 'normal');
            doc.text('New Password:', 20, 55);
            doc.setFontSize(14);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(220, 38, 38); // Red color for emphasis
            doc.text(password, 20, 65);

            // Add warning message
            doc.setFontSize(10);
            doc.setFont(undefined, 'italic');
            doc.setTextColor(0, 0, 0); // Back to black
            const warningText = 'IMPORTANT: Please keep this password secure and change it after logging in. Delete this file after you have saved your password.';
            const splitWarning = doc.splitTextToSize(warningText, 170);
            doc.text(splitWarning, 20, 85);

            // Add footer
            doc.setFontSize(8);
            doc.setFont(undefined, 'normal');
            doc.setTextColor(128, 128, 128); // Gray color
            const date = new Date().toLocaleString();
            doc.text('Generated on: ' + date, 105, 280, {
                align: 'center'
            });

            // Save the PDF
            doc.save('password_recovery_' + Date.now() + '.pdf');

            // Optional: Redirect after download
            setTimeout(function() {
                window.location.href = window.location.pathname;
            }, 1000);
        }

        function switchAuthMethod(method) {
            const passwordTab = document.getElementById('password-tab');
            const pinTab = document.getElementById('pin-tab');
            const authPassword = document.getElementById('authPassword');
            const authPIN = document.getElementById('authPIN');
            const authMethod = document.getElementById('authMethod');

            if (method === 'password') {
                passwordTab.classList.add('active');
                pinTab.classList.remove('active');
                authPassword.classList.remove('d-none');
                authPassword.classList.add('d-block');
                authPIN.classList.remove('d-block');
                authPIN.classList.add('d-none');
                authMethod.value = 'password';

            } else if (method === 'pin') {
                pinTab.classList.add('active');
                passwordTab.classList.remove('active');
                authPIN.classList.remove('d-none');
                authPIN.classList.add('d-block');
                authPassword.classList.remove('d-block');
                authPassword.classList.add('d-none');
                authMethod.value = 'pin';
            }
        }

        // Global variable to store the register captcha response
        let registerCaptchaResponse = '';
        let forgotCaptchaResponse = '';


        // reCAPTCHA callback functions
        function onRegisterCaptchaSuccess(response) {
            registerCaptchaResponse = response;
        }

        function onForgotCaptchaSuccess(response) {
            forgotCaptchaResponse = response;
        }

        document.addEventListener('DOMContentLoaded', function() {


            const registerForm = document.getElementById('registerForm1');
            const forgotForm = document.getElementById('forgotPasswordFormInner1');
            const authForm = document.getElementById('authForm');
            const authModal = new bootstrap.Modal(document.getElementById('authModal'));
            const pinInput = document.querySelector('input[name="authPIN"]');

            // Prevent non-numeric input
            pinInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Also prevent paste of non-numeric content
            pinInput.addEventListener('paste', function(e) {
                const pasteData = e.clipboardData.getData('text');
                if (!/^\d+$/.test(pasteData)) {
                    e.preventDefault();
                }
            });



            registerForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const securityQuestions = document.getElementById('securityQuestion').value;
                const securityAnswer = document.getElementById('securityAnswer').value;
                const registerEmail = document.getElementById('registerEmail').value;
                const registerPassword = document.getElementById('registerPassword').value;
                const recaptchaResponse = grecaptcha.getResponse();

                document.getElementById('email').value = registerEmail
                document.getElementById('password').value = registerPassword
                document.getElementById('question').value = securityQuestions
                document.getElementById('answer').value = securityAnswer
                document.getElementById('form_action').value = "create_account"
                document.getElementById('recaptchaResponse').value = registerCaptchaResponse;

                authModal.show();

            });

            forgotForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const recoveryEmail = document.getElementById('recoveryEmail').value;
                const recoveryQuestion = document.getElementById('recoveryQuestion').value;
                const recoveryAnswer = document.getElementById('recoveryAnswer').value;
                const recaptchaResponse = grecaptcha.getResponse();

                document.getElementById('forgotEmail').value = recoveryEmail
                document.getElementById('forgotQuestion').value = recoveryQuestion
                document.getElementById('forgotAnswer').value = recoveryAnswer
                document.getElementById('form_action').value = "recover_password"
                document.getElementById('recaptchaResponse').value = forgotCaptchaResponse;

                authModal.show();

            });

            <?php if ($showSuccess): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful!',
                    text: 'Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!',
                    showConfirmButton: false,
                    timer: 1500,
                    willClose: () => {
                        window.location.href = 'admin-dashboard.php';
                    }
                });

            <?php elseif (!empty($errors)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    html: '<?php echo implode("<br>", array_map('addslashes', $errors)); ?>',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Try Again'
                });
            <?php endif; ?>
        });
    </script>

</body>

</html>