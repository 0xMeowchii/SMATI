function validatePassword(password) {
    // Check if password is at least 8 characters long
    if (password.length < 8) {
        return false;
    }

    // Check if password contains at least one capital letter
    if (!/[A-Z]/.test(password)) {
        return false;
    }

    // Check if password contains at least one number
    if (!/\d/.test(password)) {
        return false;
    }

    // All requirements met
    return true;
}

// Main DOMContentLoaded function that organizes all functionality
document.addEventListener('DOMContentLoaded', function () {
    // Initialize password validation for both forms
    initializePasswordValidation('editPassword', 'editForm', 'editError', true); // true = optional field
    initializePasswordValidation('password', 'insertForm', 'insertError', false); // false = required field

    // Real-time search functionality for students table
    initializeSearchFunctionality();
});

// Function to initialize password validation for any form
function initializePasswordValidation(passwordInputId, formId, errorMessageDivId, isOptional = false) {
    const passwordInput = document.getElementById(passwordInputId);
    const errorMessageDiv = document.getElementById(errorMessageDivId);
    const form = document.getElementById(formId);

    if (!passwordInput || !form) return;

    let isPasswordValid = false;

    function updatePasswordValidation() {
        const password = passwordInput.value;

        // Clear previous messages
        errorMessageDiv.innerHTML = '';

        // If field is optional and empty, consider it valid
        if (isOptional && password.length === 0) {
            isPasswordValid = true;
            passwordInput.style.borderColor = '';
            passwordInput.style.boxShadow = '';
            return;
        }

        if (validatePassword(password)) {
            // Password is valid
            isPasswordValid = true;
            passwordInput.style.borderColor = 'green';
            passwordInput.style.boxShadow = '0 0 5px rgba(0, 255, 0, 0.3)';

            const successMessage = document.createElement('div');
            successMessage.textContent = '✓ Password meets requirements';
            successMessage.style.color = 'green';
            successMessage.style.fontSize = '14px';
            successMessage.style.marginTop = '5px';
            errorMessageDiv.appendChild(successMessage);
        } else {
            // Password is invalid
            isPasswordValid = false;
            passwordInput.style.borderColor = 'red';
            passwordInput.style.boxShadow = '0 0 5px rgba(255, 0, 0, 0.3)';

            // Show what's missing
            const missing = [];
            if (password.length < 8) {
                missing.push('at least 8 characters');
            }
            if (!/[A-Z]/.test(password)) {
                missing.push('one capital letter');
            }
            if (!/\d/.test(password)) {
                missing.push('one number');
            }

            const errorMessage = document.createElement('div');
            errorMessage.textContent = `Missing: ${missing.join(', ')}`;
            errorMessage.style.color = 'red';
            errorMessage.style.fontSize = '14px';
            errorMessage.style.marginTop = '5px';
            errorMessageDiv.appendChild(errorMessage);
        }

        // Clear message and styling when input is empty (for optional fields)
        if (password.length === 0) {
            passwordInput.style.borderColor = '';
            passwordInput.style.boxShadow = '';
            errorMessageDiv.innerHTML = '';
            isPasswordValid = isOptional; // Valid if optional, invalid if required
        }
    }

    // Password toggle functionality for this specific input
    const passwordToggle = document.getElementById(passwordInputId + '-toggle');
    if (passwordToggle) {
        // Touch support for mobile devices
        passwordToggle.addEventListener('touchstart', function (e) {
            e.preventDefault();
            passwordInput.type = 'text';
        });

        passwordToggle.addEventListener('touchend', function (e) {
            e.preventDefault();
            passwordInput.type = 'password';
        });

        // Change icon when revealing password
        passwordToggle.addEventListener('mousedown', function () {
            this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            passwordInput.type = 'text';
        });

        passwordToggle.addEventListener('mouseup', function () {
            this.innerHTML = '<i class="fas fa-eye"></i>';
            passwordInput.type = 'password';
        });

        passwordToggle.addEventListener('mouseleave', function () {
            this.innerHTML = '<i class="fas fa-eye"></i>';
            passwordInput.type = 'password';
        });
    }

    // Real-time password validation
    passwordInput.addEventListener('input', updatePasswordValidation);
    passwordInput.addEventListener('blur', updatePasswordValidation);

    // Form submission handler
    form.addEventListener('submit', function (e) {
        const formPasswordInput = this.querySelector('#' + passwordInputId);
        const passwordValue = formPasswordInput ? formPasswordInput.value : '';

        // For optional fields (like editPassword), only validate if there's a value
        // For required fields (like insertForm password), always validate
        const shouldValidate = isOptional ? passwordValue.length > 0 : true;

        if (formPasswordInput && shouldValidate && !validatePassword(passwordValue)) {
            e.preventDefault();
            e.stopPropagation();

            // Show error message
            updatePasswordValidation();

            // Focus on password input
            passwordInput.focus();

            // Add shake animation for attention
            passwordInput.style.animation = 'shake 0.5s';
            setTimeout(() => {
                passwordInput.style.animation = '';
            }, 500);
        }
    });

    // Initial validation to set correct state
    updatePasswordValidation();
}

// Function to initialize search functionality
function initializeSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        const tableBody = document.querySelector('tbody');
        const tableRows = Array.from(tableBody.querySelectorAll('tr'));

        // Function to perform the search
        function performSearch(searchTerm) {
            const query = searchTerm.toLowerCase().trim();
            let visibleRows = 0;

            tableRows.forEach(function (row) {
                // Get all text content from the row (excluding action buttons)
                const cells = row.querySelectorAll('td');
                let rowText = '';

                // Combine text from StudentID, Name, Course, and Email columns (skip Action column)
                for (let i = 0; i < cells.length - 1; i++) {
                    rowText += cells[i].textContent.toLowerCase() + ' ';
                }

                // Check if search term matches any part of the row text
                if (query === '' || rowText.includes(query)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Optional: Show/hide "No results" message
            showNoResultsMessage(visibleRows === 0 && query !== '');
        }

        // Function to show/hide no results message
        function showNoResultsMessage(show) {
            let noResultsRow = document.getElementById('no-results-row');

            if (show && !noResultsRow) {
                // Create no results row if it doesn't exist
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="5" class="text-center py-4" style="color: #6c757d;">
                        <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                        <br>
                        No results found matching your search
                    </td>
                `;
                tableBody.appendChild(noResultsRow);
            } else if (!show && noResultsRow) {
                // Remove no results row if it exists
                noResultsRow.remove();
            }
        }

        // Add event listener for real-time search
        searchInput.addEventListener('input', function (e) {
            performSearch(e.target.value);
        });

        // Add event listener for paste events
        searchInput.addEventListener('paste', function (e) {
            // Small delay to ensure pasted content is processed
            setTimeout(function () {
                performSearch(searchInput.value);
            }, 10);
        });

        // Optional: Add search icon click functionality
        const searchIcon = document.querySelector('#searchInput + .input-group-text');
        if (searchIcon) {
            searchIcon.addEventListener('click', function () {
                searchInput.focus();
            });
        }
    }
}

// Add CSS for shake animation
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
`;
document.head.appendChild(style);