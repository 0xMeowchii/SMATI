<?php
session_name('ADMIN');
session_start();
include('../database.php');
include '../includes/activity_logger.php';
date_default_timezone_set('Asia/Manila');

$errors = [];
$showSuccess = false;

// LOGIN QUERY
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $conn = connectToDB();

        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if ($password === $user['password']) {

                $_SESSION['id'] = $user['admin_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = 'admin';

                $date = new DateTime();
                $_SESSION['last_login'] = $date->format('m-d-Y h:i A');

                logActivity($conn, $user['admin_id'], 'admin', 'LOGIN', "logged in to the system.");

                $showSuccess = true;
            } else {
                // Failed login
                $errors[] = "Invalid username or password.";
            }
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMATI - Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        :root {
            --brand-blue: #0d6efd;
            --brand-blue-light: #3d8bfd;
            --brand-blue-dark: #0a58ca;
            --brand-blue-100: #cfe2ff;
            --brand-blue-600: #0a58ca;
            --brand-blue-800: #052c65;
        }

        body {
            background: linear-gradient(135deg, var(--brand-blue-light) 0%, var(--brand-blue) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            position: relative;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            margin-top: 20px;
        }

        .brand-bg {
            background-color: var(--brand-blue);
        }

        .brand-text {
            color: var(--brand-blue);
        }

        .btn-brand {
            background-color: var(--brand-blue);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-brand:hover {
            background-color: var(--brand-blue-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-blue-dark) 100%);
            color: white;
            text-align: center;
            padding: 25px;
            border-bottom: none;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .card-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }

        .input-group-text {
            background: white;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
        }

        .password-toggle {
            cursor: pointer;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: white;
            font-size: 0.8rem;
            width: 100%;
            padding: 0 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .logo-text {
                font-size: 1.5rem;
            }

            .card-header {
                padding: 20px;
            }

            .card-body {
                padding: 25px 20px;
            }
        }

        @media (max-width: 400px) {
            .logo-text {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <div class="logo-text">
                    <i class="bi bi-egg-fried"></i> ADMIN PORTAL
                </div>
                <p class="mb-0 mt-2">St. Michael Arcangel Technological Institute, Inc.</p>
            </div>


            <div class="card-body">
                <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <div class="mb-4">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" name="username" required autocomplete="off" placeholder="Enter your username">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password" required placeholder="Enter your password">
                            <span class="input-group-text password-toggle" id="passwordToggle"
                                onmousedown="document.getElementById('password').type='text'"
                                onmouseup="document.getElementById('password').type='password'"
                                onmouseleave="document.getElementById('password').type='password'">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-3 fw-semibold" name="btnLogin">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                    </button>
                </form>
            </div>
        </div>

        <div class="footer">
            <p>© 2025 SMATI. All rights reserved.</p>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');

            // Touch support for mobile devices
            passwordToggle.addEventListener('touchstart', function(e) {
                e.preventDefault();
                passwordInput.type = 'text';
            });

            passwordToggle.addEventListener('touchend', function(e) {
                e.preventDefault();
                passwordInput.type = 'password';
            });

            // Change icon when revealing password
            passwordToggle.addEventListener('mousedown', function() {
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            });

            passwordToggle.addEventListener('mouseup', function() {
                this.innerHTML = '<i class="bi bi-eye"></i>';
            });

            passwordToggle.addEventListener('mouseleave', function() {
                this.innerHTML = '<i class="bi bi-eye"></i>';
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