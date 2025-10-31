<?php
require_once 'includes/session.php';
include('../database.php');
include '../includes/activity_logger.php';
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
            <h4><i class="fas fa-chart-bar me-2"></i>Academics Management</h4>
        </div>

        <?php

        //UPDATE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
            $conn = connectToDB();
            $subject_id = $_POST['editId'];
            $subjectname = $_POST['editName'];
            $teacher_id = $_POST['editTeacher'];
            $yearlevel = $_POST['editYearlevel'];
            $schoolyear_id = $_POST['editSchoolyear'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE subjects 
                                        SET subject=?,
                                            course=?,
                                            teacher_id=?,
                                            yearlevel=?,
                                            schoolyear_id=?
                                        WHERE subject_id=?");
                $stmt->bind_param("ssisii", $subjectname, $course, $teacher_id, $yearlevel, $schoolyear_id, $subject_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_SUBJECT', "Updated Subject Details: $subjectname");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Subject Updated Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
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
                $conn->close();
            }
        }

        //DROP QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDrop'])) {
            $conn = connectToDB();
            $subject_id = $_POST['subjectId'];
            $subjectname = $_POST['dropSubjectname'];
            $schoolyear = $_POST['dropSchoolyear'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE subjects SET status = '0' WHERE subject_id=?");
                $stmt->bind_param("i", $subject_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DROP_SUBJECT', "Drop Subject: $subjectname, $schoolyear");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Subject Drop Successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
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
                $conn->close();
            } else {
                echo "<script>alert('Database connection failed');</script>";
            }
        }
        ?>

        <!-- Academics Table -->
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Subjects</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Subjects...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive flex-grow-1 overflow-auto" style="max-height:600px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Teacher</th>
                            <th>Year Level</th>
                            <th>School Year & Semester</th>
                            <th>Action</th>
                        </tr>
                    <tbody>
                        <?php
                        $conn = connectToDB();
                        $sql = "SELECT * 
                            FROM subjects s
                            INNER JOIN teachers t ON s.teacher_id = t.teacher_id
                            INNER JOIN schoolyear sy ON sy.schoolyear_id = s.schoolyear_id
                            WHERE s.status='1'";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["subject"] . "</td>";
                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                echo "<td>" . $row["yearlevel"] . "</td>";
                                echo "<td>" . $row["schoolyear"] . ", " . $row["semester"] . " Semester" . "</td>";
                                echo "<td>
                                <a class='btn btn-sm btn-outline-primary me-1 view-subject-btn'
                                data-name='" . $row['subject'] . "'
                                data-teacher='" . $row['lastname'] . ", " . $row['firstname'] . "'
                                data-yearlevel='" . $row['yearlevel'] . "'
                                data-schoolyear='" . $row['schoolyear'] . ", " . $row['semester'] . " Semester" . "'
                                data-createdAt='" . (new DateTime($row['subject_created']))->format('m-d-Y h:i A') . "'
                                data-bs-toggle='modal' 
                                data-bs-target='#viewSubjectModal'>
                                    <i class='fas fa-eye'></i>
                                </a>

                                <a class='btn btn-sm btn-outline-secondary me-1 edit-subject-btn'
                                data-id='" . $row['subject_id'] . "'
                                data-name='" . $row['subject'] . "'
                                data-teacher='" . $row['teacher_id'] . "'
                                data-yearlevel='" . $row['yearlevel'] . "'
                                data-schoolyear='" . $row['schoolyear_id'] . "'
                                data-bs-toggle='modal' 
                                data-bs-target='#editSubjectModal'>
                                    <i class='fas fa-edit'></i>
                                </a>

                                <a class='btn btn-sm btn-outline-danger me-1 drop-subject-btn'
                                data-id='" . $row['subject_id'] . "'
                                data-name='" . $row['subject'] . "'
                                data-schoolyear='" . $row['schoolyear'] . ", " . $row['semester'] . " Semester" . "'
                                data-bs-toggle='modal' 
                                 data-bs-target='#dropSubjectModal'>
                                    <i class='fas fa-trash'></i>
                                </a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "0 results";
                        }
                        ?>
                    </tbody>
                    </thead>
                </table>
            </div>

        </div>

        <!-- Edit Subject Modal -->
        <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editSubjectModal">Edit Subject</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" id="editName" name="editName" required>
                                </div>
                                <div>
                                    <label class="form-label">Assign a Teacher</label>
                                    <select class="form-select" id="editTeacher" name="editTeacher" required>
                                        <option value="">Select Teacher</option>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM teachers WHERE status = '1'";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                $fullname = $row['lastname'] . ", " . $row['firstname'];
                                                echo "<option value='" . $row['teacher_id'] . "'>" . $fullname . "</option>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Year Level</label>
                                    <div class="d-flex gap-3">
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit1st-outlined" value="1st">
                                        <label class="btn btn-outline-success" for="edit1st-outlined">1st Year</label>
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit2nd-outlined" value="2nd">
                                        <label class="btn btn-outline-success" for="edit2nd-outlined">2nd Year</label>
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit3rd-outlined" value="3rd">
                                        <label class="btn btn-outline-success" for="edit3rd-outlined">3rd Year</label>
                                        <input type="radio" class="btn-check" name="editYearlevel" id="edit4th-outlined" value="4th">
                                        <label class="btn btn-outline-success" for="edit4th-outlined">4th Year</label>
                                    </div>
                                </div>
                                <div>
                                    <label for="student-course" class="form-label">School Year & Semester</label>
                                    <select class="form-select" id="editSchoolyear" name="editSchoolyear" required>
                                        <option value="">Select School Year & Semester</option>
                                        <?php
                                        $conn = connectToDB();
                                        $sql = "SELECT * FROM schoolyear";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                $schoolyear = $row['schoolyear'] . ", " . $row['semester'] . ' Semester';
                                                echo "<option value='" . $row['schoolyear_id'] . "'>" . $schoolyear . "</option>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnEdit">Update Subject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Subject Modal -->
        <div class="modal fade" id="viewSubjectModal" tabindex="-1" aria-labelledby="viewSubjectModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="viewSubjectModal">Subject Details</h3>
                    </div>
                    <div class="modal-body">
                        <?php
                        echo "<p><strong>Subject Name: </strong><span id='modalSubjectName'></span></p>
                              <p><strong>Teacher: </strong><span id='modalSubjectTeacher'></span></p>
                              <p><strong>Year Level: </strong><span id='modalSubjectYearlevel'></span></p>
                              <p><strong>School Year & Semester: </strong><span id='modalSubjectSchoolyear'></span></p>
                              <p class='pt-3 border-top'><strong>CreatedAt: </strong><span id='createdAt'></span></p>";
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drop Subject Modal -->
        <div class="modal fade" id="dropSubjectModal" tabindex="-1" role="dialog" aria-labelledby="dropSubjectModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dropSubjectModal">Confirm Drop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to drop this Subject?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="subjectId" id="subjectId">
                            <input type="hidden" name="dropSubjectname" id="dropSubjectname">
                            <input type="hidden" name="dropSchoolyear" id="dropSchoolyear">
                            <button type="submit" class="btn btn-danger" name="btnDrop">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.view-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('modalSubjectName').textContent = btn.getAttribute('data-name');
                document.getElementById('modalSubjectTeacher').textContent = btn.getAttribute('data-teacher');
                document.getElementById('modalSubjectYearlevel').textContent = btn.getAttribute('data-yearlevel');
                document.getElementById('modalSubjectSchoolyear').textContent = btn.getAttribute('data-schoolyear');
                document.getElementById('createdAt').textContent = btn.getAttribute('data-createdAt');
            });
        });

        document.querySelectorAll('.edit-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editName').value = btn.getAttribute('data-name');
                document.getElementById('editTeacher').value = btn.getAttribute('data-teacher');

                // Fixed: Properly set radio button value
                const yearLevel = btn.getAttribute('data-yearlevel');
                const radioButton = document.querySelector(`input[name="editYearlevel"][value="${yearLevel}"]`);
                if (radioButton) {
                    radioButton.checked = true;
                }

                document.getElementById('editSchoolyear').value = btn.getAttribute('data-schoolyear');
            });
        });
        document.querySelectorAll('.drop-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('dropSubjectname').value = btn.getAttribute('data-name');
                document.getElementById('subjectId').value = btn.getAttribute('data-id');
                document.getElementById('dropSchoolyear').value = btn.getAttribute('data-schoolyear');
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
                <td colspan="5" class="text-center py-4" style="color: #6c757d;">
                    <i class="fas fa-search mb-2" style="font-size: 2em; opacity: 0.5;"></i>
                    <br>
                    No subjects found matching your search
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