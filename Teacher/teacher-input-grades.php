<?php
include('../database.php');
include '../includes/activity_logger.php';

//array student informations
if (isset($_GET['student_set']) && isset($_GET['subject_id'])) {
    $set = $_GET['student_set'];;
    $conn = connectToDB();

    $sql = "SELECT * FROM students WHERE course=? ORDER BY lastname ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $set);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
}

$conn = connectToDB();
$sql = "SELECT * 
        FROM subjects s
        INNER JOIN schoolyear sy ON sy.schoolyear_id = s.schoolyear_id
        WHERE s.subject_id = ? AND s.schoolyear_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_GET['subject_id'], $_GET['sy']);
$stmt->execute();
$result = $stmt->get_result();
$details = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $details[] = $row;
    }
}

?>
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
                    <?php foreach ($details as $detail): ?>
                        <div class="col-md-6">
                            <h5>All Students > <?php echo $detail['subject'] . " - " . $detail['schoolyear'] . ", " . $detail['semester'] . " Semester"; ?></h5>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Students...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php

            //array student grades
            $set = $_GET['student_set'];
            $subject = $_GET['subject_id'];
            $sy = $_GET['sy'];
            $conn = connectToDB();
            $sql = "SELECT s.*, g.prelim, g.midterm, g.finals, g.comment, g.average, g.equivalent, g.remarks
                    FROM students s
                    LEFT JOIN grades g ON s.student_id = g.student_id 
                    AND g.subject_id = ?
                    WHERE s.course = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $subject, $set);
            $stmt->execute();
            $result = $stmt->get_result();
            $grades = array();
            while ($row = $result->fetch_assoc()) {
                $grades[$row['student_id']] = $row;
            }

            //insert/update grades query
            $teacher_id = $_SESSION['id'] ?? null;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['grades']) && is_array($_POST['grades'])) {
                    $conn = connectToDB();
                    $subject_id = $_POST['subject_id'];
                    $schoolyear_id = $_POST['sy_id'];
                    $success = true;
                    $error_message = '';

                    foreach ($_POST['grades'] as $student_id => $gradeData) {
                        $prelim = isset($gradeData['prelim']) && $gradeData['prelim'] !== '' ?
                            $conn->real_escape_string($gradeData['prelim']) : NULL;
                        $midterm = isset($gradeData['midterm']) && $gradeData['midterm'] !== '' ?
                            $conn->real_escape_string($gradeData['midterm']) : NULL;
                        $finals = isset($gradeData['finals']) && $gradeData['finals'] !== '' ?
                            $conn->real_escape_string($gradeData['finals']) : NULL;
                        $average = floatval($gradeData['average'] ?? 0);
                        $equivalent = floatval($gradeData['equivalent'] ?? 0);
                        $remarks = $conn->real_escape_string($gradeData['remarks'] ?? '');
                        $comment = $conn->real_escape_string($gradeData['comment'] ?? '');

                        $sql = "INSERT INTO grades 
                                (subject_id, teacher_id, student_id, schoolyear_id, prelim, midterm, finals, average, equivalent, remarks, comment) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE
                                prelim = VALUES(prelim), 
                                midterm = VALUES(midterm), 
                                finals = VALUES(finals), 
                                average = VALUES(average),
                                equivalent = VALUES(equivalent),
                                remarks = VALUES(remarks),
                                comment = VALUES(comment)";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iiiidddddss", $subject_id, $teacher_id, $student_id, $schoolyear_id, $prelim, $midterm, $finals, $average, $equivalent, $remarks, $comment);

                        if (!$stmt->execute()) {
                            $success = false;
                            $error_message = $stmt->error;
                            break;
                        }
                    }

                    if ($success) {
                        logActivity($conn, $teacher_id, $_SESSION['user_type'], 'SUBMIT_GRADE', "submitted a Grade:" . $detail['subject'] . " S.Y. - " . $detail['schoolyear'] . ", " . $detail['semester'] . " Semester");

                        echo "<script>
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Grades saved successfully!',
                                    icon: 'success',
                                    confirmButtonColor: '#0d6efd'
                                }).then((result) => {
                                    // Refresh the page while maintaining the same URL parameters
                                    window.location.href = window.location.href;
                                });
                            </script>";
                    }
                    $stmt->close();
                    $conn->close();
                }
            }

            ?>
            <div class="table-responsive">
                <form id="gradesForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']); ?>" method="post">
                    <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject); ?>">
                    <input type="hidden" name="sy_id" value="<?php echo htmlspecialchars($sy); ?>">
                    <table class="table table-hover">
                        <thead>
                            <th>Student Name</th>
                            <th>Prelim</th>
                            <th>Midterm</th>
                            <th>Finals</th>
                            <th>Average</th>
                            <th>Equivalent</th>
                            <th>Remarks</th>
                            <th>Comments</th>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $studentinfo): ?>
                                <?php
                                $student_id = $studentinfo['student_id'];
                                $currentGrades = isset($grades[$student_id]) ? $grades[$student_id] : [];
                                ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($studentinfo['lastname'] . ', ' . $studentinfo['firstname']); ?>
                                    </td>
                                    <td>
                                        <input type='number' name="grades[<?php echo $student_id; ?>][prelim]"
                                            class='form-control form-control-sm grade-input'
                                            min='0' max='100' step='0.01'
                                            value="<?php echo isset($currentGrades['prelim']) ? htmlspecialchars($currentGrades['prelim']) : ''; ?>">
                                    </td>
                                    <td>
                                        <input type='number' name="grades[<?php echo $student_id; ?>][midterm]"
                                            class='form-control form-control-sm grade-input'
                                            min='0' max='100' step='0.01'
                                            value="<?php echo isset($currentGrades['midterm']) ? htmlspecialchars($currentGrades['midterm']) : ''; ?>">
                                    </td>
                                    <td>
                                        <input type='number' name="grades[<?php echo $student_id; ?>][finals]"
                                            class='form-control form-control-sm grade-input'
                                            min='0' max='100' step='0.01'
                                            value="<?php echo isset($currentGrades['finals']) ? htmlspecialchars($currentGrades['finals']) : ''; ?>">
                                    </td>
                                    <td>
                                        <span class='form-control-plaintext'>
                                            <?php echo isset($currentGrades['average']) ? number_format($currentGrades['average'], 2) : '0.00'; ?>
                                        </span>
                                        <input type='hidden' name="grades[<?php echo $student_id; ?>][average]"
                                            value="<?php echo isset($currentGrades['average']) ? $currentGrades['average'] : '0'; ?>">
                                    </td>
                                    <td>
                                        <span class='form-control-plaintext'>
                                            <?php echo isset($currentGrades['equivalent']) ? $currentGrades['equivalent'] : '5.00'; ?>
                                        </span>
                                        <input type='hidden' name="grades[<?php echo $student_id; ?>][equivalent]"
                                            value="<?php echo isset($currentGrades['equivalent']) ? $currentGrades['equivalent'] : '5.00'; ?>">
                                    </td>
                                    <td>
                                        <span class='form-control-plaintext'>
                                            <?php echo isset($currentGrades['remarks']) ? $currentGrades['remarks'] : 'Pending'; ?>
                                        </span>
                                        <input type='hidden' name="grades[<?php echo $student_id; ?>][remarks]"
                                            value="<?php echo isset($currentGrades['remarks']) ? htmlspecialchars($currentGrades['remarks']) : 'Pending'; ?>">
                                    </td>
                                    <td>
                                        <input type='text' name="grades[<?php echo $student_id; ?>][comment]"
                                            class='form-control form-control-sm'
                                            value="<?php echo isset($currentGrades['comment']) ? htmlspecialchars($currentGrades['comment']) : ''; ?>"
                                            placeholder="Enter comments">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save All Grades
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('gradesForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Stop immediate submission

            // Preserve current URL parameters
            const currentUrl = window.location.href;
            this.action = currentUrl;

            Swal.fire({
                title: 'Save all grades?',
                text: 'This will save/overwrite the grades for all listed students. Continue?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading and submit the form
                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            // Submit the form after a brief delay to show loading state
                            setTimeout(() => {
                                document.getElementById('gradesForm').submit();
                            }, 500);
                        }
                    });
                }
                // If cancelled, do nothing - form won't submit
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Select All functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    studentCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Search functionality
            const searchInput = document.getElementById('studentSearch');
            const studentItems = document.querySelectorAll('.student-item');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    studentItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Update Select All when individual checkboxes change
            studentCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const gradeInputs = document.querySelectorAll('.grade-input');

            gradeInputs.forEach(input => {
                input.addEventListener('input', calculateGrades);
            });

            function calculateGrades() {
                const studentRow = this.closest('tr');
                const prelimInput = studentRow.querySelector('input[name*="[prelim]"]');
                const midtermInput = studentRow.querySelector('input[name*="[midterm]"]');
                const finalsInput = studentRow.querySelector('input[name*="[finals]"]');

                const prelim = prelimInput.value.trim();
                const midterm = midtermInput.value.trim();
                const finals = finalsInput.value.trim();

                let average = 0;
                let equivalent = '5.00';
                let remarks = 'Pending';

                // Only calculate if ALL three grades have values
                if (prelim !== '' && midterm !== '' && finals !== '') {
                    const prelimNum = parseFloat(prelim) || 0;
                    const midtermNum = parseFloat(midterm) || 0;
                    const finalsNum = parseFloat(finals) || 0;

                    // Calculate average
                    average = (prelimNum + midtermNum + finalsNum) / 3;

                    // Calculate equivalent and remarks only if average is valid
                    if (average >= 90) {
                        equivalent = '1.50';
                        remarks = 'Passed';
                    } else if (average >= 85) {
                        equivalent = '2.00';
                        remarks = 'Passed';
                    } else if (average >= 80) {
                        equivalent = '2.50';
                        remarks = 'Passed';
                    } else if (average >= 75) {
                        equivalent = '3.00';
                        remarks = 'Passed';
                    } else {
                        equivalent = '5.00';
                        remarks = 'Failed';
                    }
                }

                // Update display
                studentRow.querySelector('span:first-child').textContent = average.toFixed(2);
                studentRow.querySelector('input[name*="[average]"]').value = average.toFixed(2);

                studentRow.querySelectorAll('span')[1].textContent = equivalent;
                studentRow.querySelector('input[name*="[equivalent]"]').value = equivalent;

                studentRow.querySelectorAll('span')[2].textContent = remarks;
                studentRow.querySelector('input[name*="[remarks]"]').value = remarks;
            }

            // Calculate initial values on page load
            gradeInputs.forEach(input => {
                if (input.value) {
                    calculateGrades.call(input);
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.querySelector('tbody');
            const tableRows = Array.from(tableBody.querySelectorAll('tr'));

            // Function to perform the search
            function performSearch(searchTerm) {
                const query = searchTerm.toLowerCase().trim();
                let visibleRows = 0;

                tableRows.forEach(function(row) {
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
                <td colspan="9" class="text-center py-4" style="color: #6c757d;">
                    <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                    <br>
                    No students found matching your search
                </td>
            `;
                    tableBody.appendChild(noResultsRow);
                } else if (!show && noResultsRow) {
                    // Remove no results row if it exists
                    noResultsRow.remove();
                }
            }

            // Add event listener for real-time search
            searchInput.addEventListener('input', function(e) {
                performSearch(e.target.value);
            });

            // Add event listener for paste events
            searchInput.addEventListener('paste', function(e) {
                // Small delay to ensure pasted content is processed
                setTimeout(function() {
                    performSearch(searchInput.value);
                }, 10);
            });



            // Optional: Add search icon click functionality
            const searchIcon = document.querySelector('#searchInput + .input-group-text');
            if (searchIcon) {
                searchIcon.addEventListener('click', function() {
                    searchInput.focus();
                });
            }
        });
    </script>
</body>

</html>