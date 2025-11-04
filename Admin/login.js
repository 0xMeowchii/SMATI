// Initialize variables
let currentRegisterStep = 1;

// Initialize the application
$(document).ready(function () {
    setupEventListeners();
});

// Set up all event listeners
function setupEventListeners() {
    // Real-time validation
    $('#registerEmail, #recoveryEmail').on('input', function () {
        validateInput($(this));
    });

    $('#securityAnswer, #recoveryAnswer').on('input', function () {
        validateInput($(this), true);
    });

    // Password input listeners
    $('#registerPassword').on('input', function () {
        validatePassword($(this));
        checkPasswordStrength();
    });

    $('#recoverPassword').on('input', function () {
        validateRecoverPassword($(this));
        checkRecoverPasswordStrength();
    });

    // Confirm password listeners
    $('#confirmPassword').on('input', function () {
        validateConfirmPassword();
    });

    $('#confirmRecoverPassword').on('input', function () {
        validateConfirmRecoverPassword();
    });

    // Clear answer field if no dropdown selection
    $('#securityQuestion').change(function () {
        if (!$(this).val()) {
            $('#securityAnswer').val('');
        }
    });

    // Caps lock detection
    $('#registerPassword, #recoverPassword, #loginPassword').keyup(function (e) {
        checkCapsLock(e);
    });

    // Password visibility toggling with animation
    $('#toggleRegisterPassword').click(function () {
        togglePasswordVisibility('registerPassword', $(this));
    });

    $('#toggleConfirmPassword').click(function () {
        togglePasswordVisibility('confirmPassword', $(this));
    });

    $('#toggleRecoverPassword').click(function () {
        togglePasswordVisibility('recoverPassword', $(this));
    });

    $('#toggleRecoverConfirmPassword').click(function () {
        togglePasswordVisibility('confirmRecoverPassword', $(this));
    });

    $('#toggleLoginPassword').click(function () {
        togglePasswordVisibility('loginPassword', $(this));
    });

    // Form navigation
    $('#showForgotPassword').click(showForgotPasswordForm);
    $('#backToLoginFromForgot').click(showLoginForm);

    // Registration steps
    $('#toStep2').click(toStep2);
    $('#backToStep1').click(backToStep1);
    $('#toStep3').click(toStep3);
    $('#backToStep2').click(backToStep2);

    // Form submissions
    $('#registerForm1').submit(handleRegistration);
    $('#forgotPasswordFormInner1').submit(handlePasswordRecovery);
    $('#loginForm').submit(handleLoginSubmit);

    // Tab change event
    $('#authTabs button').click(function () {
        if (this.id === 'login-tab') {
            showLoginForm();
        } else if (this.id === 'register-tab') {
            showRegisterForm();
        }
    });
}

// Handle login form submission
function handleLoginSubmit(e) {
    // Check if locked out
    const lockoutEnd = sessionStorage.getItem('loginLockoutEnd');
    if (lockoutEnd && Date.now() < parseInt(lockoutEnd)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Account Locked',
            text: 'Please wait before attempting to login again.',
            confirmButtonColor: '#0d6efd'
        });
        return false;
    }

    // Form will submit normally, PHP will handle validation
    return true;
}

