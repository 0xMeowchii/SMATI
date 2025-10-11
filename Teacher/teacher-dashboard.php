<?php include '../database.php'; ?>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #333;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark-color), #16213e);
            position: fixed;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            color: white;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link.active {
            color: white;
            background: var(--primary-color);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .navbar-toggler {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h1>
        </div>

        <?php

        //APPROVE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnApprove'])) {
            $conn = connectToDB();
            $concern_id = $_POST['approveId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE concern SET concern_status = 'Approved' WHERE concern_id=?");
                $stmt->bind_param("i", $concern_id);

                if ($stmt->execute()) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Concern Approved Successfully!',
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

        //CLOSE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnClose'])) {
            $conn = connectToDB();
            $concern_id = $_POST['closeId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE concern SET concern_status = 'Case Closed' WHERE concern_id=?");
                $stmt->bind_param("i", $concern_id);

                if ($stmt->execute()) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Case Closed Successfully!',
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
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center fw-bold bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Concerns</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reference #</th>
                                <th>Name</th>
                                <th>Section</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectToDB();
                            $sql = "SELECT *
                                    FROM concern c
                                    INNER JOIN students s ON c.student_id = s.student_id
                                    WHERE c.teacher_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                // Determine status badge class
                                $status_class = '';
                                switch ($row['concern_status']) {
                                    case 'Approved':
                                        $status_class = 'bg-success';
                                        break;
                                    case 'Case Closed':
                                        $status_class = 'bg-danger';
                                        break;
                                    case 'Pending':
                                    default:
                                        $status_class = 'bg-warning';
                                        break;
                                }

                                // Determine button states
                                $approve_disabled = ($row['concern_status'] == 'Approved' || $row['concern_status'] == 'Case Closed') ? 'disabled' : '';
                                $close_disabled = ($row['concern_status'] != 'Approved') ? 'disabled' : '';

                                $approve_class = $approve_disabled ? 'btn-outline-secondary' : 'btn-outline-success';
                                $close_class = $close_disabled ? 'btn-outline-secondary' : 'btn-outline-danger';

                                echo "<tr>";
                                echo "<td>" . $row['reference_num'] . "</td>";
                                echo "<td>" . $row['lastname'] . ", " . $row['firstname'] . "</td>";
                                echo "<td>" . $row['section'] . "</td>";
                                echo "<td>" . $row['type'] . "</td>";
                                echo "<td><span class='badge $status_class'>" . $row['concern_status'] . "</span></td>";
                                echo "<td>" . $row['concern_date'] . "</td>";
                                echo "<td>
                                    <div class='position-relative d-inline-flex'>
                                    <a class='btn btn-sm btn-outline-info view-concern-btn'
                                        data-num='" . $row["reference_num"] . "'
                                        data-name='" . $row["lastname"] . ", " . $row["firstname"] . "'
                                        data-section='" . $row["section"] . "'
                                        data-email='" . $row["email"] . "'
                                        data-type='" . $row["type"] . "'
                                        data-status='" . $row["concern_status"] . "'
                                        data-details='" . $row["details"] . "'
                                        data-date='" . $row["concern_date"] . "'
                                        data-bs-toggle='modal' 
                                        data-bs-target='#viewConcernModal'>
                                        <i class='fas fa-eye'></i>
                                    </a>
                                    <a class='btn btn-sm btn-outline-primary download-pdf-btn'
                                        data-num='" . $row["reference_num"] . "'
                                        data-name='" . $row["lastname"] . ", " . $row["firstname"] . "'
                                        data-section='" . $row["section"] . "'
                                        data-email='" . $row["email"] . "'
                                        data-type='" . $row["type"] . "'
                                        data-status='" . $row["concern_status"] . "'
                                        data-details='" . $row["details"] . "'
                                        data-date='" . $row["concern_date"] . "'>
                                        <i class='fas fa-download'></i>
                                    </a>
                                    <button class='btn btn-sm $approve_class approve-concern-btn' $approve_disabled
                                    data-id='" . $row["concern_id"] . "'
                                    data-bs-toggle='modal' 
                                    data-bs-target='#approveConcernModal'>
                                        <i class='fas fa-check'></i>
                                    </button>
                                    <button class='btn btn-sm $close_class close-concern-btn' $close_disabled
                                    data-id='" . $row["concern_id"] . "'
                                    data-bs-toggle='modal' 
                                    data-bs-target='#closeConcernModal'>
                                        <i class='fas fa-times'></i>
                                    </button>
                                    </div>
                                </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- View Concern Modal -->
    <div class="modal modal-lg fade" id="viewConcernModal" tabindex="-1" aria-labelledby="viewConcernModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="viewConcernModal">View Concern Details</h4>
                </div>
                <div class="modal-body row">
                    <?php
                    echo "<div class='col-md-6'>";
                    echo "<p><strong>Reference Number: </strong><span id='modalReferenceNumber'></span></p>
                        <p><strong>Name: </strong><span id='modalName'></span></p>
                        <p><strong>Email: </strong><span id='modalEmail'></span></p>
                        <p><strong>Concern Type: </strong><span id='modalConcernType'></span></p>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<p><strong>Date Submitted: </strong><span id='modalDate'></span></p>
                        <p><strong>Section: </strong><span id='modalSection'></span></p>
                        <p><strong>Status: </strong><span id='modalStatus'></span></p>";
                    echo "</div>";
                    echo "<div class='col-12'>";
                    echo "<p><strong>Concern Details:</strong>";
                    echo "<div class='border p-3 mt-2 rounded'><span id='modalDetails'></span></div></p>";
                    echo "</div>";
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Concern Modal -->
    <div class="modal fade" id="approveConcernModal" tabindex="-1" role="dialog" aria-labelledby="approveConcernModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveConcernModal">Approve Concern</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Do you want to Approve this Concern?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                        <input type="hidden" name="approveId" id="approveId">
                        <button type="submit" class="btn btn-success" name="btnApprove">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Concern Modal -->
    <div class="modal fade" id="closeConcernModal" tabindex="-1" role="dialog" aria-labelledby="closeConcernModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closeConcernModal">Close Concern</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Do you want to Close this Concern?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                        <input type="hidden" name="closeId" id="closeId">
                        <button type="submit" class="btn btn-danger" name="btnClose">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


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

        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        document.querySelectorAll('.view-concern-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const status = btn.getAttribute('data-status');

                // Set the content
                document.getElementById('modalReferenceNumber').textContent = btn.getAttribute('data-num');
                document.getElementById('modalName').textContent = btn.getAttribute('data-name');
                document.getElementById('modalEmail').textContent = btn.getAttribute('data-email');
                document.getElementById('modalConcernType').textContent = btn.getAttribute('data-type');
                document.getElementById('modalDate').textContent = btn.getAttribute('data-date');
                document.getElementById('modalSection').textContent = btn.getAttribute('data-section');
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

        document.querySelectorAll('.approve-concern-btn:not(:disabled)').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('approveId').value = btn.getAttribute('data-id');
            });
        });

        document.querySelectorAll('.close-concern-btn:not(:disabled)').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('closeId').value = btn.getAttribute('data-id');
            });
        });

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
    </script>
</body>

</html>