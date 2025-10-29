<?php
require_once 'includes/session.php';
include('../database.php');
date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMATI-REGISTRAR | Secure Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google reCAPTCHA API -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>
    <?php
    $errors = [];
    $showSuccess = false;

    // LOGIN QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
        $conn = connectToDB();
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

            $secret_key = "6LdxyvQrAAAAAFje30yKm8Zyt_d3lrJv7wjzcZP1";
            $captcha_response = $_POST['g-recaptcha-response'] ?? '';

            $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
            $response_data = json_decode($verify_response);

            if ($response_data->success) {
                // Validate inputs
                if (empty($username)) {
                    $errors[] = "username is required.";
                }

                if (empty($password)) {
                    $errors[] = "Password is required.";
                }

                if (empty($errors)) {

                    $stmt = $conn->prepare("SELECT * FROM registrars WHERE username = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();

                    if ($user) {
                        if (password_verify($password, $user['password'])) {

                            $_SESSION['id'] = $user['registrar_id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['user_type'] = 'registrar';

                            $showSuccess = true;
                        } else {
                            // Failed login
                            $errors[] = "Invalid username or password.";
                        }
                    } else {
                        $errors[] = "Invalid username or password.";
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
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-university"></i>
                </div>
                <div class="system-name">SMATI-REGISTRAR</div>
                <div class="system-role">Administrative Portal</div>
            </div>
            <div class="secure-badge">
                <i class="fas fa-shield-alt"></i> Secure Access Required
            </div>

            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-database feature-icon"></i>
                    <div class="feature-text">Secure Database Access</div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-user-shield feature-icon"></i>
                    <div class="feature-text">Role-based Authorization</div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <div class="feature-text">Advanced Analytics</div>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="login-body">
            <h4 class="login-title">Authorized Access Only</h4>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                        <span class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- Enhanced CAPTCHA Container -->
                <div class="captcha-container">
                    <div class="captcha-header">
                        <i class="fas fa-shield-alt captcha-icon"></i>
                        <span class="captcha-title">Security Verification</span>
                    </div>
                    <p class="captcha-description">Please verify you're not a robot</p>
                    <div class="g-recaptcha" data-sitekey="6LdxyvQrAAAAAMCDZVWlknaTzOMK_q6CT6Wx4min"></div>
                    <div class="captcha-footer">
                        <i class="fas fa-info-circle"></i>
                        <span>This helps prevent automated access</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-login" id="loginBtn" name="btnLogin">
                    <i class="fas fa-sign-in-alt"></i> Access System
                </button>

                <div class="forgot-password">
                    <a class="forgot-link" id="forgotPassword">Forgot your password?</a>
                </div>
            </form>

            <!-- Security Notice -->
            <div class="security-notice">
                <i class="fas fa-info-circle"></i>
                <div class="security-text">
                    <strong>Security Notice:</strong> This system contains confidential registrar information.
                    Unauthorized access is prohibited and may be subject to legal action.
                </div>
            </div>

            <!-- Footer Note -->
            <div class="footer-note">
                <i class="fas fa-copyright"></i> SMATI Registrar System v1.0.0
            </div>
        </div>

        <!-- Cooldown Overlay -->
        <div id="cooldownOverlay" class="cooldown-overlay" style="display: none;">
            <div class="cooldown-timer" id="cooldownTimer">03:00</div>
            <div class="cooldown-message">
                <h4>Security Lockout</h4>
                <p class="text-muted">Multiple failed login attempts detected. System temporarily locked.</p>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal-overlay" id="forgotModal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h3 class="modal-title">Password Assistance</h3>
            <p class="modal-text">
                For security reasons, password resets must be processed by the system administrator.
                Please contact the admin using the information below.
            </p>

            <div class="admin-contact">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="contact-details">
                        <div class="contact-label">System Administrator</div>
                        <div class="contact-value">Mr. James Wilson</div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <div class="contact-label">Email Address</div>
                        <div class="contact-value">admin@smati-registrar.edu</div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <div class="contact-label">Office Phone</div>
                        <div class="contact-value">+1 (555) 123-4567</div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <div class="contact-label">Office Location</div>
                        <div class="contact-value">IT Department, Building A, Room 205</div>
                    </div>
                </div>
            </div>

            <button class="btn-modal" id="closeModal">Understood</button>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="script.js"></script>
    <script>
        <?php if ($showSuccess): ?>
            Swal.fire({
                icon: 'success',
                title: 'Login Successful!',
                text: 'Welcome back, Registrar!',
                showConfirmButton: false,
                timer: 1500,
                willClose: () => {
                    window.location.href = 'registrar-dashboard.php';
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
    </script>
</body>

</html>