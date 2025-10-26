// Initialize variables
let cooldownActive = false;
let cooldownTime = 30;
let cooldownTimer;
let loginAttempts = 0;
let lastAttemptTime = 0;
let isOnline = navigator.onLine;
let statusProgressInterval;

// Check if cooldown is active
function checkCooldown() {
    const currentTime = Math.floor(Date.now() / 1000);
    const elapsedTime = currentTime - lastAttemptTime;

    if (loginAttempts >= 3 && elapsedTime < cooldownTime) {
        startCooldown(cooldownTime - elapsedTime);
    }
}

// Start cooldown timer with SweetAlert and show timer in UI
function startCooldown(seconds) {
    cooldownActive = true;
    $('#authContainer').addClass('cooldown-active');
    $('#loginButton').prop('disabled', true);
    $('#cooldownTimer').show();

    // Update the timer display
    $('#cooldownSeconds').text(seconds);

    cooldownTimer = setInterval(() => {
        seconds--;
        $('#cooldownSeconds').text(seconds);

        if (seconds <= 0) {
            clearInterval(cooldownTimer);
            cooldownActive = false;
            $('#authContainer').removeClass('cooldown-active');
            $('#loginButton').prop('disabled', false);
            $('#cooldownTimer').hide();
            loginAttempts = 0;
        }
    }, 1000);

    // Also show SweetAlert notification
    Swal.fire({
        title: 'Too Many Attempts!',
        html: `Please wait <b>${seconds}</b> seconds before trying again.`,
        icon: 'error',
        timer: seconds * 1000,
        timerProgressBar: true,
        showConfirmButton: false,
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            Swal.fire('Cooldown Complete!', 'You can now try to login again.', 'success');
        }
    });
}

// Enable/disable form fields based on internet status
function toggleFormFields(online) {
    const loginId = $('#loginId');
    const loginPassword = $('#loginPassword');
    const loginButton = $('#loginButton');
    const passwordToggle = $('#toggleLoginPassword');
    const forgotPassword = $('#showForgotPassword');
    const studentBtn = $('#studentBtn');
    const teacherBtn = $('#teacherBtn');
    const offlineOverlay = $('#offlineOverlay');
    const loginPanel = $('#loginPanel');

    if (online) {
        // Enable all fields
        loginId.prop('disabled', false);
        loginPassword.prop('disabled', false);
        loginButton.prop('disabled', false);
        passwordToggle.prop('disabled', false);
        studentBtn.prop('disabled', false);
        teacherBtn.prop('disabled', false);

        // Remove disabled styling
        forgotPassword.removeClass('disabled');
        loginPanel.removeClass('offline');
        offlineOverlay.hide();
        $('body').removeClass('offline-mode');
    } else {
        // Disable all fields
        loginId.prop('disabled', true);
        loginPassword.prop('disabled', true);
        loginButton.prop('disabled', true);
        passwordToggle.prop('disabled', true);
        studentBtn.prop('disabled', true);
        teacherBtn.prop('disabled', true);

        // Add disabled styling
        forgotPassword.addClass('disabled');
        loginPanel.addClass('offline');
        offlineOverlay.show();
        $('body').addClass('offline-mode');
    }
}

