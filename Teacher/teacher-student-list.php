<?php
include('../database.php');

//INSERT QUERY
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
    $conn = connectToDB();
    $set = $_POST['set'];
    $subject_id = $_POST['subject'];

    if ($conn) {

        $checkStmt = $conn->prepare("SELECT * FROM student_list WHERE subject_id = ? AND student_set = ?");
        $checkStmt->bind_param("is", $subject_id, $set);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Class already exists!',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO student_list (subject_id, student_set) 
                                VALUES (?, ?)");
            $stmt->bind_param("is", $subject_id, $set);

            if ($stmt->execute()) {
                echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Class created succesfully!',
                                    confirmButtonColor: '#d33'
                                }).then(() => {
                                    // Refresh the page after SweetAlert closes
                                    window.location.href = window.location.href;
                                });
                            });
                        </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '" . addslashes($stmt->error) . "',
                        confirmButtonColor: '#d33'
                    });
                </script>";
            }

            $stmt->close();
        }
        $checkStmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Database connection failed');</script>";
    }
}

//UPDATE QUERY
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdate'])) {
    $conn = connectToDB();
    $list_id = $_POST['listId'];
    $subject_id = $_GET['subject_id'];
    $student_set = $_POST['editSet'];

    if ($conn) {

        $checkStmt = $conn->prepare("SELECT * FROM student_list WHERE subject_id = ? AND student_set = ?");
        $checkStmt->bind_param("is", $subject_id, $student_set);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Class already exists!',
                                    confirmButtonColor: '#d33'
                                });
                            });
                        </script>";
        } else {

            $stmt = $conn->prepare("UPDATE student_list SET student_set = ? WHERE list_id = ?");
            $stmt->bind_param("si", $student_set, $list_id);

            if ($stmt->execute()) {

                echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Updated Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Refresh the page after SweetAlert closes
                                    window.location.href = window.location.href;
                                });
                            });
                        </script>";
            } else {
                echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '" . addslashes($stmt->error) . "',
                                confirmButtonColor: '#d33'
                            });
                        </script>";
            }
            $stmt->close();
        }
        $checkStmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Database connection failed');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('includes/header.php'); ?>
</head>

<body>
    <!-- Sidebar -->
    <?php
    include('includes/sidebar.php');
    ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fa fa-book me-2"></i>My Subjects</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-student-list-btn" data-bs-toggle="modal" data-bs-target="#add-student-list-modal">
                    <i class="fas fa-plus me-1"></i>Create Class
                </button>
            </div>
        </div>

        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Subjects > Class List</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search List...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>Set</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $subject = $_GET['subject_id'];
                        $sy = $_GET['sy'];
                        $sql = "SELECT * 
                                 FROM student_list
                                 WHERE subject_id = ?
                                 ORDER BY student_set ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $subject);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["student_set"] . "</td>";
                                echo "<td>
                                      <a class='btn btn-sm btn-outline-primary' 
                                      href='teacher-input-grades.php?student_set=" . $row['student_set'] . "&subject_id=" . $subject .
                                    "&sy=" . $sy . "&list_id=" . $row['list_id'] . "'>
                                          <i class='fas fa-eye me-2'></i>View
                                      </a>
                                      
                                      <a class='btn btn-sm btn-outline-secondary me-1 update-list-btn'
                                        data-id='" . $row['list_id'] . "'
                                        data-set='" . $row['student_set'] . "'
                                        data-bs-toggle='modal' 
                                        data-bs-target='#dropListModal'>
                                        <i class='fas fa-edit me-2'></i>Edit
                                        </a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo '<div class="col-12">';
                            echo '<div class="alert alert-info">No Data found.</div>';
                            echo '</div>';
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Student List Modal -->
        <div class="modal fade" id="add-student-list-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Add New Student List</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <input type="hidden" name="subject" value="<?php echo $subject; ?>">
                                    <label for="course" class="form-label">Set</label>
                                    <select class="form-select" name="set" id="set" required>
                                        <option value="">Select Set</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnAdd">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update List Modal -->
        <div class="modal fade" id="dropListModal" tabindex="-1" role="dialog" aria-labelledby="dropListModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dropListModal">Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <input type="hidden" id="listId" name="listId">
                                    <label for="course" class="form-label">Set</label>
                                    <select class="form-select" name="editSet" id="editSet" required>
                                        <option value="">Select Set</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-success" name="btnUpdate">Yes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.update-list-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('listId').value = btn.getAttribute('data-id');
                document.getElementById('editSet').value = btn.getAttribute('data-set');
            });
        });

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