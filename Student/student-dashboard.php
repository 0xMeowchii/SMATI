<?php

include('../database.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
        .overflow-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .overflow-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php

    include('includes/sidebar.php');

    //Fetch Announcements
    $conn = connectToDB();
    $sql = "SELECT * FROM announcements ORDER BY announcement_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $announcements = [];
    while ($row = $result->fetch_assoc()) {
        $announcements[] = [
            'title' => $row['title'],
            'details' => $row['details'],
            'priority' => $row['type'],
            'date' => new DateTime($row['createdAt'])
        ];
    }

    //Fetch Subjects
    $conn = connectToDB();
    $sql = "SELECT * 
        FROM grades g
        INNER JOIN teachers t ON g.teacher_id = t.teacher_id
        INNER JOIN subjects s ON g.subject_id = s.subject_id
        INNER JOIN schoolyear sy ON g.schoolyear_id = sy.schoolyear_id
        WHERE g.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = [
            'subjectname' => $row['subject'],
            'teachername' => $row['lastname'] . ", " . $row['firstname'],
            'schoolyear' => $row['schoolyear'] . ", " . $row['semester'] . ' Semester'

        ];
    }

    //Fetch Subjects
    $conn = connectToDB();
    $sql = "SELECT *
            FROM concern c
            INNER JOIN students s ON c.student_id = s.student_id
            WHERE c.student_id = ? ORDER BY c.concern_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $concerns = [];
    while ($row = $result->fetch_assoc()) {
        $concerns[] = [
            'concern_id' => $row['concern_id'],
            'reference_num' => $row['reference_num'],
            'fullname' => $row['lastname'] . ", " . $row['firstname'],
            'date' => $row['concern_date'],
            'section' => $row['section'],
            'type' => $row['type'],
            'details' => $row['details'],
            'status' => $row['concern_status'],
            'email' => $row['email']
        ];
    }

    ?>

    <main class="main-content">
        <!--Welcome -->
        <div class="card border-0 text-white mb-4" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-12">
                        <h1 class="card-title h2 mb-2 fw-bold">Welcome back, <?php echo $_SESSION['firstname'] ?>!</h1>
                        <p class="card-text mb-1 opacity-75 fw-semibold">Education is the most powerful weapon which you can use to change the world.” — Nelson Mandela</p>
                        <p class="card-text mb-0 opacity-50 fst-italic">Good morning! Believe in your goals today — every class, every effort counts toward your dream.</p>
                    </div>
                </div>
            </div>
        </div>

        <!--Announcement & Subjects -->
        <div class="row g-4">
            <!--Annnouncements -->
            <div class="col-12 col-md-8">
                <div class="card border rounded-3 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">Announcements</span>
                            <span class="badge bg-warning text-dark" data-badge-type="announcements-count"></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control border-end-0" id="searchAnnouncements" placeholder="Search announcements...">
                                    <span class="input-group-text bg-white border-start-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-wrap gap-2" id="announcementsFilterButtons">
                                    <button class="btn btn-sm btn-primary announcements-filter-btn active" data-announcement-filter="all">All</button>
                                    <button class="btn btn-sm btn-outline-primary announcements-filter-btn" data-announcement-filter="low">Low</button>
                                    <button class="btn btn-sm btn-outline-primary announcements-filter-btn" data-announcement-filter="high">High</button>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-auto" id="announcementsListContainer" style="max-height:450px;">
                            <?php foreach ($announcements as $announcement): ?>
                                <?php
                                // Determine badge class and icon based on status
                                $badge_class = '';

                                switch ($announcement['priority']) {
                                    case 'Low':
                                        $badge_class = 'bg-warning text-black';
                                        break;
                                    case 'High':
                                        $badge_class = 'bg-danger text-white';
                                        break;
                                }
                                ?>
                                <div class="card mb-3 rounded-4 card-announcements"
                                    data-announcement-title="<?php echo strtolower($announcement['title']); ?>"
                                    data-announcement-priority="<?php echo strtolower($announcement['priority']); ?>">
                                    <div class="card-body border-start border-5 rounded-4 border-warning">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0 fw-bold announcement-title"><?php echo $announcement['title'] ?></h5>
                                            <div class="d-flex gap-1">
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo $announcement['priority'] ?></span>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted mb-3"><?php echo $announcement['details'] ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>Admin •
                                                <i class="fas fa-clock me-1 ms-2"></i><?php echo $announcement['date']->format('m-d-Y') ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Subjects -->
            <div class="col-12 col-md-4">
                <div class="card border rounded-3 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">My Subjects</span>
                            <span class="badge bg-warning text-dark" data-badge-type="subjects-count"></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="col mb-4">
                            <select class="form-control rounded-4 border-2" id="filterDate">
                                <?php
                                $conn = connectToDB();
                                $sql = "SELECT * FROM schoolyear WHERE status = '1' ORDER BY schoolyear_id DESC";
                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    // output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        $schoolyear = $row['schoolyear'] . ", " . $row['semester'] . ' Semester';
                                        echo "<option value='" . $schoolyear . "'>Sort by: " . $schoolyear . "</option>";
                                    }
                                } else {
                                    echo "0 results";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="flex-grow-1 overflow-auto subject-container" style="max-height: 380px;">
                            <?php foreach ($subjects as $subject): ?>
                                <div class="card border rounded-3 shadow-sm mb-3 subject-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0 fw-bold"><?php echo $subject['subjectname'] ?></h5>
                                        </div>
                                        <p class="card-text text-muted mb-3">
                                            <i class="fas fa-user-tie me-1"></i><?php echo $subject['teachername'] ?>
                                        </p>
                                        <p class="d-none schoolyear"><?php echo $subject['schoolyear'] ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4 pt-3 border-top">
                            <a class="btn btn-outline-primary btn-sm" href="student-grades.php">
                                <i class="fas fa-eye me-1"></i> View Grades
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Concern List -->
        <div class="card border rounded-3 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5">My Concern Tickets</span>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="input-group">
                            <input type="text" class="form-control border-end-0" id="concernSearchInput" placeholder="Search reference number...">
                            <span class="input-group-text bg-white border-start-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap gap-2" id="concernFilterButtons">
                            <button class="btn btn-sm btn-primary concern-filter-btn active" data-concern-filter="all">All</button>
                            <button class="btn btn-sm btn-outline-primary concern-filter-btn" data-concern-filter="pending">Pending</button>
                            <button class="btn btn-sm btn-outline-primary concern-filter-btn" data-concern-filter="approved">Approved</button>
                            <button class="btn btn-sm btn-outline-primary concern-filter-btn" data-concern-filter="case closed">Closed</button>
                        </div>
                    </div>
                </div>

                <div class="flex-grow-1 overflow-auto" id="concernListContainer" style="max-height:600px;">
                    <?php foreach ($concerns as $concern): ?>
                        <?php
                        // Determine badge class and icon based on status
                        $badge_class = '';
                        $status_icon = '';

                        switch ($concern['status']) {
                            case 'Pending':
                                $badge_class = 'bg-warning text-dark';
                                $status_icon = 'fa-clock';
                                break;
                            case 'Approved':
                                $badge_class = 'bg-success text-white';
                                $status_icon = 'fa-check-circle';
                                break;
                            case 'Case Closed':
                                $badge_class = 'bg-danger text-white';
                                $status_icon = 'fa-archive';
                                break;
                        }
                        ?>

                        <div class="card concern-ticket-card rounded-4 mb-4"
                            data-concern-status="<?php echo strtolower($concern['status']); ?>"
                            data-concern-reference="<?php echo strtolower($concern['reference_num']); ?>">
                            <div class="card-body rounded-start-4 border-start border-5 position-relative border-primary">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="pt-3 mb-2">
                                        <div class="h5 text-primary fw-bold concern-reference-num"><?php echo $concern['reference_num'] ?></div>
                                        <div class="text-muted small">Submitted on <?php echo $concern['date'] ?> </div>
                                        <div class="badge bg-light text-primary px-3 py-2 mt-3">
                                            <i class="fas fa-tag me-1"></i><?php echo $concern['type']; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge <?php echo $badge_class; ?> px-3 py-2">
                                            <i class="fas <?php echo $status_icon; ?> me-1"></i>
                                            <?php echo $concern['status']; ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <div class='position-relative d-inline-flex gap-1'>
                                        <!-- PDF Download Button - Always visible -->
                                        <a class='btn btn-sm btn-outline-primary download-pdf-btn'
                                            data-num='<?php echo $concern['reference_num']; ?>'
                                            data-name='<?php echo $concern['fullname']; ?>'
                                            data-section='<?php echo $concern['section']; ?>'
                                            data-email='<?php echo $concern['email']; ?>'
                                            data-type='<?php echo $concern['type']; ?>'
                                            data-status='<?php echo $concern['status']; ?>'
                                            data-details='<?php echo $concern['details']; ?>'
                                            data-date='<?php echo $concern['date']; ?>'>
                                            <i class='fas fa-download'></i>
                                        </a>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary view-concern-btn"
                                            data-num='<?php echo $concern['reference_num']; ?>'
                                            data-type='<?php echo $concern['type']; ?>'
                                            data-status='<?php echo $concern['status']; ?>'
                                            data-details='<?php echo $concern['details']; ?>'
                                            data-date='<?php echo $concern['date']; ?>'
                                            data-bs-toggle='modal'
                                            data-bs-target='#viewConcernModal'>
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- View Concern Modal -->
        <div class="modal fade" id="viewConcernModal" tabindex="-1" aria-labelledby="viewConcernModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h4 class="modal-title" id="viewConcernModal">
                            <i class="fas fa-ticket-alt me-2 text-white"></i>Concern Details
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="p-4 rounded-3 bg-light mb-4 col">
                                        <div class="d-flex mb-1 align-items-center" style="font-weight: 600; font-size: 0.9rem;">
                                            <i class="fas fa-hashtag me-2 fs-6 text-primary" style="font-size: 0.8rem;"></i>Reference Number
                                        </div>
                                        <p class="fs-6 fw-bold text-primary mb-0"><span id='modalReferenceNumber'></span></p>
                                    </div>
                                    <div class="p-4 rounded-3 bg-light mb-4">
                                        <div class="d-flex mb-1 align-items-center" style="font-weight: 600; font-size: 0.9rem;">
                                            <i class="fas fa-info-circle me-2 fs-6 text-primary" style="font-size: 0.8rem;"></i>Status
                                        </div>
                                        <p class="fs-6 mb-0"><span id='modalStatus'></span></p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="p-4 rounded-3 bg-light mb-4">
                                        <div class="d-flex mb-1 align-items-center" style="font-weight: 600; font-size: 0.9rem;">
                                            <i class="fas fa-calendar-alt me-2 fs-6 text-primary" style="font-size: 0.8rem;"></i>Date Submitted
                                        </div>
                                        <p class="fs-6 mb-0"><span id='modalDate'></span></p>
                                    </div>
                                    <div class="p-4 rounded-3 bg-light mb-4">
                                        <div class="d-flex mb-1 align-items-center" style="font-weight: 600; font-size: 0.9rem;">
                                            <i class="fas fa-tag me-2 fs-6 text-primary" style="font-size: 0.8rem;"></i>Concern Type
                                        </div>
                                        <p class="fs-6 mb-0"><span id='modalConcernType'></span></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-1">
                                <div class="d-flex align-items-center pb-2 border-bottom border-1 fw-medium">
                                    <i class="fas fa-align-left me-2 text-primary"></i>Concern Details
                                </div>
                                <p class="fs-6 mb-0 mt-3"><span id='modalDetails'></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Initialize jsPDF
        const {
            jsPDF
        } = window.jspdf;

        // PDF Download functionality
        document.querySelectorAll('.download-pdf-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Get concern data from data attributes
                const concernData = {
                    reference_num: btn.getAttribute('data-num'),
                    name: btn.getAttribute('data-name'),
                    section: btn.getAttribute('data-section'),
                    email: btn.getAttribute('data-email'),
                    type: btn.getAttribute('data-type'),
                    status: btn.getAttribute('data-status'),
                    details: btn.getAttribute('data-details'),
                    date: btn.getAttribute('data-date')
                };

                // Show confirmation dialog
                Swal.fire({
                    title: 'Download Concern Report',
                    html: `Do you want to download the PDF report for <strong>${concernData.reference_num}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4361ee',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, download',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        generateConcernPDF(concernData);
                    }
                });
            });
        });

        function generateConcernPDF(data) {
            // Create a new jsPDF instance
            const doc = new jsPDF();

            // Set document properties
            doc.setProperties({
                title: `SMATI Concern - ${data.reference_num}`,
                subject: 'Student Concern Report',
                author: 'SMATI Concern Portal',
                keywords: 'concern, student, report',
                creator: 'SMATI Concern Portal'
            });

            // Add header with background
            doc.setFillColor(67, 97, 238);
            doc.rect(0, 0, 210, 30, 'F');

            // Add title
            doc.setFontSize(20);
            doc.setTextColor(255, 255, 255);
            doc.text("SMATI CONCERN REPORT", 105, 18, {
                align: 'center'
            });

            // Add reference number prominently
            doc.setFontSize(16);
            doc.setTextColor(67, 97, 238);
            doc.text(`Reference: ${data.reference_num}`, 20, 50);

            // Add submission date
            doc.setFontSize(12);
            doc.setTextColor(100, 100, 100);
            doc.text(`Submitted on: ${data.date}`, 20, 60);

            // Add status with color coding
            doc.setFontSize(12);
            doc.setTextColor(0, 0, 0);
            doc.text(`Status:`, 20, 70);
            doc.setFont(undefined, 'bold');

            // Color code based on status
            if (data.status === 'Approved') {
                doc.setTextColor(0, 128, 0); // Green
                doc.text(data.status, 45, 70);
            } else if (data.status === 'Case Closed') {
                doc.setTextColor(0, 0, 255); // Blue
                doc.text(data.status, 45, 70);
            } else {
                doc.setTextColor(255, 165, 0); // Orange for pending
                doc.text(data.status, 45, 70);
            }

            // Add divider line
            doc.setDrawColor(200, 200, 200);
            doc.line(20, 80, 190, 80);

            // Student Information Section
            doc.setFontSize(14);
            doc.setTextColor(67, 97, 238);
            doc.text("STUDENT INFORMATION", 20, 95);

            doc.setFontSize(11);
            doc.setTextColor(0, 0, 0);
            doc.setFont(undefined, 'normal');

            // Student details
            doc.text(`Name: ${data.name}`, 20, 105);
            doc.text(`Section: ${data.section}`, 20, 115);
            doc.text(`Email: ${data.email}`, 20, 125);

            // Concern Details Section
            doc.setFontSize(14);
            doc.setTextColor(67, 97, 238);
            doc.text("CONCERN DETAILS", 20, 145);

            doc.setFontSize(11);
            doc.setTextColor(0, 0, 0);

            doc.text(`Type: ${data.type}`, 20, 155);

            // Concern details with text wrapping
            doc.text("Description:", 20, 170);
            const splitDetails = doc.splitTextToSize(data.details, 170);
            doc.text(splitDetails, 20, 180);

            // Add footer
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.text("Generated by SMATI Concern Portal", 105, 280, {
                align: 'center'
            });
            doc.text(new Date().toLocaleDateString(), 105, 285, {
                align: 'center'
            });

            // Add page border
            doc.setDrawColor(200, 200, 200);
            doc.rect(10, 10, 190, 277);

            // Save the PDF
            doc.save(`SMATI-Concern-${data.reference_num}.pdf`);
        }

        document.querySelectorAll('.view-concern-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const status = btn.getAttribute('data-status');

                // Set the content
                document.getElementById('modalReferenceNumber').textContent = btn.getAttribute('data-num');
                document.getElementById('modalConcernType').textContent = btn.getAttribute('data-type');
                document.getElementById('modalDate').textContent = btn.getAttribute('data-date');
                document.getElementById('modalDetails').textContent = btn.getAttribute('data-details');


                // Set status with background color
                const statusElement = document.getElementById('modalStatus');
                statusElement.textContent = status;

                // Remove existing classes
                statusElement.className = 'badge';

                // Add appropriate Bootstrap class based on status
                switch (status) {
                    case 'Approved':
                        statusElement.classList.add('bg-success');
                        break;
                    case 'Case Closed':
                        statusElement.classList.add('bg-danger');
                        break;
                    case 'Pending':
                    default:
                        statusElement.classList.add('bg-warning', 'text-dark');
                        break;
                }
            });
        });

        // Function to filter subjects by school year
        function filterSubjectsBySchoolYear() {
            const filterSelect = document.getElementById('filterDate');
            const selectedSchoolYear = filterSelect.value;
            const subjectCards = document.querySelectorAll('.subject-card');
            const subjectBadge = document.querySelector('[data-badge-type="subjects-count"]');

            let visibleCount = 0;

            subjectCards.forEach(card => {
                // Get the school year from the hidden paragraph
                const schoolYearElement = card.querySelector('.schoolyear');
                const cardSchoolYear = schoolYearElement ? schoolYearElement.textContent.trim() : '';

                // Check if the subject matches the selected school year
                const matches = selectedSchoolYear === '' || cardSchoolYear === selectedSchoolYear;

                if (matches) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update the subject count badge
            if (subjectBadge) {
                subjectBadge.textContent = `${visibleCount} Subject/s`;
            }

            // Show no results message if needed
            showNoSubjectsMessage(visibleCount, selectedSchoolYear);
        }

        // Function to show no results message
        function showNoSubjectsMessage(visibleCount, selectedSchoolYear) {
            const subjectContainer = document.querySelector('.subject-container');
            let noResultsMessage = subjectContainer.querySelector('.no-subjects-message');

            if (visibleCount === 0 && selectedSchoolYear !== '') {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.className = 'no-subjects-message text-center py-4';
                    noResultsMessage.innerHTML = `
                <i class="fas fa-book mb-2" style="font-size: 2em; opacity: 0.5; color: #6c757d;"></i>
                <br>
                <span style="color: #6c757d;">No subjects found for "${selectedSchoolYear}"</span>
            `;
                    subjectContainer.appendChild(noResultsMessage);
                }
                noResultsMessage.style.display = 'block';
            } else if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }

        // Function to clear subject filter
        function clearSubjectFilter() {
            const filterSelect = document.getElementById('filterDate');
            filterSelect.value = '';
            filterSubjectsBySchoolYear();
        }

        // Event listeners for subject filtering
        document.addEventListener('DOMContentLoaded', function() {
            const filterDate = document.getElementById('filterDate');

            if (filterDate) {
                filterDate.addEventListener('change', filterSubjectsBySchoolYear);
            }

            // Initialize filter on page load
            filterSubjectsBySchoolYear();
        });

        // Concern Filtering and Search System
        (function() {
            'use strict';

            // Cache DOM elements
            const searchInput = document.getElementById('concernSearchInput');
            const filterButtons = document.querySelectorAll('.concern-filter-btn');
            const concernCards = document.querySelectorAll('.concern-ticket-card');
            const concernContainer = document.getElementById('concernListContainer');

            // Current filter state
            let currentFilter = 'all';
            let currentSearch = '';

            // Initialize the system
            function init() {
                if (!searchInput || !filterButtons.length || !concernCards.length) {
                    console.warn('Concern filtering elements not found');
                    return;
                }

                // Add event listeners
                searchInput.addEventListener('input', handleSearch);
                filterButtons.forEach(btn => {
                    btn.addEventListener('click', handleFilterClick);
                });

                // Initial display
                applyFilters();
            }

            // Handle search input
            function handleSearch(e) {
                currentSearch = e.target.value.toLowerCase().trim();
                applyFilters();
            }

            // Handle filter button click
            function handleFilterClick(e) {
                const btn = e.currentTarget;
                const filter = btn.getAttribute('data-concern-filter');

                // Update active state
                filterButtons.forEach(b => {
                    b.classList.remove('active', 'btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('active', 'btn-primary');

                // Update current filter
                currentFilter = filter;
                applyFilters();
            }

            // Apply both search and filter
            function applyFilters() {
                let visibleCount = 0;

                concernCards.forEach(card => {
                    const status = card.getAttribute('data-concern-status');
                    const reference = card.getAttribute('data-concern-reference');

                    // Check filter match
                    const filterMatch = currentFilter === 'all' || status === currentFilter;

                    // Check search match
                    const searchMatch = currentSearch === '' || reference.includes(currentSearch);

                    // Show or hide card
                    if (filterMatch && searchMatch) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show "no results" message if needed
                showNoResultsMessage(visibleCount);
            }

            // Show or hide "no results" message
            function showNoResultsMessage(count) {
                // Remove existing message
                const existingMsg = document.getElementById('concernNoResultsMsg');
                if (existingMsg) {
                    existingMsg.remove();
                }

                // Add message if no results
                if (count === 0) {
                    const noResultsDiv = document.createElement('div');
                    noResultsDiv.id = 'concernNoResultsMsg';
                    noResultsDiv.className = 'text-center py-5';
                    noResultsDiv.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No concerns found</h5>
                <p class="text-muted">Try adjusting your search or filter criteria</p>
            `;
                    concernContainer.appendChild(noResultsDiv);
                }
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();

        (function() {
            'use strict';

            // Cache DOM elements
            const searchInput = document.getElementById('searchAnnouncements');
            const filterButtons = document.querySelectorAll('.announcements-filter-btn');
            const announcementsCard = document.querySelectorAll('.card-announcements');
            const announcementsContainer = document.getElementById('announcementsListContainer');
            const announcementsBadge = document.querySelector('[data-badge-type="announcements-count"]');

            // Current filter state
            let currentFilter = 'all';
            let currentSearch = '';

            // Initialize the system
            function init() {
                if (!searchInput || !filterButtons.length || !announcementsCard.length) {
                    console.warn('Announcements filtering elements not found');
                    return;
                }

                // Add event listeners
                searchInput.addEventListener('input', handleSearch);
                filterButtons.forEach(btn => {
                    btn.addEventListener('click', handleFilterClick);
                });

                // Initial display
                applyFilters();
            }

            // Handle search input
            function handleSearch(e) {
                currentSearch = e.target.value.toLowerCase().trim();
                applyFilters();
            }

            // Handle filter button click
            function handleFilterClick(e) {
                const btn = e.currentTarget;
                const filter = btn.getAttribute('data-announcement-filter');

                // Update active state
                filterButtons.forEach(b => {
                    b.classList.remove('active', 'btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('active', 'btn-primary');

                // Update current filter
                currentFilter = filter;
                applyFilters();
            }

            // Apply both search and filter
            function applyFilters() {
                let visibleCount = 0;

                announcementsCard.forEach(card => {
                    const priority = card.getAttribute('data-announcement-priority');
                    const title = card.getAttribute('data-announcement-title');

                    // Check filter match
                    const filterMatch = currentFilter === 'all' || priority === currentFilter;

                    // Check search match
                    const searchMatch = currentSearch === '' || title.includes(currentSearch);

                    // Show or hide card
                    if (filterMatch && searchMatch) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }

                });

                if (announcementsBadge) {
                    announcementsBadge.textContent = `${visibleCount} Announcement/s`;
                }

                // Show "no results" message if needed
                showNoResultsMessage(visibleCount);
            }

            // Show or hide "no results" message
            function showNoResultsMessage(count) {
                // Remove existing message
                const existingMsg = document.getElementById('announcementNoResultsMsg');
                if (existingMsg) {
                    existingMsg.remove();
                }

                // Add message if no results
                if (count === 0) {
                    const noResultsDiv = document.createElement('div');
                    noResultsDiv.id = 'announcementNoResultsMsg';
                    noResultsDiv.className = 'text-center py-5';
                    noResultsDiv.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No announcements found</h5>
                <p class="text-muted">Try adjusting your search or filter criteria</p>
            `;
                    announcementsContainer.appendChild(noResultsDiv);
                }
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</body>

</html>