// Set up all event listeners
function setupEventListeners() {
    // User type selection
    $('#studentBtn').click(function () {
        if (!isOnline) return;

        $(this).addClass('active');
        $('#teacherBtn').removeClass('active');
        $('#idLabel').text('Student ID');
        $('#loginId').attr('placeholder', 'Enter your Student ID');

        // Clear any stored credentials when switching
        clearFormFields();

        // Smooth background transition
        $('body').removeClass('teacher-mode').addClass('student-mode animated-background');
    });

    $('#studentBtn, #teacherBtn').click(function () {
        const userType = $(this).data('user-type');

        // Update hidden input
        $('#userType').val(userType);
    });

    $('#teacherBtn').click(function () {
        if (!isOnline) return;

        $(this).addClass('active');
        $('#studentBtn').removeClass('active');
        $('#idLabel').text('Teacher ID');
        $('#loginId').attr('placeholder', 'Enter your Teacher ID');

        // Clear any stored credentials when switching
        clearFormFields();

        // Smooth background transition
        $('body').removeClass('student-mode').addClass('teacher-mode animated-background');
    });

    // Real-time validation
    $('#loginId').on('input', function () {
        if (!isOnline) return;
        validateInput($(this));
    });

    $('#loginPassword').on('input', function () {
        if (!isOnline) return;
        validateInput($(this), true);
    });

    // Caps lock detection
    $('#loginPassword').keyup(function (e) {
        if (!isOnline) return;
        checkCapsLock(e);
    });

    // Password visibility toggling with animation
    $('#toggleLoginPassword').click(function () {
        if (!isOnline) return;
        togglePasswordVisibility('loginPassword', $(this));
    });
    

    // Form submission
    $('#loginForm').submit(function (e) {
        if (!isOnline) {
            e.preventDefault();
            Swal.fire('No Internet Connection', 'Please check your network connection and try again.', 'warning');
            return;
        }

        if (cooldownActive) {
            e.preventDefault();
            Swal.fire('Error', 'Please wait for the cooldown period to end before trying again', 'error');
            return;
        }

        // Basic validation
        const id = $('#loginId').val().trim();
        const password = $('#loginPassword').val().trim();

        if (!id || !password) {
            e.preventDefault();
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }

        // If all validations pass, allow form submission to proceed normally
        // No need to prevent default, let the form submit to PHP
    });

    // Forgot password
    $('#showForgotPassword').click(handleForgotPassword);

    // Status close button
    $('#statusClose').click(function () {
        hideConnectionStatus();
    });

    // Account recovery modal
    $('#closeRecoveryModal').click(function () {
        $('#recoveryModal').hide();
    });

    $('#emailRecovery').click(function () {
        Swal.fire({
            title: 'Email Recovery',
            html: 'Recovery instructions will be sent to your registered email address.<br><br>' +
                '<div class="text-start">' +
                '<p class="mb-1"><i class="bi bi-envelope me-2"></i><strong>Email:</strong> admin@smati.edu</p>' +
                '</div>',
            icon: 'info',
            confirmButtonColor: '#0A2342',
            confirmButtonText: 'OK'
        });
    });

    $('#phoneRecovery').click(function () {
        Swal.fire({
            title: 'SMS Recovery',
            html: 'A verification code will be sent to your registered mobile number.<br><br>' +
                '<div class="text-start">' +
                '<p class="mb-1"><i class="bi bi-telephone me-2"></i><strong>Phone:</strong> (123) 456-7890</p>' +
                '</div>',
            icon: 'info',
            confirmButtonColor: '#0A2342',
            confirmButtonText: 'OK'
        });
    });

    $('#adminRecovery').click(function () {
        Swal.fire({
            title: 'Contact Administrator',
            html: 'Please contact the administration office for assistance.<br><br>' +
                '<div class="text-start">' +
                '<p class="mb-1"><i class="bi bi-telephone me-2"></i><strong>Phone:</strong> (123) 456-7890</p>' +
                '<p class="mb-1"><i class="bi bi-envelope me-2"></i><strong>Email:</strong> admin@smati.edu</p>' +
                '<p class="mb-0"><i class="bi bi-geo-alt me-2"></i><strong>Office:</strong> Administration Building, Room 101</p>' +
                '</div>',
            icon: 'info',
            confirmButtonColor: '#0A2342',
            confirmButtonText: 'OK'
        });
    });
}

// Validate input in real-time
function validateInput(input, required = true) {
    const value = input.val().trim();
    const id = input.attr('id');
    const validIcon = $('#' + id + 'Valid');
    const invalidIcon = $('#' + id + 'Invalid');

    // Remove previous validation classes
    input.removeClass('valid-input invalid-input');
    validIcon.hide();
    invalidIcon.hide();

    // Skip validation if empty and not required
    if (!required && value === '') {
        return true;
    }

    // Validate based on input type
    let isValid = value.length > 0;

    // Apply validation styling
    if (isValid) {
        input.addClass('valid-input');
        validIcon.show();
        return true;
    } else {
        input.addClass('invalid-input');
        invalidIcon.show();
        return false;
    }
}

