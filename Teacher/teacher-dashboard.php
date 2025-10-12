<?php
include '../database.php';
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="../Teacher/teacher.css">
    <style>
        .dashboard-card {
            border-radius: 16px;
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card.total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .dashboard-card.pending {
            background: linear-gradient(135deg, #a49b17ff 0%, #d3f207ff 100%);
        }

        .dashboard-card.in-progress {
            background: linear-gradient(135deg, #0e8829ff 0%, #43fa01ff 100%);
        }

        .dashboard-card.resolved {
            background: linear-gradient(135deg, #b50b38ff 0%, #f74ec2ff 100%);
        }

        .icon-container {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        /* Custom styles to maintain your design */
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #f72585;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        /* Search input focus state */
        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15) !important;
        }

        /* Filter button hover and active states */
        .filter-btn {
            transition: var(--transition);
            background: #f1f5f9;
            border: none;
            font-size: 0.9rem;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary) !important;
            color: white !important;
            transform: translateY(-1px);
        }

        /* Remove default Bootstrap button focus shadow */
        .btn:focus {
            box-shadow: none !important;
        }

        .no-results-message {
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php
    include('sidebar.php');

    //TOTAL CONCERNS
    $conn = connectToDB();
    $sql = "SELECT * FROM concern WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $totalConcern = $result->num_rows;

    //PENDING
    $conn = connectToDB();
    $sql = "SELECT * FROM concern WHERE teacher_id = ? AND concern_status = 'Pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $pendingConcern = $result->num_rows;

    //APPROVED
    $conn = connectToDB();
    $sql = "SELECT * FROM concern WHERE teacher_id = ? AND concern_status = 'Approved'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $approvedConcern = $result->num_rows;

    //CASE CLOSED
    $conn = connectToDB();
    $sql = "SELECT * FROM concern WHERE teacher_id = ? AND concern_status = 'Case Closed'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $closeConcern = $result->num_rows;

    //Fetch concern list
    $conn = connectToDB();
    $sql = "SELECT *
            FROM concern c
            INNER JOIN students s ON c.student_id = s.student_id
            WHERE c.teacher_id = ? ORDER BY c.concern_id DESC";
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Concern Approved Successfully!',
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

        //CLOSE QUERY
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnClose'])) {
            $conn = connectToDB();
            $concern_id = $_POST['closeId'];

            if ($conn) {
                $stmt = $conn->prepare("UPDATE concern SET concern_status = 'Case Closed' WHERE concern_id=?");
                $stmt->bind_param("i", $concern_id);

                if ($stmt->execute()) {
                    echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Case Closed Successfully!',
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

        <!-- Dashboard Stats -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card total text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-ticket-alt fs-1"></i>
                        </div>
                        <div>
                            <div class="h2 fw-bold"><?php echo $totalConcern ?></div>
                            <div class="text-white-50">Total Concerns</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card dashboard-card pending text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-clock fs-2"></i>
                        </div>
                        <div>
                            <div class="h2 fw-bold"><?php echo $pendingConcern ?></div>
                            <div class="text-white-50">Pending</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card in-progress text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-spinner fs-2"></i>
                        </div>
                        <div>
                            <div class="h2 fw-bold"><?php echo $approvedConcern ?></div>
                            <div class="text-white-50">Resolved</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card resolved text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-check-circle fs-2"></i>
                        </div>
                        <div>
                            <div class="h2 fw-bold"><?php echo $closeConcern ?></div>
                            <div class="text-white-50">Case Closed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group input-group-md position-relative">
                    <span class="input-group-text bg-muted border-2">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text"
                        class="form-control rounded-end-3 border-2 border-start-0"
                        id="searchInput"
                        placeholder="Search concerns by reference number, type, or details..."
                        style="height: 3.5rem;">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select form-select-md rounded-3 border-2"
                    id="sortSelect"
                    style="border-color: #e2e8f0; height: 3.5rem;">
                    <option value="newest">Sort by: Newest First</option>
                    <option value="oldest">Sort by: Oldest First</option>
                    <option value="status">Sort by: Status</option>
                </select>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <h6 class="card-title fw-semibold mb-3">Filter by Status:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary rounded-pill px-3 py-2 d-flex align-items-center filter-btn active"
                        data-filter="all">
                        <i class="fas fa-layer-group me-2"></i> All
                    </button>
                    <button class="btn btn-outline-secondary rounded-pill px-3 py-2 d-flex align-items-center filter-btn"
                        data-filter="pending">
                        <i class="fas fa-clock me-2"></i> Pending
                    </button>
                    <button class="btn btn-outline-secondary rounded-pill px-3 py-2 d-flex align-items-center filter-btn"
                        data-filter="resolved">
                        <i class="fas fa-check-circle me-2"></i> Resolved
                    </button>
                    <button class="btn btn-outline-secondary rounded-pill px-3 py-2 d-flex align-items-center filter-btn"
                        data-filter="closed">
                        <i class="fas fa-archive me-2"></i> Closed
                    </button>
                </div>
            </div>
        </div>

        <?php foreach ($concerns as $concern): ?>
            <?php
            // Determine badge class and icon based on status
            $badge_class = '';
            $status_icon = '';
            $border_class = '';

            switch ($concern['status']) {
                case 'Pending':
                    $badge_class = 'bg-warning text-dark';
                    $status_icon = 'fa-clock';
                    $border_class = 'border-warning';
                    break;
                case 'Approved':
                    $badge_class = 'bg-success text-white';
                    $status_icon = 'fa-check-circle';
                    $border_class = 'border-success';
                    break;
                case 'Case Closed':
                    $badge_class = 'bg-danger text-white';
                    $status_icon = 'fa-archive';
                    $border_class = 'border-danger';
                    break;
                default:
                    $badge_class = 'bg-warning text-dark';
                    $status_icon = 'fa-clock';
                    $border_class = 'border-warning';
            }

            // Determine button states and classes
            $approve_disabled = ($concern['status'] == 'Approved' || $concern['status'] == 'Case Closed') ? 'disabled' : '';
            $close_disabled = ($concern['status'] != 'Approved') ? 'disabled' : '';

            $approve_class = $approve_disabled ? 'btn-outline-secondary' : 'btn-outline-success';
            $close_class = $close_disabled ? 'btn-outline-secondary' : 'btn-outline-danger';
            ?>

            <div class="card rounded-4 mb-4">
                <div class="card-body rounded-start-4 border-start border-5 position-relative <?php echo $border_class; ?>">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="pt-3 mb-2">
                            <div class="h5 text-primary fw-bold"><?php echo $concern['reference_num']; ?></div>
                            <div class="text-muted small">Submitted on <?php echo $concern['date']; ?></div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge <?php echo $badge_class; ?> px-3 py-2">
                                <i class="fas <?php echo $status_icon; ?> me-1"></i>
                                <?php echo $concern['status']; ?>
                            </span>
                        </div>
                    </div>

                    <div class="badge bg-light text-primary px-3 py-2 mb-4">
                        <i class="fas fa-tag me-1"></i><?php echo $concern['type']; ?>
                    </div>

                    <h5 class="card-title"><?php echo $concern['fullname']; ?></h5>

                    <div class="card-text text-muted mb-3">
                        <?php echo $concern['details']; ?>
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

                            <!-- Approve Button - Only show if not already approved/closed -->
                            <?php if ($concern['status'] == 'Pending'): ?>
                                <button class='btn btn-sm <?php echo $approve_class; ?> approve-concern-btn'
                                    data-id='<?php echo $concern['concern_id']; ?>'
                                    data-bs-toggle='modal'
                                    data-bs-target='#approveConcernModal'>
                                    <i class='fas fa-check'></i> Approve
                                </button>
                            <?php endif; ?>

                            <!-- Close Button - Only show if approved but not closed -->
                            <?php if ($concern['status'] == 'Approved'): ?>
                                <button class='btn btn-sm <?php echo $close_class; ?> close-concern-btn'
                                    data-id='<?php echo $concern['concern_id']; ?>'
                                    data-bs-toggle='modal'
                                    data-bs-target='#closeConcernModal'>
                                    <i class='fas fa-times'></i> Close
                                </button>
                            <?php endif; ?>

                            <!-- Show message for closed cases -->
                            <?php if ($concern['status'] == 'Case Closed'): ?>
                                <span class="badge bg-light text-muted px-2 py-2">
                                    <i class="fas fa-lock me-1"></i> Case Closed
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary view-concern-btn"
                                data-num='<?php echo $concern['reference_num']; ?>'
                                data-name='<?php echo $concern['fullname']; ?>'
                                data-section='<?php echo $concern['section']; ?>'
                                data-email='<?php echo $concern['email']; ?>'
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
    </main>

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
                                        <i class="fas fa-user me-2 fs-6 text-primary" style="font-size: 0.8rem;"></i>Name
                                    </div>
                                    <p class="fs-6 mb-0"><span id='modalName'></span></p>
                                </div>
                                <div class="p-4 rounded-3 bg-light mb-4">
                                    <div class="d-flex mb-1 align-items-center" style="font-weight: 600; font-size: 0.9rem;">
                                        <i class="fas fa-envelope me-2 fs-6 text-primary" style="font-size: 0.8rem;"></i>Email
                                    </div>
                                    <p class="fs-6 mb-0"><span id='modalEmail'></span></p>
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
                                        <i class="fas fa-users me-2 fs-6 text-primary" style="font-size: 0.8rem;"></i>Section
                                    </div>
                                    <p class="fs-6 mb-0"><span id='modalSection'></span></p>
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

        // Combined search and filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const concernCards = document.querySelectorAll('.card.rounded-4.mb-4');

            let currentFilter = 'all';
            let currentSearchTerm = '';

            // Store card data
            const cardData = Array.from(concernCards).map(card => {
                const statusElement = card.querySelector('.badge.px-3.py-2');
                const status = statusElement ?
                    statusElement.textContent.toLowerCase().replace(/\s+/g, '-') : '';

                return {
                    element: card,
                    referenceNum: card.querySelector('.h5.text-primary')?.textContent.toLowerCase() || '',
                    studentName: card.querySelector('.card-title')?.textContent.toLowerCase() || '',
                    concernType: card.querySelector('.badge.bg-light')?.textContent.toLowerCase() || '',
                    concernDetails: card.querySelector('.card-text')?.textContent.toLowerCase() || '',
                    status: status
                };
            });

            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    currentSearchTerm = this.value.toLowerCase().trim();
                    filterCards();
                });
            }

            // Filter functionality
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Update active state
                    filterButtons.forEach(btn => btn.classList.remove('active', 'btn-primary'));
                    filterButtons.forEach(btn => btn.classList.add('btn-outline-secondary'));
                    this.classList.add('active', 'btn-primary');
                    this.classList.remove('btn-outline-secondary');

                    currentFilter = this.getAttribute('data-filter');
                    filterCards();
                });
            });

            function filterCards() {
                let hasVisibleCards = false;

                cardData.forEach(data => {
                    // Check search match
                    const searchMatch = currentSearchTerm === '' ||
                        data.referenceNum.includes(currentSearchTerm) ||
                        data.studentName.includes(currentSearchTerm) ||
                        data.concernType.includes(currentSearchTerm) ||
                        data.concernDetails.includes(currentSearchTerm);

                    // Check filter match
                    const filterMatch = currentFilter === 'all' ||
                        data.status.includes(currentFilter);

                    // Show card if both conditions are met
                    const shouldShow = searchMatch && filterMatch;
                    data.element.style.display = shouldShow ? 'block' : 'none';

                    if (shouldShow) {
                        hasVisibleCards = true;
                    }
                });

                // Show/hide no results message
                showNoResultsMessage(!hasVisibleCards);
            }

            function showNoResultsMessage(show) {
                // Remove existing message
                const existingMessage = document.querySelector('.no-results-message');
                if (existingMessage) {
                    existingMessage.remove();
                }

                if (show) {
                    const noResultsMessage = document.createElement('div');
                    noResultsMessage.className = 'no-results-message text-center py-5';

                    let message = 'No concerns found';
                    if (currentSearchTerm !== '' && currentFilter !== 'all') {
                        message = `No ${currentFilter} concerns match your search for "<strong>${currentSearchTerm}</strong>"`;
                    } else if (currentSearchTerm !== '') {
                        message = `No concerns match your search for "<strong>${currentSearchTerm}</strong>"`;
                    } else if (currentFilter !== 'all') {
                        message = `No ${currentFilter} concerns found`;
                    }

                    noResultsMessage.innerHTML = `
                <div class="text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>${message}</h5>
                    <button class="btn btn-outline-primary mt-2" onclick="clearSearchAndFilter()">
                        Show All Concerns
                    </button>
                </div>
            `;

                    const filterSection = document.querySelector('.card.border-0.shadow-sm.rounded-3.mb-4');
                    if (filterSection) {
                        filterSection.parentNode.insertBefore(noResultsMessage, filterSection.nextSibling);
                    }
                }
            }

            // Clear search and filter function
            window.clearSearchAndFilter = function() {
                searchInput.value = '';
                currentSearchTerm = '';

                // Reset filter to 'all'
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                document.querySelector('.filter-btn[data-filter="all"]').classList.add('active', 'btn-primary');
                document.querySelector('.filter-btn[data-filter="all"]').classList.remove('btn-outline-secondary');

                currentFilter = 'all';
                filterCards();
                searchInput.focus();
            };
        });

        // Enhanced sort functionality for concerns
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('sortSelect');

            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const sortValue = this.value;
                    sortConcerns(sortValue);
                });
            }

            function sortConcerns(sortBy) {
                const concernCards = Array.from(document.querySelectorAll('.card.rounded-4.mb-4'));
                const concernsContainer = concernCards[0]?.parentElement;

                if (!concernsContainer) return;

                const cardData = concernCards.map(card => {
                    const dateText = card.querySelector('.text-muted.small')?.textContent || '';
                    const statusElement = card.querySelector('.badge.px-3.py-2');
                    const status = statusElement?.textContent.toLowerCase() || '';
                    const referenceNum = card.querySelector('.h5.text-primary')?.textContent || '';

                    return {
                        element: card,
                        date: parseDate(dateText),
                        status: status,
                        referenceNum: referenceNum,
                        statusElement: statusElement
                    };
                });

                cardData.sort((a, b) => {
                    switch (sortBy) {
                        case 'newest':
                            return b.date - a.date; // Newest first

                        case 'oldest':
                            return a.date - b.date; // Oldest first

                        case 'status':
                            return sortByStatus(a, b);

                        default:
                            return 0;
                    }
                });

                // Reorder cards in DOM with smooth animation
                cardData.forEach((data, index) => {
                    setTimeout(() => {
                        concernsContainer.appendChild(data.element);
                    }, index * 50); // Staggered animation
                });
            }

            function parseDate(dateText) {
                // Handle various date formats from "Submitted on [date]"
                const dateString = dateText.replace('Submitted on ', '');

                // Try parsing as ISO date first (if stored that way)
                let date = new Date(dateString);

                // If invalid, try parsing common formats
                if (isNaN(date.getTime())) {
                    // Try MM/DD/YYYY or similar formats
                    date = new Date(dateString.replace(/(\d+)(st|nd|rd|th)/, '$1'));
                }

                // Final fallback
                if (isNaN(date.getTime())) {
                    console.warn('Could not parse date:', dateString);
                    return new Date(); // Current date as fallback
                }

                return date;
            }

            function sortByStatus(a, b) {
                // Define custom status priority
                const statusPriority = {
                    'pending': 1,
                    'approved': 2,
                    'resolved': 3,
                    'case closed': 4
                };

                const priorityA = statusPriority[a.status] || 999;
                const priorityB = statusPriority[b.status] || 999;

                // If same status, sort by date (newest first)
                if (priorityA === priorityB) {
                    return b.date - a.date;
                }

                return priorityA - priorityB;
            }
        });
    </script>
</body>

</html>