<?php
include 'includes/session.php';
include('../database.php');
include '../includes/activity_logger.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
        .no-results-message {
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .col {
            transition: all 0.3s ease;
        }
    </style>

<body>
    <!-- Sidebar -->
    <?php
    include('sidebar.php');

    //Fetch subjects
    $conn = connectToDB();
    $sql = "SELECT * 
            FROM subjects s
            INNER JOIN teachers t ON s.teacher_id = t.teacher_id
            INNER JOIN schoolyear sy ON sy.schoolyear_id = s.schoolyear_id
            WHERE s.teacher_id = ? AND s.schoolyear_id = ? AND s.status = '1' ORDER BY s.subject_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['id'], $_GET['sy']);
    $stmt->execute();
    $result = $stmt->get_result();

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = [
            'subject_id' => $row['subject_id'],
            'schoolyear' => $row['schoolyear_id'],
            'subject_name' => $row['subject'],
            'subject_code' => $row['subject_code'],
            'yearlevel' => $row['yearlevel']
        ];
    }
    ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fa fa-book me-2"></i>My Subjects</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-subject-btn" data-bs-toggle="modal" data-bs-target="#add-subjects-modal">
                    <i class="fas fa-plus me-1"></i>Add Subject
                </button>
            </div>
        </div>

        <!-- QUERY -->
        <?php

        //INSERT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
            $conn = connectToDB();
            $subjectcode = $_POST['subjectcode'];
            $subjectname = $_POST['subjectname'];
            $teacher_id = $_SESSION['id'];
            $yearlevel = $_POST['yearlevel'];
            $schoolyear_id = $_GET['sy'];
            $status = '1';

            if ($conn) {
                $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject, teacher_id, yearlevel, schoolyear_id, status) 
                                            VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisis", $subjectcode, $subjectname, $teacher_id, $yearlevel, $schoolyear_id, $status);

                if ($stmt->execute()) {
                    logActivity($conn, $teacher_id, $_SESSION['user_type'], 'CREATE_SUBJECT', "Created subject: $subjectname");
                    echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Subject Added Successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh the page after SweetAlert closes
                        window.location.href = window.location.href;
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

        //UPDATE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
            $conn = connectToDB();
            $subject_id = $_POST['editId'];
            $teacher_id = $_SESSION['id'];
            $subjectname = $_POST['editSubjectname'];
            $subjectcode = $_POST['editSubjectcode'];
            $yearlevel = $_POST['editYearlevel'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE subjects
                                        SET subject = ?, yearlevel = ?, subject_code = ?
                                        WHERE subject_id = ?");
                $stmt->bind_param("sssi", $subjectname, $yearlevel, $subjectcode ,$subject_id);

                if ($stmt->execute()) {

                    logActivity($conn, $teacher_id, $_SESSION['user_type'], 'UPDATE_SUBJECT', "Updated subject details: $subjectname");

                    echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Subject Updated Successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh the page after SweetAlert closes
                        window.location.href = window.location.href;
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

        //DROP QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDrop'])) {
            $conn = connectToDB();
            $teacher_id = $_SESSION['id'];
            $subject_id = $_POST['subjectId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE subjects SET status = '0' WHERE subject_id=?");
                $stmt->bind_param("i", $subject_id);

                if ($stmt->execute()) {

                    logActivity($conn, $teacher_id, $_SESSION['user_type'], 'DROP_SUBJECT', "Drop a subject.");

                    echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Subject Drop Successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh the page after SweetAlert closes
                        window.location.href = window.location.href;
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

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($subjects as $subject): ?>
                    <div class="col">
                        <div class="card text-center rounded-4">
                            <div class="card-header rounded-top-4 bg-primary text-white fw-bold">
                                <?php echo $subject['subject_code'] ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $subject['subject_name'] ?></h5>
                                <p class="card-text"><?php echo $subject['yearlevel'] . " Year" ?></p>

                            </div>
                            <div class="card-footer bg-white rounded-bottom-4">
                                <div class="d-inline">
                                    <a class='btn btn-sm btn-outline-primary me-1 view-subject-btn'
                                        href="teacher-student-list.php?subject_id=<?php echo urlencode($subject['subject_id']); ?>&sy=<?php echo urlencode($subject['schoolyear']); ?>">
                                        <i class='fas fa-eye'></i>
                                    </a>
                                    <a class='btn btn-sm btn-outline-secondary me-1 edit-subject-btn'
                                        data-id='<?php echo $subject['subject_id']; ?>'
                                        data-code='<?php echo $subject['subject_code']; ?>'
                                        data-name='<?php echo $subject['subject_name']; ?>'
                                        data-yearlevel='<?php echo $subject['yearlevel']; ?>'
                                        data-bs-toggle='modal'
                                        data-bs-target='#edit-subjects-modal'>
                                        <i class='fas fa-edit'></i>
                                    </a>
                                    <a class='btn btn-sm btn-outline-danger me-1 drop-subject-btn'
                                        data-id='<?php echo $subject['subject_id']; ?>'
                                        data-bs-toggle='modal'
                                        data-bs-target='#drop-subjects-modal'>
                                        <i class='fas fa-trash'></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add Subject Modal -->
        <div class="modal fade" id="add-subjects-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Add New Subject</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                            <div class="row g-3">
                                <div class="col">
                                    <label class="form-label">Subject Code</label>
                                    <input type="text" class="form-control" id="subjectcode" name="subjectcode" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" id="subjectname" name="subjectname" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Year Level</label>
                                    <div class="d-flex gap-3">
                                        <input type="radio" class="btn-check" name="yearlevel" id="1st-outlined" value="1st">
                                        <label class="btn btn-outline-success" for="1st-outlined">1st Year</label>
                                        <input type="radio" class="btn-check" name="yearlevel" id="2nd-outlined" value="2nd">
                                        <label class="btn btn-outline-success" for="2nd-outlined">2nd Year</label>
                                        <input type="radio" class="btn-check" name="yearlevel" id="3rd-outlined" value="3rd">
                                        <label class="btn btn-outline-success" for="3rd-outlined">3rd Year</label>
                                        <input type="radio" class="btn-check" name="yearlevel" id="4th-outlined" value="4th">
                                        <label class="btn btn-outline-success" for="4th-outlined">4th Year</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnAdd">Save Subject</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Subject Modal -->
        <div class="modal fade" id="edit-subjects-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Add New Subject</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3">
                                <div class="col">
                                    <label class="form-label">Subject Code</label>
                                    <input type="text" class="form-control" id="editSubjectcode" name="editSubjectcode" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" id="editSubjectname" name="editSubjectname" required>
                                </div>
                                <div class="mb-3">
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
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success" name="btnEdit">Update Subject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drop Subject Modal -->
        <div class="modal fade" id="drop-subjects-modal" tabindex="-1" role="dialog" aria-labelledby="dropSubjectModal" aria-hidden="true">
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
        document.querySelectorAll('.edit-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editSubjectcode').value = btn.getAttribute('data-code');
                document.getElementById('editSubjectname').value = btn.getAttribute('data-name');

                // Fixed: Properly set radio button value
                const yearLevel = btn.getAttribute('data-yearlevel');
                const radioButton = document.querySelector(`input[name="editYearlevel"][value="${yearLevel}"]`);
                if (radioButton) {
                    radioButton.checked = true;
                }
            });
        });

        document.querySelectorAll('.drop-subject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('subjectId').value = btn.getAttribute('data-id');
            });
        });

        // Enhanced search functionality for subjects
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const subjectCards = document.querySelectorAll('.col .card');

            // Store card data for better performance
            const cardData = Array.from(subjectCards).map(card => {
                const cardColumn = card.closest('.col');
                return {
                    element: card,
                    column: cardColumn,
                    title: card.querySelector('.card-title')?.textContent.toLowerCase() || '',
                    text: card.querySelector('.card-text')?.textContent.toLowerCase() || '',
                    header: card.querySelector('.card-header')?.textContent.toLowerCase() || '',
                    fullText: card.textContent.toLowerCase()
                };
            });

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let hasVisibleCards = false;

                    cardData.forEach(data => {
                        const matches = data.title.includes(searchTerm) ||
                            data.text.includes(searchTerm) ||
                            data.header.includes(searchTerm) ||
                            data.fullText.includes(searchTerm);

                        if (matches || searchTerm === '') {
                            data.column.style.display = 'block';
                            hasVisibleCards = true;
                        } else {
                            data.column.style.display = 'none';
                        }
                    });

                    showNoResultsMessage(!hasVisibleCards && searchTerm !== '');
                });

                // Add Enter key support to clear search
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        this.dispatchEvent(new Event('input'));
                    }
                });
            }

            function showNoResultsMessage(show) {
                const existingMessage = document.querySelector('.no-results-message');
                if (existingMessage) {
                    existingMessage.remove();
                }

                if (show) {
                    const searchTerm = searchInput.value;
                    const noResultsMessage = document.createElement('div');
                    noResultsMessage.className = 'no-results-message text-center py-5 col-12';
                    noResultsMessage.innerHTML = `
                <div class="text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>No subjects found</h5>
                    <p>No subjects match your search for "<strong>${searchTerm}</strong>"</p>
                    <button class="btn btn-outline-primary mt-2" onclick="clearSubjectSearch()">
                        <i class="fas fa-times me-1"></i>Clear Search
                    </button>
                </div>
            `;

                    const cardsRow = document.querySelector('.row.row-cols-1.row-cols-md-3.g-4');
                    if (cardsRow) {
                        cardsRow.appendChild(noResultsMessage);
                    }
                }
            }

            // Clear search function
            window.clearSubjectSearch = function() {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    searchInput.focus();
                }
            };
        });
    </script>
</body>

</html>