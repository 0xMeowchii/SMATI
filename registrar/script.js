document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    const forgotPassword = document.getElementById('forgotPassword');
    const forgotModal = document.getElementById('forgotModal');
    const closeModal = document.getElementById('closeModal');

    // Toggle password visibility
    togglePassword.addEventListener('click', function () {

        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Forgot password modal
    forgotPassword.addEventListener('click', function () {
        forgotModal.classList.add('active');
    });

    // Close modal
    closeModal.addEventListener('click', function () {
        forgotModal.classList.remove('active');
    });

    // Close modal when clicking outside
    forgotModal.addEventListener('click', function (e) {
        if (e.target === forgotModal) {
            forgotModal.classList.remove('active');
        }
    });

    // Login validation
    loginBtn.addEventListener('click', function () {

        // Check if reCAPTCHA is completed
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            Swal.fire({
                icon: 'warning',
                title: 'Security Verification Required',
                text: 'Please complete the reCAPTCHA verification to continue.',
                confirmButtonColor: '#0a1931'
            });
            return;
        }

        // Validate credentials
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        if (!username || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Credentials',
                text: 'Please enter both username and password.',
                confirmButtonColor: '#0a1931'
            });
            return;
        }
        /*
        // Simulate login validation
        if (username === 'admin' && password === 'smati2024') {
            Swal.fire({
                icon: 'success',
                title: 'Access Granted',
                text: 'Welcome to SMATI Registrar System!',
                confirmButtonColor: '#0a1931'
            }).then(() => {
                // Reset failed attempts on successful login
                localStorage.removeItem('failedAttempts');
                localStorage.removeItem('cooldownEndTime');
                resetFormFields();
            });
        } else {
            handleFailedLogin();
        }
            
    */
    });
    /*
    function handleFailedLogin() {
        // Get current failed attempts
        let failedAttempts = parseInt(localStorage.getItem('failedAttempts')) || 0;
        failedAttempts++;

        // Store updated count
        localStorage.setItem('failedAttempts', failedAttempts.toString());

        if (failedAttempts >= 3) {
            // Start cooldown
            const cooldownEndTime = new Date().getTime() + (3 * 60 * 1000); // 3 minutes
            localStorage.setItem('cooldownEndTime', cooldownEndTime.toString());

            // Clear all form fields
            resetFormFields();

            // Block all fields and show cooldown overlay
            enableFormFields(false);
            showCooldownOverlay();

            Swal.fire({
                icon: 'error',
                title: 'Security Lockout Activated',
                html: `Maximum authentication attempts exceeded. System locked for <strong>3 minutes</strong>.`,
                confirmButtonColor: '#0a1931'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: `Invalid credentials. You have ${3 - failedAttempts} attempt(s) remaining.`,
                confirmButtonColor: '#0a1931'
            });

            // Clear form fields after failed attempt
            resetFormFields();
        }
    }

    function isInCooldown() {
        const cooldownEndTime = localStorage.getItem('cooldownEndTime');
        if (!cooldownEndTime) return false;

        const currentTime = new Date().getTime();
        return currentTime < parseInt(cooldownEndTime);
    }

    function checkCooldown() {
        if (isInCooldown()) {
            // Block all fields and show cooldown overlay
            enableFormFields(false);
            showCooldownOverlay();
        }
    }

    function showCooldownOverlay() {
        cooldownOverlay.style.display = 'flex';

        // Start countdown timer
        const cooldownEndTime = parseInt(localStorage.getItem('cooldownEndTime'));
        updateCooldownTimer(cooldownEndTime);

        const timerInterval = setInterval(function () {
            const currentTime = new Date().getTime();
            const remainingTime = cooldownEndTime - currentTime;

            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                // Cooldown finished
                localStorage.removeItem('failedAttempts');
                localStorage.removeItem('cooldownEndTime');
                cooldownOverlay.style.display = 'none';
                enableFormFields(true);

                Swal.fire({
                    icon: 'info',
                    title: 'Security Lockout Lifted',
                    text: 'You can now attempt to access the system again.',
                    confirmButtonColor: '#0a1931'
                });
            } else {
                updateCooldownTimer(cooldownEndTime);
            }
        }, 1000);
    }

    function updateCooldownTimer(cooldownEndTime) {
        const currentTime = new Date().getTime();
        const remainingTime = cooldownEndTime - currentTime;

        const minutes = Math.floor(remainingTime / (60 * 1000));
        const seconds = Math.floor((remainingTime % (60 * 1000)) / 1000);

        cooldownTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    */

    function enableFormFields(enabled) {
        const inputs = document.querySelectorAll('input');
        const buttons = document.querySelectorAll('button');

        inputs.forEach(input => {
            input.disabled = !enabled;
        });

        buttons.forEach(button => {
            if (button.id !== 'loginBtn' && button.id !== 'closeModal') {
                button.disabled = !enabled;
            }
        });

        loginBtn.disabled = !enabled;

        // Adjust styling for disabled state
        if (!enabled) {
            document.querySelectorAll('.form-control').forEach(input => {
                input.style.backgroundColor = '#f1f5f9';
            });
        } else {
            document.querySelectorAll('.form-control').forEach(input => {
                input.style.backgroundColor = '';
            });
        }
    }

    function resetFormFields() {
        // Clear form fields
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';

        // Reset password visibility
        passwordInput.setAttribute('type', 'password');
        togglePassword.querySelector('i').classList.remove('fa-eye-slash');
        togglePassword.querySelector('i').classList.add('fa-eye');

        // Reset reCAPTCHA
        grecaptcha.reset();
    }
});