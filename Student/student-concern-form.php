<?php
include('../database.php');

$conn = connectToDB();
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $fullname = $row['lastname'] . ", " . $row['firstname'];
    $email = $row['email'];
    $set = $row['course'];
} else {
    echo "Student not found!";
}

function getTodaySubmissionsCount($student_id)
{
    $conn = connectToDB();
    $today = date('Y-m-d');

    $sql = "SELECT COUNT(*) as count FROM concern 
            WHERE student_id = ? AND DATE(createdAt) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $student_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    return $row['count'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMATI - Submit Concern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #f72585;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            color: #4a5568;
            line-height: 1.6;
        }

        .navbar {
            background: linear-gradient(120deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.15);
            padding: 0.8rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border: none;
            border-radius: 10px;
            padding: 0.9rem 2rem;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(to right, var(--secondary), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }

        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: var(--transition);
            background: white;
            box-shadow: var(--card-shadow);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            border-bottom: 0;
        }

        footer {
            background: var(--dark);
            color: white;
            margin-top: 3rem;
            padding: 2rem 0;
        }

        .control-number-display {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 2px dashed var(--primary);
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.1);
            position: relative;
            overflow: hidden;
        }

        .control-number-display::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 60%);
            transform: rotate(30deg);
        }

        .control-number {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--secondary);
            letter-spacing: 1px;
            position: relative;
            text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.05);
        }

        .submission-counter {
            background: linear-gradient(to right, #edf2f7, #e2e8f0);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .submission-limit-warning {
            color: var(--accent);
            font-weight: 600;
            margin-top: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.7rem;
            font-size: 0.95rem;
        }

        .required-field::after {
            content: " *";
            color: var(--accent);
        }

        .feature-icon {
            color: var(--primary);
            margin-right: 0.7rem;
            font-size: 1.1rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            transition: var(--transition);
            font-size: 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-left: 4px solid var(--primary);
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        .floating-label {
            position: relative;
            margin-bottom: 1.8rem;
        }

        .floating-input {
            padding: 1.2rem 1rem;
            height: calc(3.7rem + 2px);
        }

        .floating-label label {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            transition: var(--transition);
            pointer-events: none;
            color: #94a3b8;
            background: white;
            padding: 0 0.5rem;
            border-radius: 5px;
        }

        .floating-input:focus~label,
        .floating-input:not(:placeholder-shown)~label {
            top: 0;
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
        }

        .ticket-preview {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: var(--card-shadow);
            display: none;
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px dashed #e2e8f0;
        }

        .ticket-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .ticket-field {
            margin-bottom: 1rem;
        }

        .ticket-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .ticket-value {
            color: #4a5568;
        }

        .ticket-footer {
            margin-top: 2rem;
            text-align: center;
            padding-top: 1rem;
            border-top: 2px dashed #e2e8f0;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .download-btn {
            margin-top: 1.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        /* Animation for success */
        @keyframes successTick {
            0% {
                transform: scale(0);
            }

            80% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .success-tick {
            animation: successTick 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>SMATI Concern Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" id="backLink">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php

    //INSERT QUERY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitButton'])) {
        $student_id = $_GET['id'];

        // Check daily submission limit
        $todaySubmissions = getTodaySubmissionsCount($student_id);

        if ($todaySubmissions >= 2) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Daily Limit Reached!',
                    text: 'You have already submitted 2 concerns today. Please try again tomorrow.',
                    confirmButtonColor: '#d33'
                });
              </script>";
            return; // Stop execution
        }

        // Proceed with insertion
        $conn = connectToDB();
        $section = $_POST['section'];
        $email = $_POST['email'];
        $teacher_id = $_POST['teacher'];
        $concernType = $_POST['concernType'];
        $details = $_POST['concernDetails'];
        $concernNumber = $_POST['concernNumber'];

        if ($conn) {
            // Insert with created_at
            $stmt = $conn->prepare("INSERT INTO concern (student_id, section, email, teacher_id, type, details, reference_num, createdAt) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ississs", $student_id, $section, $email, $teacher_id, $concernType, $details, $concernNumber);

            if ($stmt->execute()) {
                // Success handling...
                $insertedData = [
                    'concernNumber' => $concernNumber,
                    'name' => $_POST['name'],
                    'section' => $section,
                    'email' => $email,
                    'concernType' => $concernType,
                    'concernDetails' => $details
                ];

                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            html: 'Your reference number is: <strong>{$concernNumber}</strong>',
                            confirmButtonText: 'Download Ticket',
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const data = " . json_encode($insertedData) . ";
                                generatePDFTicket(data);
                            }
                        });
                    });
                  </script>";
            } else {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error!',
                            text: 'Database connection failed',
                            confirmButtonColor: '#d33'
                        });
                    });
                </script>";
            }

            $stmt->close();
            $conn->close();
        }
    }
    ?>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-dark mb-0 fw-bold">
                        <i class="fas fa-headset me-2 text-primary"></i>Submit a Concern
                    </h1>
                    <div class="submission-counter">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        <span id="submissionCount">0/2</span> submissions today
                    </div>
                </div>

                <!-- Submission Limit Alert -->
                <div id="limitAlert" class="alert alert-warning d-none animate-pulse">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    You have reached the maximum of 2 concerns per day. Please try again tomorrow.
                </div>

                <!-- Control Number Display -->
                <div class="control-number-display">
                    <p class="mb-2"><i class="fas fa-ticket-alt feature-icon"></i>Your Concern Reference Number</p>
                    <div class="control-number" id="controlNumberDisplay">SMATI2025-01</div>
                    <p class="text-muted mt-3 small">
                        <i class="fas fa-lightbulb me-1"></i>Please save this number for tracking your concern
                    </p>
                </div>


                <div class="card shadow-lg">
                    <div class="card-header py-3">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Concern Details</h4>
                    </div>
                    <div class="card-body p-5">
                        <!-- Form -->
                        <form id="concernForm" method="POST" action="">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="floating-label">
                                        <input type="text" class="form-control floating-input" name="name" placeholder=" " value="<?php echo $fullname ?>" required>
                                        <label for="name" class="required-field">Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="floating-label">
                                        <input type="text" class="form-control floating-input" name="section" placeholder=" " value="<?php echo $set ?>" required>
                                        <label for="section" class="required-field">Section</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="floating-label">
                                    <input type="email" class="form-control floating-input" name="email" placeholder=" " value="<?php echo $email ?>" required>
                                    <label for="email" class="required-field">Email Address</label>
                                </div>
                                <div class="form-text ms-2">We'll never share your email with anyone else.</div>
                            </div>

                            <div class="mb-4">
                                <label for="teacher" class="form-label required-field">Teacher</label>
                                <select class="form-select" name="teacher" required>
                                    <option value="" disabled selected>Select teacher</option>
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
                                <div class="form-text ms-2">Who do you want to send your concern.</div>
                            </div>

                            <div class="mb-4">
                                <label for="concernType" class="form-label required-field">Concern Type</label>
                                <select class="form-select" name="concernType" required>
                                    <option value="" disabled selected>Select concern type</option>
                                    <option value="Grades">Grades</option>
                                    <option value="Attendance">Attendance</option>
                                    <option value="Schedule">Schedule</option>
                                    <option value="Materials">Learning Materials</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="concernDetails" class="form-label required-field">Concern Details</label>
                                <textarea class="form-control" name="concernDetails" rows="5" placeholder="Please provide specific details about your concern..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="concernNumber" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" name="concernNumber" id="concernNumber" readonly>
                                <div class="form-text">This number is automatically generated for tracking purposes.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg py-3" id="submitButton" name="submitButton">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Concern
                                </button>
                            </div>

                            <div class="submission-limit-warning text-center mt-3">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Limit: 2 concerns per day
                            </div>
                        </form>

                        <!-- PDF Ticket Preview (initially hidden) -->
                        <div class="ticket-preview mt-4" id="ticketPreview">
                            <div class="ticket-header">
                                <h4 class="text-primary mb-2">SMATI Concern Ticket</h4>
                                <p class="text-muted">Your concern has been registered successfully</p>
                            </div>

                            <div class="ticket-body">
                                <div class="ticket-field">
                                    <div class="ticket-label">Reference Number</div>
                                    <div class="ticket-value fw-bold" id="ticketRef">SMATI2025-01</div>
                                </div>

                                <div class="ticket-field">
                                    <div class="ticket-label">Date Submitted</div>
                                    <div class="ticket-value" id="ticketDate">May 15, 2023</div>
                                </div>

                                <div class="ticket-field">
                                    <div class="ticket-label">Name</div>
                                    <div class="ticket-value" id="ticketName">John Doe</div>
                                </div>

                                <div class="ticket-field">
                                    <div class="ticket-label">Section</div>
                                    <div class="ticket-value" id="ticketSection">BSIT-3A</div>
                                </div>

                                <div class="ticket-field">
                                    <div class="ticket-label">Email</div>
                                    <div class="ticket-value" id="ticketEmail">john.doe@example.com</div>
                                </div>

                                <div class="ticket-field">
                                    <div class="ticket-label">Concern Type</div>
                                    <div class="ticket-value" id="ticketType">Grades</div>
                                </div>

                                <div class="ticket-field" style="grid-column: span 2;">
                                    <div class="ticket-label">Concern Details</div>
                                    <div class="ticket-value" id="ticketDetails">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eu ultricies lacinia, nunc nisl aliquam nisl, eu aliquam nisl nunc eu nisl.</div>
                                </div>
                            </div>

                            <div class="ticket-footer">
                                <p>Thank you for submitting your concern. We will address it shortly.</p>
                                <button class="btn btn-primary download-btn" id="downloadTicket">
                                    <i class="fas fa-download me-1"></i> Download Ticket as PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-4 info-card">
                    <div class="card-body p-4">
                        <h5 class="mb-4"><i class="fas fa-info-circle feature-icon"></i>About the Process</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex">
                                    <i class="fas fa-check-circle text-success mt-1 me-3 fs-5"></i>
                                    <span>Each concern receives a unique reference number</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex">
                                    <i class="fas fa-check-circle text-success mt-1 me-3 fs-5"></i>
                                    <span>You can submit a maximum of 2 concerns per day</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex">
                                    <i class="fas fa-check-circle text-success mt-1 me-3 fs-5"></i>
                                    <span>Use your reference number to track the status</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 text-center mt-5">
        <p class="mb-0">© 2025 SMATI Concern Portal | Designed for Student Support</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('backLink').addEventListener('click', function(e) {
            e.preventDefault();
            history.back();
        });

        // Initialize jsPDF
        const {
            jsPDF
        } = window.jspdf;

        document.addEventListener("DOMContentLoaded", function() {
            // Initialize the page
            initializePage();
        });

        function initializePage() {
            // Check submission limit on page load
            checkSubmissionLimit();

            // Generate initial concern number from server
            generateConcernNumber();

            // Initialize floating labels
            initFloatingLabels();
        }

        function initFloatingLabels() {
            const inputs = document.querySelectorAll('.floating-input');
            inputs.forEach(input => {
                if (input.value) {
                    input.classList.add('has-value');
                }

                input.addEventListener('focus', () => {
                    input.classList.add('focused');
                });

                input.addEventListener('blur', () => {
                    if (!input.value) {
                        input.classList.remove('focused', 'has-value');
                    } else {
                        input.classList.add('has-value');
                    }
                });
            });
        }

        // Function to check submission limit via AJAX
        function checkSubmissionLimit() {
            fetch('check-submission-limit.php?student_id=<?php echo $_GET["id"]; ?>')
                .then(response => response.json())
                .then(data => {
                    console.log('Submission count:', data.count); // Debug log
                    document.getElementById("submissionCount").textContent = `${data.count}/2`;

                    if (data.count >= 2) {
                        document.getElementById("limitAlert").classList.remove("d-none");
                        document.getElementById("submitButton").disabled = true;
                        document.getElementById("submitButton").innerHTML = '<i class="fas fa-ban me-2"></i>Daily Limit Reached';
                        document.getElementById("submitButton").classList.remove("btn-primary");
                        document.getElementById("submitButton").classList.add("btn-secondary");
                    } else {
                        document.getElementById("limitAlert").classList.add("d-none");
                        document.getElementById("submitButton").disabled = false;
                        document.getElementById("submitButton").innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Concern';
                        document.getElementById("submitButton").classList.remove("btn-secondary");
                        document.getElementById("submitButton").classList.add("btn-primary");
                    }
                })
                .catch(error => {
                    console.error('Error checking submission limit:', error);
                    document.getElementById("submissionCount").textContent = '0/2';
                });
        }

        // Function to generate concern number from server
        function generateConcernNumber() {
            fetch('generate-concern-number.php?student_id=<?php echo $_GET["id"]; ?>')
                .then(response => response.json())
                .then(data => {
                    console.log('Generated concern number:', data.concernNumber); // Debug log
                    if (data.concernNumber) {
                        document.getElementById("concernNumber").value = data.concernNumber;
                        document.getElementById("controlNumberDisplay").textContent = data.concernNumber;
                    }
                })
                .catch(error => {
                    console.error('Error generating concern number:', error);
                    // Fallback: generate client-side number
                    generateFallbackConcernNumber();
                });
        }

        // Fallback function if server generation fails
        function generateFallbackConcernNumber() {
            const year = new Date().getFullYear();
            const timestamp = Date.now().toString().slice(-4);
            const concernNumber = `SMATI${year}-${timestamp}`;

            document.getElementById("concernNumber").value = concernNumber;
            document.getElementById("controlNumberDisplay").textContent = concernNumber;
        }

        function generatePDFTicket(data) {
            // Create a new jsPDF instance
            const doc = new jsPDF();

            // Add background
            doc.setFillColor(243, 246, 255);
            doc.rect(0, 0, 210, 297, 'F');

            // Add header
            doc.setDrawColor(67, 97, 238);
            doc.setFillColor(67, 97, 238);
            doc.rect(0, 0, 210, 40, 'F');

            // Add title
            doc.setFontSize(20);
            doc.setTextColor(255, 255, 255);
            doc.text("SMATI CONCERN TICKET", 105, 25, {
                align: 'center'
            });

            // Add ticket content
            doc.setFontSize(12);
            doc.setTextColor(0, 0, 0);

            // Reference number
            doc.setFont(undefined, 'bold');
            doc.text("REFERENCE NUMBER:", 20, 60);
            doc.setFont(undefined, 'normal');
            doc.text(data.concernNumber, 70, 60);

            // Date
            doc.setFont(undefined, 'bold');
            doc.text("DATE SUBMITTED:", 20, 70);
            doc.setFont(undefined, 'normal');
            doc.text(new Date().toLocaleDateString(), 70, 70);

            // Divider line
            doc.setDrawColor(200, 200, 200);
            doc.line(20, 80, 190, 80);

            // User information
            doc.setFont(undefined, 'bold');
            doc.text("NAME:", 20, 90);
            doc.setFont(undefined, 'normal');
            doc.text(data.name, 50, 90);

            doc.setFont(undefined, 'bold');
            doc.text("SECTION:", 20, 100);
            doc.setFont(undefined, 'normal');
            doc.text(data.section, 50, 100);

            doc.setFont(undefined, 'bold');
            doc.text("EMAIL:", 20, 110);
            doc.setFont(undefined, 'normal');
            doc.text(data.email, 50, 110);

            doc.setFont(undefined, 'bold');
            doc.text("CONCERN TYPE:", 20, 120);
            doc.setFont(undefined, 'normal');
            doc.text(data.concernType, 60, 120);

            // Another divider line
            doc.line(20, 130, 190, 130);

            // Concern details
            doc.setFont(undefined, 'bold');
            doc.text("CONCERN DETAILS:", 20, 140);

            // Split the concern details into multiple lines if needed
            const splitDetails = doc.splitTextToSize(data.concernDetails, 170);
            doc.setFont(undefined, 'normal');
            doc.text(splitDetails, 20, 150);

            // Footer
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.text("Thank you for submitting your concern. We will address it shortly.", 105, 270, {
                align: 'center'
            });
            doc.text("© 2025 SMATI Concern Portal", 105, 280, {
                align: 'center'
            });

            // Save the PDF
            doc.save(`SMATI-Ticket-${data.concernNumber}.pdf`);
        }

        // Add download button event listener
        document.getElementById("downloadTicket")?.addEventListener("click", function() {
            const data = {
                concernNumber: document.getElementById("ticketRef").textContent,
                name: document.getElementById("ticketName").textContent,
                section: document.getElementById("ticketSection").textContent,
                email: document.getElementById("ticketEmail").textContent,
                concernType: document.getElementById("ticketType").textContent,
                concernDetails: document.getElementById("ticketDetails").textContent
            };

            generatePDFTicket(data);
        });
    </script>
</body>

</html>