<?php
include('../database.php');
include '../includes/activity_logger.php';
include 'includes/grade_submission_helper.php';

//array student informations
if (isset($_GET['student_set']) && isset($_GET['subject_id'])) {
    $set = $_GET['student_set'];;
    $conn = connectToDB();

    $sql = "SELECT * FROM students WHERE course=? AND status = '1' ORDER BY lastname ASC";
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
            $list_id = $_GET['list_id'];

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


            //insert/update grades
            $teacher_id = $_SESSION['id'] ?? null;
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['request_approval'])) {
                if (isset($_POST['grades']) && is_array($_POST['grades'])) {
                    $conn = connectToDB();
                    $subject_id = $_POST['subject_id'];
                    $list_id = $_POST['list_id'];
                    $schoolyear_id = $_POST['sy_id'];

                    // Check if teacher can submit
                    $check = canSubmitGrades($conn, $teacher_id, $subject_id, $list_id, $schoolyear_id);

                    if (!$check['can_submit']) {
                        echo "<script>
                                Swal.fire({
                                    title: 'Cannot Submit!',
                                    text: '" . addslashes($check['reason']) . "',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            </script>";
                    } else {
                        $success = true;
                        $error_message = '';

                        // Start transaction
                        $conn->begin_transaction();

                        try {
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
                                    throw new Exception($stmt->error);
                                }
                                $stmt->close();
                            }

                            // Increment submission count
                            if (!incrementSubmissionCount($conn, $subject_id, $teacher_id, $list_id, $schoolyear_id)) {
                                throw new Exception("Failed to update submission count");
                            }

                            $conn->commit();

                            // Get new count
                            $new_count = getSubmissionCount($conn, $subject_id, $teacher_id, $list_id, $schoolyear_id);
                            $submissions_left = 3 - $new_count;

                            logActivity(
                                $conn,
                                $teacher_id,
                                $_SESSION['user_type'],
                                'SUBMIT_GRADE',
                                "submitted grades for " . $detail['subject'] . " S.Y. - " .
                                    $detail['schoolyear'] . ", " . $detail['semester'] . " Semester (Submission $new_count/3)"
                            );

                            $message = "Grades saved successfully! (Submission $new_count of 3)";
                            if ($submissions_left > 0) {
                                $message .= "You have $submissions_left more submission(s) left.";
                            } else {
                                $message .= "You have reached the maximum submissions. Further changes require admin approval.";
                            }

                            echo "<script>
                                    Swal.fire({
                                        title: 'Success!',
                                        text: '" . addslashes($message) . "',
                                        icon: 'success',
                                        confirmButtonColor: '#0d6efd'
                                    }).then((result) => {
                                        window.location.href = window.location.href;
                                    });
                                </script>";
                        } catch (Exception $e) {
                            $conn->rollback();
                            echo "<script>
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to save grades: " . addslashes($e->getMessage()) . "',
                                        icon: 'error',
                                        confirmButtonColor: '#dc3545'
                                    });
                                </script>";
                        }

                        $conn->close();
                    }
                }
            }

            ?>
            <div class="alert alert-info mb-3">
                <?php
                $conn = connectToDB();
                $check = canSubmitGrades($conn, $teacher_id, $_GET['subject_id'], $_GET['list_id'], $_GET['sy']);
                $conn->close();

                if ($check['can_submit']) {
                    echo "<strong>Submission Status:</strong> You have " . $check['submissions_left'] . " submission(s) remaining.";
                } else {
                    echo "<strong>Submission Status:</strong> " . $check['reason'];

                    if (isset($check['needs_approval']) || isset($check['is_pending'])) {
                        $conn = connectToDB();
                        $has_pending = hasPendingRequest($conn, $teacher_id, $_GET['subject_id'],  $_GET['list_id'], $_GET['sy']);
                        $conn->close();

                        if (!$has_pending) {
                            echo '<button type="button" class="btn btn-warning btn-sm ms-3" id="requestApprovalBtn">
                                    <i class="fas fa-hand-paper me-1"></i>Request Approval
                                </button>';
                        } else {
                            echo '<span class="badge bg-warning text-dark ms-3">
                                    <i class="fas fa-clock me-1"></i>Approval Pending
                                </span>';
                        }
                    }
                }
                ?>
            </div>

            <div class="table-responsive">
                <form id="gradesForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']); ?>" method="post">
                    <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject); ?>">
                    <input type="hidden" name="sy_id" value="<?php echo htmlspecialchars($sy); ?>">
                    <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($list_id); ?>">
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

                // Add input validation
                input.addEventListener('blur', function() {
                    let value = parseFloat(this.value);
                    if (isNaN(value)) {
                        this.value = '';
                        calculateGrades.call(this);
                        return;
                    }

                    // Validate range
                    if (value < 0) this.value = 0;
                    if (value > 100) this.value = 100;

                    // Round to 2 decimal places
                    this.value = parseFloat(this.value).toFixed(2);
                    calculateGrades.call(this);
                });
            });

            function calculateGrades() {
                const studentRow = this.closest('tr');
                if (!studentRow) return;

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

                    // Calculate average with proper rounding
                    average = (prelimNum + midtermNum + finalsNum) / 3;
                    average = Math.round(average * 100) / 100;

                    // Calculate equivalent with proper decimal ranges
                    if (average >= 98) {
                        equivalent = '1.00';
                        remarks = 'Passed';
                    } else if (average >= 95) {
                        equivalent = '1.25';
                        remarks = 'Passed';
                    } else if (average >= 92) {
                        equivalent = '1.50';
                        remarks = 'Passed';
                    } else if (average >= 89) {
                        equivalent = '1.75';
                        remarks = 'Passed';
                    } else if (average >= 86) {
                        equivalent = '2.00';
                        remarks = 'Passed';
                    } else if (average >= 83) {
                        equivalent = '2.25';
                        remarks = 'Passed';
                    } else if (average >= 80) {
                        equivalent = '2.50';
                        remarks = 'Passed';
                    } else if (average >= 76) {
                        equivalent = '2.75';
                        remarks = 'Passed';
                    } else if (average >= 75) {
                        equivalent = '3.00';
                        remarks = 'Passed';
                    } else {
                        equivalent = '5.00';
                        remarks = 'Failed';
                    }
                }

                // Update display with proper selectors
                const averageDisplay = studentRow.querySelector('td:nth-child(5) span');
                const equivalentDisplay = studentRow.querySelector('td:nth-child(6) span');
                const remarksDisplay = studentRow.querySelector('td:nth-child(7) span');

                if (averageDisplay) averageDisplay.textContent = average.toFixed(2);
                if (equivalentDisplay) equivalentDisplay.textContent = equivalent;
                if (remarksDisplay) remarksDisplay.textContent = remarks;

                // Update hidden inputs
                const averageInput = studentRow.querySelector('input[name*="[average]"]');
                const equivalentInput = studentRow.querySelector('input[name*="[equivalent]"]');
                const remarksInput = studentRow.querySelector('input[name*="[remarks]"]');

                if (averageInput) averageInput.value = average.toFixed(2);
                if (equivalentInput) equivalentInput.value = equivalent;
                if (remarksInput) remarksInput.value = remarks;
            }

            // Calculate initial values on page load
            gradeInputs.forEach(input => {
                if (input.value) {
                    setTimeout(() => calculateGrades.call(input), 100);
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

        <?php if (!$check['can_submit']): ?>
            const gradesForm = document.querySelector('#gradesForm');
            if (gradesForm) {
                const submitBtn = gradesForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('disabled');
                }

                const gradeInputs = gradesForm.querySelectorAll('input[type="number"]');
                gradeInputs.forEach(input => {
                    input.readOnly = true;
                });
            }
        <?php endif; ?>

        // Request approval functionality - WITH NULL CHECK
        const requestApprovalBtn = document.getElementById('requestApprovalBtn');
        if (requestApprovalBtn) {
            requestApprovalBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Request Admin Approval',
                    input: 'textarea',
                    inputLabel: 'Please provide a reason for additional submission',
                    inputPlaceholder: 'Enter your reason here...',
                    inputAttributes: {
                        'aria-label': 'Reason for approval request',
                        'rows': 4
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Submit Request',
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to provide a reason!'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit approval request
                        const formData = new FormData();
                        formData.append('subject_id', <?php echo $_GET['subject_id']; ?>);
                        formData.append('sy_id', <?php echo $_GET['sy']; ?>);
                        formData.append('list_id', <?php echo $_GET['list_id']; ?>);
                        formData.append('reason', result.value);

                        Swal.fire({
                            title: 'Submitting...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('api/request_approval.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Request Submitted!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#0d6efd'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message,
                                        icon: 'error',
                                        confirmButtonColor: '#dc3545'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Failed to submit request. Please try again.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            });
                    }
                });
            });
        }
    </script>
</body>

</html>