// Check if caps lock is on
function checkCapsLock(e) {
    const isCapsLockOn = e.getModifierState && e.getModifierState('CapsLock');
    $('#loginCapsWarning').toggle(isCapsLockOn);
}

// Toggle password visibility with animation
function togglePasswordVisibility(fieldId, toggleElement) {
    const passwordField = $('#' + fieldId);
    const icon = toggleElement.find('i');

    // Animate the icon
    toggleElement.addClass('shake');
    setTimeout(() => {
        toggleElement.removeClass('shake');
    }, 500);

    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
}

// Clear form fields
function clearFormFields() {
    $('#loginId').val('');
    $('#loginPassword').val('');
    $('#loginId').removeClass('valid-input invalid-input');
    $('#loginPassword').removeClass('valid-input invalid-input');
    $('#loginIdValid, #loginIdInvalid, #loginPasswordValid, #loginPasswordInvalid').hide();
}

// Handle forgot password
function handleForgotPassword(e) {
    e.preventDefault();

    if (!isOnline) {
        Swal.fire('No Internet Connection', 'Please check your network connection and try again.', 'warning');
        return;
    }

    // Show the elegant recovery modal
    $('#recoveryModal').show();
}

// Hide connection status notification
function hideConnectionStatus() {
    const statusElement = $('#connectionStatus');
    statusElement.css('animation', 'slideOutRight 0.5s ease');
    setTimeout(() => {
        statusElement.hide();
        statusElement.css('animation', '');
        clearInterval(statusProgressInterval);
    }, 500);
}

// Show connection status notification
function showConnectionStatus(online) {
    const statusElement = $('#connectionStatus');
    const statusCard = $('#statusCard');
    const statusIcon = $('#statusIcon');
    const statusTitle = $('#statusTitle');
    const statusMessage = $('#statusMessage');
    const statusProgressBar = $('#statusProgressBar');

    clearInterval(statusProgressInterval);

    if (online) {
        statusCard.removeClass('offline').addClass('online');
        statusIcon.removeClass('offline').addClass('online');
        statusIcon.find('i').removeClass('bi-wifi-off').addClass('bi-wifi');
        statusTitle.text('Connection Restored');
        statusMessage.text('Your internet connection has been restored. You can now continue using the portal.');
        statusProgressBar.removeClass('offline').addClass('online');
    } else {
        statusCard.removeClass('online').addClass('offline');
        statusIcon.removeClass('online').addClass('offline');
        statusIcon.find('i').removeClass('bi-wifi').addClass('bi-wifi-off');
        statusTitle.text('No Internet Connection');
        statusMessage.text('Please check your network connection. Some features may be unavailable.');
        statusProgressBar.removeClass('online').addClass('offline');
    }

    statusElement.show();

    // Start progress bar countdown for online status
    if (online) {
        let progress = 100;
        statusProgressBar.css('width', '100%');

        statusProgressInterval = setInterval(() => {
            progress -= 1;
            statusProgressBar.css('width', progress + '%');

            if (progress <= 0) {
                hideConnectionStatus();
            }
        }, 50); // 5 seconds total
    }
}

// Check internet connection status
function checkInternetConnection() {
    const wasOnline = isOnline;
    isOnline = navigator.onLine;

    if (isOnline !== wasOnline) {
        if (isOnline) {
            // Enable form fields
            toggleFormFields(true);
            // Show online notification
            showConnectionStatus(true);
        } else {
            // Disable form fields
            toggleFormFields(false);
            // Show offline notification
            showConnectionStatus(false);
        }
    }
}

// Initialize the application
function initializeApplication() {
    // Check if we're in cooldown period
    checkCooldown();

    // Set up event listeners
    setupEventListeners();

    // Set up internet connection monitoring
    window.addEventListener('online', checkInternetConnection);
    window.addEventListener('offline', checkInternetConnection);

    // Initial internet status check and form setup
    checkInternetConnection();
}

// Start the application when page loads
$(document).ready(function () {
    initializeApplication();
});