// Validate input in real-time
function validateInput(input, required = true) {
    const value = input.val().trim();
    const id = input.attr('id');
    const validIcon = $('#' + id + 'Valid');
    const invalidIcon = $('#' + id + 'Invalid');

    input.removeClass('valid-input invalid-input');
    validIcon.hide();
    invalidIcon.hide();

    if (!required && value === '') {
        return true;
    }

    let isValid = false;
    if (input.attr('type') === 'email') {
        isValid = isValidEmail(value);
    } else {
        isValid = value.length > 0;
    }

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

// Validate password
function validatePassword(input) {
    const value = input.val();
    const id = input.attr('id');
    const validIcon = $('#' + id + 'Valid');
    const invalidIcon = $('#' + id + 'Invalid');

    input.removeClass('valid-input invalid-input');
    validIcon.hide();
    invalidIcon.hide();

    const isValid = isValidPassword(value);

    if (isValid) {
        input.addClass('valid-input');
        validIcon.show();
        return true;
    } else if (value.length > 0) {
        input.addClass('invalid-input');
        invalidIcon.show();
        return false;
    }

    return false;
}

// Validate confirm password
function validateConfirmPassword() {
    const password = $('#registerPassword').val();
    const confirmPassword = $('#confirmPassword').val();
    const input = $('#confirmPassword');
    const validIcon = $('#confirmPasswordValid');
    const invalidIcon = $('#confirmPasswordInvalid');

    input.removeClass('valid-input invalid-input');
    validIcon.hide();
    invalidIcon.hide();

    if (confirmPassword === '') {
        return false;
    }

    const isValid = password === confirmPassword;

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

// Check password strength
function checkPasswordStrength() {
    const password = $('#registerPassword').val();
    const strengthBar = $('#passwordStrengthBar');

    let strength = 0;

    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;

    strengthBar.css('width', strength + '%');

    if (strength < 50) {
        strengthBar.css('background-color', '#e53935');
    } else if (strength < 100) {
        strengthBar.css('background-color', '#fb8c00');
    } else {
        strengthBar.css('background-color', '#43a047');
    }
}

// Check recover password strength
function checkRecoverPasswordStrength() {
    const password = $('#recoverPassword').val();
    const strengthBar = $('#recoverPasswordStrengthBar');

    let strength = 0;

    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;

    strengthBar.css('width', strength + '%');

    if (strength < 50) {
        strengthBar.css('background-color', '#e53935');
    } else if (strength < 100) {
        strengthBar.css('background-color', '#fb8c00');
    } else {
        strengthBar.css('background-color', '#43a047');
    }
}

// Check if caps lock is on
function checkCapsLock(e) {
    const isCapsLockOn = e.getModifierState && e.getModifierState('CapsLock');
    const fieldId = e.target.id;

    if (fieldId === 'registerPassword') {
        $('#registerCapsWarning').toggle(isCapsLockOn);
    } else if (fieldId === 'recoverPassword') {
        $('#recoverCapsWarning').toggle(isCapsLockOn);
    } else if (fieldId === 'loginPassword') {
        $('#loginCapsWarning').toggle(isCapsLockOn);
    }
}

// Toggle password visibility with animation
function togglePasswordVisibility(fieldId, toggleElement) {
    const passwordField = $('#' + fieldId);
    const icon = toggleElement.find('i');

    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
}

// Show register form
function showRegisterForm(e) {
    if (e) e.preventDefault();

    // Hide login form and show register form using Bootstrap classes
    $('#login').removeClass('show active');
    $('#register').addClass('show active');
    $('#login-tab').removeClass('active');
    $('#register-tab').addClass('active');

    // Show registration step 1
    $('.form-section').removeClass('active');
    $('#registerForm1').addClass('active');
    $('#registerStep1').addClass('active');
    $('#forgotPasswordForm').removeClass('active');
    currentRegisterStep = 1;
    updateStepIndicator();
}

// Show forgot password form
function showForgotPasswordForm(e) {
    e.preventDefault();
    // Hide both login and register tabs
    $('#login').removeClass('show active');
    $('#register').removeClass('show active');
    $('#login-tab').removeClass('active');
    $('#register-tab').removeClass('active');

    // Show forgot password form
    $('.form-section').removeClass('active');
    $('#forgotPasswordForm').addClass('active');

    // Reset the warning
    $('#resetLimitWarning').hide();
}

// Check password reset limit for email
function checkResetLimit(email) {
    if (!email || !isValidEmail(email)) {
        $('#resetLimitWarning').hide();
        return;
    }

    $.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            check_reset_limit: true,
            check_email: email
        },
        dataType: 'json',
        success: function (response) {
            if (response.remaining !== undefined) {
                const remaining = response.remaining;
                const currentMonth = new Date().toLocaleString('default', { month: 'long', year: 'numeric' });

                if (remaining === 0) {
                    $('#resetLimitText').html(`You have used all password resets for <strong>${currentMonth}</strong>. Please try again next month.`);
                    $('#resetLimitWarning').removeClass('alert-warning').addClass('alert-danger').show();
                    $('#recoveryButton').prop('disabled', true);
                } else if (remaining === 1) {
                    $('#resetLimitText').html(`You have <strong>1 password reset</strong> remaining for <strong>${currentMonth}</strong>.`);
                    $('#resetLimitWarning').removeClass('alert-danger').addClass('alert-warning').show();
                    $('#recoveryButton').prop('disabled', false);
                } else {
                    $('#resetLimitText').html(`You have <strong>${remaining} password resets</strong> remaining for <strong>${currentMonth}</strong>.`);
                    $('#resetLimitWarning').removeClass('alert-danger').addClass('alert-info').show();
                    $('#recoveryButton').prop('disabled', false);
                }
            }
        },
        error: function () {
            // Silently fail - don't block user
            $('#resetLimitWarning').hide();
        }
    });
}

