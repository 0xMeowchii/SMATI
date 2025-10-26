document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const passwordForm = document.getElementById('passwordForm');
    const pinForm = document.getElementById('pinForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    const numpadButtons = document.querySelectorAll('.numpad-btn');
    const pinDisplay = document.getElementById('pinDisplay');
    const authRadioInputs = document.querySelectorAll('.auth-radio-input');
    const emailInput = document.getElementById('email');
    const emailValidation = document.getElementById('emailValidation');
    const passwordValidation = document.getElementById('passwordValidation');
    const keyboardNotification = document.getElementById('keyboardNotification');
    const loginMethod = document.getElementById('method');
    
    // PIN storage
    let pinInput = '';
    let keyboardPinActive = false;
    
    // Authentication method switching via radio buttons
    authRadioInputs.forEach(radio => {
        radio.addEventListener('change', function() {
            const type = this.value;
            
            // Hide all forms
            passwordForm.style.display = 'none';
            pinForm.style.display = 'none';
            
            // Show selected form
            if (type === 'password') {
                passwordForm.style.display = 'block';
                loginMethod.value = 'password';
                // Remove keyboard event listener for PIN
                document.removeEventListener('keydown', handleKeyboardPinInput);
                keyboardNotification.style.display = 'none';
            } else if (type === 'pin') {
                pinForm.style.display = 'block';
                loginMethod.value = 'pin';
                // Add keyboard event listener for PIN
                document.addEventListener('keydown', handleKeyboardPinInput);
                keyboardNotification.style.display = 'flex';
            }
        });
    });
    
    // Real-time email validation
    emailInput.addEventListener('input', function() {
        validateEmail();
    });
    
    // Real-time password validation
    passwordInput.addEventListener('input', function() {
        validatePassword();
    });
    
    function validateEmail() {
        const email = emailInput.value;
        if (email.length === 0) {
            emailValidation.textContent = '';
            emailValidation.className = 'validation-feedback';
            return false;
        }
        
        const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        
        if (isValid) {
            emailValidation.textContent = 'Valid email format';
            emailValidation.className = 'validation-feedback validation-valid';
        } else {
            emailValidation.textContent = 'Please enter a valid email address';
            emailValidation.className = 'validation-feedback validation-invalid';
        }
        
        return isValid;
    }
    
    function validatePassword() {
        const password = passwordInput.value;
        if (password.length === 0) {
            passwordValidation.textContent = '';
            passwordValidation.className = 'validation-feedback';
            return false;
        }
        
        if (password.length >= 8) {
            passwordValidation.textContent = 'Password strength: Good';
            passwordValidation.className = 'validation-feedback validation-valid';
            return true;
        } else {
            passwordValidation.textContent = 'Password should be at least 8 characters';
            passwordValidation.className = 'validation-feedback validation-invalid';
            return false;
        }
    }
    
    // Keyboard PIN input handler
    function handleKeyboardPinInput(e) {
        // Only handle number keys, backspace, and delete
        if ((e.key >= '0' && e.key <= '9') || e.key === 'Backspace' || e.key === 'Delete') {
            e.preventDefault();
            
            if (e.key === 'Backspace' || e.key === 'Delete') {
                pinInput = pinInput.slice(0, -1);
            } else if (pinInput.length < 6) {
                pinInput += e.key;
            }
            
            updatePinDisplay();
        }
    }
    
    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Numpad functionality
    numpadButtons.forEach(button => {
        button.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            
            if (value === 'clear') {
                pinInput = '';
            } else if (value === 'backspace') {
                pinInput = pinInput.slice(0, -1);
            } else if (pinInput.length < 6) {
                pinInput += value;
            }
            
            updatePinDisplay();
        });
    });
    
    function updatePinDisplay() {
        // Update the visual display
        let display = '';
        for (let i = 0; i < 6; i++) {
            if (i < pinInput.length) {
                display += '•';
            } else {
                display += '_';
            }
        }
        pinDisplay.value = display;
        
        // Update the hidden input for form submission
        document.getElementById('pinInput').value = pinInput;
        
        // Remove error class when user starts typing again
        pinDisplay.classList.remove('pin-error');
    }
    
    // Login validation
    loginBtn.addEventListener('click', function(e) {
        // Check if reCAPTCHA is completed
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Security Verification Required',
                text: 'Please complete the reCAPTCHA verification to continue.',
                confirmButtonColor: '#3a86ff'
            });
            return;
        }
        
        // Validate based on active login type
        const isPasswordLogin = passwordForm.style.display !== 'none';
        
        if (isPasswordLogin) {
            if (!validatePasswordLogin()) {
                e.preventDefault();
            }
        } else {
            if (!validatePinLogin()) {
                e.preventDefault();
            }
        }
    });
    
    function validatePasswordLogin() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!email || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Credentials',
                text: 'Please enter both email and password.',
                confirmButtonColor: '#3a86ff'
            });
            return false;
        }
        
        // Validate email format
        if (!validateEmail()) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                confirmButtonColor: '#3a86ff'
            });
            return false;
        }
        
        // All validations passed
        return true;
    }
    
    function validatePinLogin() {
        if (pinInput.length !== 6) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid PIN',
                text: 'PIN must be exactly 6 digits.',
                confirmButtonColor: '#3a86ff'
            });
            return false;
        }
        
        // All validations passed
        return true;
    }
});