<?php

include('../database.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
</head>

<body>
    <!-- Sidebar -->
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fa fa-book me-2"></i>My Subjects</h4>
        </div>

        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Subjects</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search S.Y. & Semester...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>School Year & Semester</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * FROM schoolyear WHERE status = '1' ORDER BY schoolyear_id DESC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['schoolyear'] . ", " . $row['semester'] . " Semester" . "</td>";
                                echo "<td>
                                        <a class='btn btn-sm btn-outline-primary'
                                         href='teacher-subject-list.php?sy=" . $row['schoolyear_id'] . "'>
                                        <i class='fas fa-eye me-2'></i>View
                                        </a>
                                        </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "0 results";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function initializeSearchInput() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('tbody tr');
            const noResultsRow = document.getElementById('noResults') || createNoResultsRow();

            if (!searchInput) return;

            function createNoResultsRow() {
                const tbody = document.querySelector('tbody');
                const row = document.createElement('tr');
                row.id = 'noResults';
                row.style.display = 'none';
                row.innerHTML = `<td colspan="2" class="text-center text-muted py-4">No results found</td>`;
                tbody.appendChild(row);
                return row;
            }

            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let hasVisibleRows = false;

                tableRows.forEach(row => {
                    if (row.id === 'noResults') return;

                    const schoolYearText = row.cells[0].textContent.toLowerCase();
                    const isVisible = schoolYearText.includes(searchTerm);

                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) hasVisibleRows = true;
                });

                // Show/hide no results message
                if (searchTerm && !hasVisibleRows) {
                    noResultsRow.style.display = '';
                } else {
                    noResultsRow.style.display = 'none';
                }
            }

            // Debounced search to improve performance
            let timeoutId;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(performSearch, 300);
            });

            // Enter key support
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(timeoutId);
                    performSearch();
                }
            });

            // Clear search on escape
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    performSearch();
                }
            });
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', initializeSearchInput);
    </script>
</body>

</html>