// Show login form
function showLoginForm(e) {
    if (e) e.preventDefault();

    // Show login form and hide register form using Bootstrap classes
    $('#login').addClass('show active');
    $('#register').removeClass('show active');
    $('#login-tab').addClass('active');
    $('#register-tab').removeClass('active');

    // Hide other forms
    $('.form-section').removeClass('active');
    $('#forgotPasswordForm').removeClass('active');
}

// Navigate to step 2 of registration
function toStep2() {
    const question = $('#securityQuestion').val();
    const answer = $('#securityAnswer').val();

    if (!question) {
        Swal.fire('Error', 'Please select a security question', 'error');
        return;
    }

    if (!validateInput($('#securityAnswer'))) {
        Swal.fire('Error', 'Please provide an answer to the security question', 'error');
        return;
    }

    $('#registerStep1').removeClass('active');
    $('#registerStep2').addClass('active');
    currentRegisterStep = 2;
    updateStepIndicator();
}

// Navigate back to step 1 of registration
function backToStep1() {
    $('#registerStep2').removeClass('active');
    $('#registerStep1').addClass('active');
    currentRegisterStep = 1;
    updateStepIndicator();
}

// Navigate to step 3 of registration
function toStep3() {
    const email = $('#registerEmail').val();

    if (!validateInput($('#registerEmail'))) {
        Swal.fire('Error', 'Please enter a valid email address', 'error');
        return;
    }

    if (!validatePassword($('#registerPassword'))) {
        Swal.fire('Error', 'Password must be at least 8 characters with uppercase, lowercase, and numbers', 'error');
        return;
    }

    if (!validateConfirmPassword()) {
        Swal.fire('Error', 'Passwords do not match', 'error');
        return;
    }

    $('#reviewEmail').text(email);
    $('#reviewQuestion').text($('#securityQuestion option:selected').text());

    $('#registerStep2').removeClass('active');
    $('#registerStep3').addClass('active');
    currentRegisterStep = 3;
    updateStepIndicator();
}

// Navigate back to step 2 of registration
function backToStep2() {
    $('#registerStep3').removeClass('active');
    $('#registerStep2').addClass('active');
    currentRegisterStep = 2;
    updateStepIndicator();
}

// Update step indicator
function updateStepIndicator() {
    $('.step').removeClass('active completed');

    for (let i = 1; i <= currentRegisterStep; i++) {
        if (i < currentRegisterStep) {
            $('#step' + i).addClass('completed');
        } else {
            $('#step' + i).addClass('active');
        }
    }
}

// Handle registration form submission
function handleRegistration(e) {
    e.preventDefault();

    if (!$('#termsAgree').is(':checked')) {
        Swal.fire('Error', 'You must agree to the terms and conditions', 'error');
        return;
    }
}

// Handle password recovery
function handlePasswordRecovery(e) {
    e.preventDefault();

    const email = $('#recoveryEmail').val();
    const question = $('#recoveryQuestion').val();
    const answer = $('#recoveryAnswer').val();

    if (!validateInput($('#recoveryEmail'))) {
        Swal.fire('Error', 'Please enter a valid email address', 'error');
        return;
    }

    if (!question) {
        Swal.fire('Error', 'Please select a security question', 'error');
        return;
    }

    if (!validateInput($('#recoveryAnswer'))) {
        Swal.fire('Error', 'Please provide an answer to the security question', 'error');
        return;
    }

}

// Validate email format
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate password strength
function isValidPassword(password) {
    const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    return re.test(password);
}