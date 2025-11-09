<?php
include '../database.php';
include '../includes/activity_logger.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
        .priority-high {
            border-left: 4px solid #dc3545;
        }

        .priority-medium {
            border-left: 4px solid #ffc107;
        }

        .priority-low {
            border-left: 4px solid #198754;
        }

        .announcement-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-weight: 600;
            color: #2c3e50;
        }

        .priority-badge {
            font-size: 0.75rem;
        }

        .details-text {
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: #6c757d;
        }

        .action-buttons1 .btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fa fa-calendar me-2"></i>Announcement Management</h4>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-announcement-btn" data-bs-toggle="modal" data-bs-target="#add-announcement-modal">
                    <i class="fas fa-plus me-1"></i>Add Announcement
                </button>
            </div>
        </div>

        <?php

        //INSERT QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
            $conn = connectToDB();
            $title = $_POST['title'];
            $details = $_POST['details'];
            $type = $_POST['type'];
            $target = $_POST['target'];

            if ($conn) {
                $stmt = $conn->prepare("INSERT INTO announcements (title, details, type, target, createdAt) 
                                            VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssss", $title, $details, $type, $target);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'CREATE_ANNOUNCEMENT', "created new announcement. Check the Recent Announcement Board.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Announcement Created Successfully!',
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

        //UPDATE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEdit'])) {
            $conn = connectToDB();
            $announcement_id = $_POST['editId'];
            $title = $_POST['editTitle'];
            $details = $_POST['editDetails'];
            $type = $_POST['editType'];
            $target = $_POST['editTarget'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE announcements 
                                        SET title=?,
                                            details=?,
                                            type=?,
                                            target=?
                                        WHERE announcement_id=?");
                $stmt->bind_param("ssssi", $title, $details, $type, $target, $announcement_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'UPDATE_ANNOUNCEMENT', "updated announcement details. Check the Recent Announcement Board.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Announcement Updated Successfully!',
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

        //DELETE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
            $conn = connectToDB();
            $announcement_id = $_POST['id'];

            if ($conn) {
                $stmt = $conn->prepare("DELETE FROM announcements WHERE announcement_id=?");
                $stmt->bind_param("i", $announcement_id);

                if ($stmt->execute()) {

                    logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'DELETE_ANNOUNCEMENT', "deleted an announcement.");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Announcement Deleted Successfully!',
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
        <!-- Student Table -->
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Announcement</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Announcement...">
                            <span class="input-group-text bg-primary"><i class="fas fa-search text-white"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container my-4">
                <div class="row" id="announcements-container">
                    <?php
                    $conn = connectToDB();
                    $sql = "SELECT * FROM announcements ORDER BY announcement_id DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        // output data of each row
                        while ($row = $result->fetch_assoc()) {
                            $date = new DateTime($row['createdAt']);
                            // Determine priority class
                            $priorityClass = '';
                            if ($row["type"] == 'High') {
                                $priorityClass = 'priority-high';
                            } else {
                                $priorityClass = 'priority-low';
                            }

                            // Determine badge color
                            $badgeClass = '';
                            if ($row["type"] == 'High') {
                                $badgeClass = 'bg-danger';
                            } else {
                                $badgeClass = 'bg-success';
                            }

                            $iconClass = '';
                            if ($row["type"] == 'High') {
                                $iconClass = 'bi bi-arrow-up';
                            } else {
                                $iconClass = 'bi bi-arrow-down';
                            }

                            echo '<div class="col-lg-4 col-md-6 mb-4">';
                            echo '<div class="card announcement-card h-100 ' . $priorityClass . '">';
                            echo '<div class="card-body d-flex flex-column">';
                            echo '<div class="d-flex justify-content-between align-items-start mb-2">';
                            echo '<h5 class="card-title">' . $row["title"] . '</h5>';
                            echo '<span class="badge ' . $badgeClass . ' priority-badge"><i class="' . $iconClass . '"></i>' . $row["type"] . '</span>';
                            echo '</div>';
                            echo '<p class="card-text details-text flex-grow-1">' . $row["details"] . '</p>';
                            echo '<p class="card-text details-text"><i class="bi bi-person-fill me-2"></i>' . $row['target'] . '</p>';
                            echo '<p class="card-text details-text"><i class="bi bi-calendar-event-fill me-2"></i>' . $date->format('m-d-Y h:i A') . '</p>';
                            echo '<div class="action-buttons1 mt-3">';
                            echo '<a class="btn btn-sm btn-outline-primary me-1 view-announcement-btn"
                            data-id="' . $row["announcement_id"] . '"
                            data-title="' . $row["title"] . '"
                            data-details="' . $row["details"] . '"
                            data-type="' . $row["type"] . '"
                            data-target="' . $row["target"] . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#view-announcement-modal">
                            <i class="fas fa-eye"></i>
                        </a>';
                            echo '<a class="btn btn-sm btn-outline-secondary me-1 edit-announcement-btn"
                            data-id="' . $row["announcement_id"] . '"
                            data-title="' . $row["title"] . '"
                            data-details="' . $row["details"] . '"
                            data-type="' . $row["type"] . '"
                            data-target="' . $row["target"] . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#edit-announcement-modal">
                            <i class="fas fa-edit"></i>
                        </a>';
                            echo '<a class="btn btn-sm btn-outline-danger me-1 drop-announcement-btn"
                            data-id="' . $row["announcement_id"] . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#drop-announcement-modal">
                            <i class="fas fa-trash"></i>
                        </a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12 text-center py-5">';
                        echo '<h4 class="text-muted">No announcements found</h4>';
                        echo '<p class="text-muted">Create your first announcement to get started.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Add Announcement Modal -->
        <div class="modal fade" id="add-announcement-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Add New Announcement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Announcement Details</h4>
                                <div class="col-12 col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" placeholder="Title" id="floatingTextarea1" name="title" required> </input>
                                        <label for="floatingTextarea1">Title</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <select class="form-control rounded-2" name="type" required>
                                        <option value="Low">Priority: Low</option>
                                        <option value="High">Priority: High</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <select class="form-control rounded-2" name="target" required>
                                        <option value="All">Target: All</option>
                                        <option value="Student">Target: Student</option>
                                        <option value="Teacher">Target: Teacher</option>
                                        <option value="Registrar">Target: Registrar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating mt-3">
                                <textarea class="form-control" placeholder="Details" id="floatingTextarea2" style="height: 120px" name="details" required></textarea>
                                <label for="floatingTextarea2">Details</label>
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

        <!-- View Announcement Modal -->
        <div class="modal fade" id="view-announcement-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">View Announcement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <h4 class="pb-2 border-bottom">Announcement Details</h4>
                            <?php
                            echo "<p><strong>Title: </strong><span id='viewAnnouncementTitle'></span></p>
                                    <p><strong>Details: </strong><span id='viewAnnouncementDetails'></span></p>
                                    <p><strong>Target: </strong><span id='viewAnnouncementTarget'></span></p>
                                    <p><strong>Priority: </strong><span id='viewAnnouncementPriority'></span></p>";
                            ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Announcement Modal -->
        <div class="modal fade" id="edit-announcement-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="studentModalTitle">Edit Announcement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                            <input type="hidden" name="editId" id="editId">
                            <div class="row g-3">
                                <h4 class="pb-2 border-bottom">Announcement Details</h4>
                                <div class="col-12 col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" placeholder="Title" id="editTitle" name="editTitle"></input>
                                        <label for="editTitle">Title</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <select class="form-control rounded-2" name="editType" id="editType" required>
                                        <option value="Low">Priority: Low</option>
                                        <option value="High">Priority: High</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <select class="form-control rounded-2" name="editTarget" id="editTarget" required>
                                        <option value="All">Target: All</option>
                                        <option value="Student">Target: Student</option>
                                        <option value="Teacher">Target: Teacher</option>
                                        <option value="Registrar">Target: Registrar</option>
                                    </select>
                                </div>

                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Details" id="editDetails" style="height: 120px" name="editDetails"></textarea>
                                <label for="editDetails">Details</label>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Announcement Modal -->
        <div class="modal fade" id="drop-announcement-modal" tabindex="-1" role="dialog" aria-labelledby="drop-announcement-modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="drop-announcement-modal">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this Announcement?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                            <input type="hidden" name="id" id="id">
                            <button type="submit" class="btn btn-danger" name="btnDelete">Yes</button>
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
        document.querySelectorAll('.view-announcement-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('viewAnnouncementTitle').textContent = btn.getAttribute('data-title');
                document.getElementById('viewAnnouncementDetails').textContent = btn.getAttribute('data-details');
                document.getElementById('viewAnnouncementPriority').textContent = btn.getAttribute('data-type');
            });
        });
        document.querySelectorAll('.edit-announcement-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = btn.getAttribute('data-id');
                document.getElementById('editTitle').value = btn.getAttribute('data-title');
                document.getElementById('editDetails').value = btn.getAttribute('data-details');
                document.getElementById('editType').value = btn.getAttribute('data-type');
            });
        });

        document.querySelectorAll('.drop-announcement-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('id').value = btn.getAttribute('data-id');
            });
        });

        // Simplified version for date search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const announcementsContainer = document.getElementById('announcements-container');
            const announcementCards = Array.from(announcementsContainer.querySelectorAll('.col-lg-4'));

            function performSearch(searchTerm) {
                const query = searchTerm.toLowerCase().trim();
                let visibleCards = 0;

                announcementCards.forEach(function(card) {
                    // Skip the "no announcements" message card
                    if (card.classList.contains('col-12') && card.querySelector('h4.text-muted')) {
                        card.style.display = 'none';
                        return;
                    }

                    // Get all text content from the entire card
                    const cardText = card.textContent.toLowerCase();

                    // Check if search term matches any part of the card content
                    if (query === '' || cardText.includes(query)) {
                        card.style.display = '';
                        visibleCards++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide "No results" message
                showNoResultsMessage(visibleCards === 0 && query !== '');
            }

            function showNoResultsMessage(show) {
                let noResultsCard = document.getElementById('no-results-card');

                if (show && !noResultsCard) {
                    noResultsCard = document.createElement('div');
                    noResultsCard.id = 'no-results-card';
                    noResultsCard.className = 'col-12 text-center py-5';
                    noResultsCard.innerHTML = `
                <h4 class="text-muted">No announcements found</h4>
                <p class="text-muted">No announcements match your search criteria</p>
                <div class="mt-3">
                    <i class="fas fa-search" style="font-size: 3em; opacity: 0.3;"></i>
                </div>
            `;
                    announcementsContainer.appendChild(noResultsCard);
                } else if (!show && noResultsCard) {
                    noResultsCard.remove();
                }

                const originalNoAnnouncements = announcementsContainer.querySelector('.col-12.text-center.py-5');
                if (originalNoAnnouncements && !originalNoAnnouncements.id) {
                    originalNoAnnouncements.style.display = show ? 'none' : '';
                }
            }

            // Event listeners
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    performSearch(e.target.value);
                });

                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') performSearch(this.value);
                });

                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        performSearch('');
                    }
                });
            }
        });
    </script>
</body>

</html>