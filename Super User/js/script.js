document.addEventListener('DOMContentLoaded', function () {
    const passwordToggle = document.getElementById('password-toggle');
    const passwordInput = document.getElementById('editPassword');

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
    });

    passwordToggle.addEventListener('mouseup', function () {
        this.innerHTML = '<i class="fas fa-eye"></i>';
    });

    passwordToggle.addEventListener('mouseleave', function () {
        this.innerHTML = '<i class="fas fa-eye"></i>';
    });
});

// Real-time search functionality for students table
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
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
});

document.querySelectorAll('.edit-admin-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('editId').value = btn.getAttribute('data-id');
        document.getElementById('editEmail').value = btn.getAttribute('data-email');
        document.getElementById('editPassword').value = btn.getAttribute('data-password');
    });
});

document.querySelectorAll('.edit-teacher-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('editId').value = btn.getAttribute('data-id');
        document.getElementById('editEmail').value = btn.getAttribute('data-email');
        document.getElementById('editPassword').value = btn.getAttribute('data-password');
    });
});

document.querySelectorAll('.edit-student-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('editId').value = btn.getAttribute('data-id');
        document.getElementById('editEmail').value = btn.getAttribute('data-email');
        document.getElementById('editPassword').value = btn.getAttribute('data-password');
    });
});