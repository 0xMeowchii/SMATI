<?php
include('../database.php');

//array student informations
if (isset($_GET['student_set']) && isset($_GET['subject_id'])) {
    $set = $_GET['student_set'];;
    $conn = connectToDB();

    $sql = "SELECT * FROM students WHERE course=?";
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../Teacher/teacher.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-user me-2"></i>My Subjects</h4>
        </div>

        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Students</h5>
                    </div>
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
            $conn = connectToDB();
            $sql = "SELECT s.*, g.prelim, g.midterm, g.prefinals, g.finals, g.comment, g.average, g.equivalent, g.remarks
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSaveGrades'])) {
                if (isset($_POST['grades']) && is_array($_POST['grades'])) {
                    $conn = connectToDB();
                    $subject_id = $_POST['subject_id'] ?? '';
                    $success = true;
                    $error_message = '';

                    foreach ($_POST['grades'] as $student_id => $gradeData) {
                        $prelim = isset($gradeData['prelim']) && $gradeData['prelim'] !== '' ?
                            $conn->real_escape_string($gradeData['prelim']) : NULL;
                        $midterm = isset($gradeData['midterm']) && $gradeData['midterm'] !== '' ?
                            $conn->real_escape_string($gradeData['midterm']) : NULL;
                        $prefinals = isset($gradeData['prefinals']) && $gradeData['prefinals'] !== '' ?
                            $conn->real_escape_string($gradeData['prefinals']) : NULL;
                        $finals = isset($gradeData['finals']) && $gradeData['finals'] !== '' ?
                            $conn->real_escape_string($gradeData['finals']) : NULL;
                        $average = floatval($gradeData['average'] ?? 0);
                        $equivalent = floatval($gradeData['equivalent'] ?? 0);
                        $remarks = $conn->real_escape_string($gradeData['remarks'] ?? '');
                        $comment = $conn->real_escape_string($gradeData['comment'] ?? '');

                        $sql = "INSERT INTO grades 
                                (subject_id, teacher_id, student_id, prelim, midterm, prefinals, finals, average, equivalent, remarks, comment) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE 
                                prelim = VALUES(prelim), 
                                midterm = VALUES(midterm), 
                                prefinals = VALUES(prefinals), 
                                finals = VALUES(finals), 
                                average = VALUES(average),
                                equivalent = VALUES(equivalent),
                                remarks = VALUES(remarks),
                                comment = VALUES(comment)";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iiiddddddss", $subject_id, $teacher_id, $student_id, $prelim, $midterm, $prefinals, $finals, $average, $equivalent, $remarks, $comment);

                        if (!$stmt->execute()) {
                            $success = false;
                            $error_message = $stmt->error;
                            break;
                        }
                    }

                    if ($success) {
                        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
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
                            // show loading modal then submit
                            Swal.fire({
                                title: 'Saving...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    setTimeout(() => window.location.href = 'teacher-student-list.php?subject_id=" . urlencode($subject_id) . "', 1000);
                                }
                            });
                        }
                    });
                });
                 
            </script>";
                    } else {
                        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '" . addslashes($error_message) . "',
                    confirmButtonColor: '#d33'
                });
            </script>";
                    }

                    $stmt->close();
                    $conn->close();
                }
            }

            ?>
            <div class="table-responsive">
                <form id="gradesForm" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject); ?>">
                    <table class="table table-hover">
                        <thead>
                            <th>Student Name</th>
                            <th>Prelim</th>
                            <th>Midterm</th>
                            <th>Prefinals</th>
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
                                        <input type='number' name="grades[<?php echo $student_id; ?>][prefinals]"
                                            class='form-control form-control-sm grade-input'
                                            min='0' max='100' step='0.01'
                                            value="<?php echo isset($currentGrades['prefinals']) ? htmlspecialchars($currentGrades['prefinals']) : ''; ?>">
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
                                            <?php echo isset($currentGrades['remarks']) ? $currentGrades['remarks'] : 'Failed'; ?>
                                        </span>
                                        <input type='hidden' name="grades[<?php echo $student_id; ?>][remarks]"
                                            value="<?php echo isset($currentGrades['remarks']) ? htmlspecialchars($currentGrades['remarks']) : 'Failed'; ?>">
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
                        <button type="submit" name="btnSaveGrades" class="btn btn-primary">
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
                const studentId = this.name.match(/\[(\d+)\]/)[1];

                const prelim = parseFloat(studentRow.querySelector('input[name*="[prelim]"]').value) || 0;
                const midterm = parseFloat(studentRow.querySelector('input[name*="[midterm]"]').value) || 0;
                const prefinals = parseFloat(studentRow.querySelector('input[name*="[prefinals]"]').value) || 0;
                const finals = parseFloat(studentRow.querySelector('input[name*="[finals]"]').value) || 0;

                // Calculate average
                const average = (prelim + midterm + prefinals + finals) / 4;

                // Calculate equivalent and remarks
                let equivalent = '5.00';
                let remarks = 'Failed';

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