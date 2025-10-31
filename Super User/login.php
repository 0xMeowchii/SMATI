<?php
include '../database.php';
session_name('superuser');
session_start();

$errors = [];
$showSuccess = false;

// LOGIN QUERY
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
    $method = $_POST['method'];
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $pin = $_POST['pin'];

    if (empty($errors)) {
        $conn = connectToDB();
        if ($method === 'password') {
            $stmt = $conn->prepare("SELECT * FROM super_user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                if ($password === $user['password']) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_type'] = 'superuser';

                    $showSuccess = true;
                } else {
                    $errors[] = "Invalid email or password.";
                }
            }
        } else {
            $stmt = $conn->prepare("SELECT * FROM super_user WHERE pin = ?");
            $stmt->bind_param("s", $pin);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                $_SESSION['logged_in'] = true;
                $showSuccess = true;
            } else {
                 $errors[] = "Invalid PIN.";
            }
        }

        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PixelDev | Super User Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google reCAPTCHA API -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="brand-logo">
                <i class="fas fa-code"></i>
                PixelDev
            </div>
            <p class="tagline">Super User Portal</p>
            <div class="super-user-badge">
                <i class="fas fa-star"></i> Privileged Access
            </div>
        </div>

        <!-- Body -->
        <div class="login-body">
            <h4 class="login-title">Secure Authentication</h4>
            <!-- Authentication Method Selector -->
            <div class="auth-method-selector">
                <span class="auth-method-label">Select Authentication Method:</span>
                <div class="auth-radio-group">
                    <div class="auth-radio-option">
                        <input class="auth-radio-input" type="radio" id="passwordRadio" name="authMethod" value="password" checked>
                        <label class="auth-radio-label" for="passwordRadio">
                            <i class="fas fa-key auth-radio-icon"></i>
                            <span class="auth-radio-text">Password</span>
                        </label>
                    </div>
                    <div class="auth-radio-option">
                        <input class="auth-radio-input" type="radio" id="pinRadio" name="authMethod" value="pin">
                        <label class="auth-radio-label" for="pinRadio">
                            <i class="fas fa-lock auth-radio-icon"></i>
                            <span class="auth-radio-text">PIN</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Password Login Form -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <div id="passwordForm">
                    <div class="mb-4 input-with-validation">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="superadmin@dev.com">
                        </div>
                        <div class="validation-feedback" id="emailValidation"></div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                            <span class="input-group-text password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="validation-feedback" id="passwordValidation"></div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                </div>

                <!-- PIN Login Form -->
                <div id="pinForm" style="display: none;">
                    <div class="mb-4">
                        <label class="form-label">Enter your 6-digit PIN</label>
                        <input type="hidden" id="pinInput" name="pin" value="">
                        <input type="text" class="pin-display" id="pinDisplay" maxlength="6" readonly>
                        <div class="keyboard-notification" id="keyboardNotification" style="display: none;">
                            <i class="fas fa-keyboard"></i>
                            <span>You can also enter PIN using your keyboard</span>
                        </div>

                        <div class="numpad-container">
                            <div class="numpad-btn" data-value="1">1</div>
                            <div class="numpad-btn" data-value="2">2</div>
                            <div class="numpad-btn" data-value="3">3</div>
                            <div class="numpad-btn" data-value="4">4</div>
                            <div class="numpad-btn" data-value="5">5</div>
                            <div class="numpad-btn" data-value="6">6</div>
                            <div class="numpad-btn" data-value="7">7</div>
                            <div class="numpad-btn" data-value="8">8</div>
                            <div class="numpad-btn" data-value="9">9</div>
                            <div class="numpad-btn" data-value="clear">C</div>
                            <div class="numpad-btn" data-value="0">0</div>
                            <div class="numpad-btn" data-value="backspace"><i class="fas fa-backspace"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced CAPTCHA Container -->
                <div class="captcha-container">
                    <div class="captcha-header">
                        <i class="fas fa-shield-alt captcha-icon"></i>
                        <span class="captcha-title">Security Verification</span>
                    </div>
                    <p class="captcha-description">Please verify you're not a robot</p>
                    <div class="g-recaptcha" data-sitekey="6LcU8_MrAAAAAOoYAXm9WHudb0FukJ8y2SH3M4fA"></div>
                    <div class="captcha-footer">
                        <i class="fas fa-info-circle"></i>
                        <span>This helps prevent automated access</span>
                    </div>
                </div>

                <!-- Login Button -->
                <button class="btn btn-login mb-3" id="loginBtn" name="btnLogin">
                    <i class="fas fa-sign-in-alt"></i> Authenticate
                </button>
                <input type="hidden" id="method" name="method" value="password">
            </form>
        </div>


        <!-- Cooldown Overlay -->
        <div id="cooldownOverlay" class="cooldown-overlay" style="display: none;">
            <div class="cooldown-timer" id="cooldownTimer">03:00</div>
            <div class="cooldown-message">
                <h4 class="text-danger">Security Lockout</h4>
                <p class="text-muted">Too many failed authentication attempts. System will unlock automatically.</p>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($showSuccess): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful!',
                    text: 'Welcome back, Dev!',
                    showConfirmButton: false,
                    timer: 1500,
                    willClose: () => {
                        window.location.href = 'admin-list.php';
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