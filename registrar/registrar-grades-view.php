<?php
include('../database.php');

$conn = connectToDB();
$user_image = '';

$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();

$students = array();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

if ($conn) {

    $stmt = $conn->prepare("SELECT image FROM students WHERE student_id = ?");

    if (isset($stmt)) {
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $stmt->bind_result($user_image);
        $stmt->fetch();
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
        .btn-export {
            background-color: #198754;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .btn-export:hover {
            background-color: #157347;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php

    include('includes/sidebar.php');

    ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-file me-2"></i>Student Grades > <?php foreach ($students as $student) {
                                                                        echo $student['lastname'] . ", " . $student['firstname'];
                                                                    } ?></h4>
            <img class="border border-2" src="<?php echo !empty($user_image) ? $user_image : '../images/logo5.png';  ?>" alt="Profile" width="100px" height="100px" style="object-fit: cover; cursor: pointer;" id="student_profile_image">
        </div>
        <div class="container">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>All Grades</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <button id="exportPdf" class="btn-export">
                            <i class="fas fa-file-pdf me-2"></i>Export to PDF
                        </button>
                    </div>
                </div>
            </div>


            <?php
            $conn = connectToDB();
            $sql = "SELECT * FROM schoolyear WHERE status = '1' ORDER BY schoolyear_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            $schoolyearlist = array();
            while ($row = $result->fetch_assoc()) {
                $schoolyearlist[] = $row;
            }
            ?>

            <?php if (!empty($schoolyearlist)): ?>

                <?php foreach ($schoolyearlist as $schoolyear): ?>

                    <div class="col mt-3 shadow rounded-bottom-3 bg-white">
                        <div class="card-border-0 custom-card mb-4">
                            <div class="card-header px-4 py-3 bg-primary text-white rounded-top-3">
                                <h4 class="card-title mb-0"><?php echo htmlspecialchars($schoolyear['schoolyear'] . ', ' . $schoolyear['semester']) . ' Semester'; ?></h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <th>Subject</th>
                                            <th>Teacher</th>
                                            <th>Prelim</th>
                                            <th>Midterm</th>
                                            <th>Finals</th>
                                            <th>Average</th>
                                            <th>Equivalent</th>
                                            <th>Remarks</th>
                                            <th>Comment</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $conn = connectToDB();
                                            $sql = "SELECT * 
                                                FROM grades g
                                                INNER JOIN teachers t ON g.teacher_id = t.teacher_id
                                                INNER JOIN subjects s ON g.subject_id = s.subject_id
                                                WHERE g.student_id = ? AND g.schoolyear_id = ?";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param("ii", $_GET['id'], $schoolyear['schoolyear_id']);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $badgeClass = '';
                                                    if ($row['remarks'] === 'Passed') {
                                                        $badgeClass = 'badge bg-success';
                                                    } elseif ($row['remarks'] === 'Pending') {
                                                        $badgeClass = 'badge bg-warning text-dark';
                                                    } elseif ($row['remarks'] === 'Failed') {
                                                        $badgeClass = 'badge bg-danger';
                                                    } else {
                                                        $badgeClass = 'badge bg-secondary';
                                                    }
                                                    echo "<tr>";
                                                    echo "<td>" . $row['subject'] . "</td>";
                                                    echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                                    echo "<td>" . ($row['prelim'] !== null ? $row['prelim'] : '-') . "</td>";
                                                    echo "<td>" . ($row['midterm'] !== null ? $row['midterm'] : '-') . "</td>";
                                                    echo "<td>" . ($row['finals'] !== null ? $row['finals'] : '-') . "</td>";
                                                    echo "<td>" . $row['average'] . "</td>";
                                                    echo "<td>" . $row['equivalent'] . "</td>";
                                                    echo "<td><span class='$badgeClass'>" . $row['remarks'] . "</span></td>";
                                                    echo "<td>" . $row['comment'] . "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<td colspan='10' class='text-center py-4' style='color: #6c757d;'>";
                                                echo "<i class='fas fa-search mb-2' style='font-size: 2em; opacity: 0.5;'></i>";
                                                echo "<br>";
                                                echo "No submitted Grades yet.";
                                                echo "</td>";
                                            }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-book mb-3" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Grades Submitted Yet.</h4>
                    <p>The teachers has not submitted any grades for the student yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- E-Signature Modal -->
    <div class="modal fade" id="esignatureModal" tabindex="-1" aria-labelledby="esignatureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="esignatureModalLabel">Add E-Signature (Optional)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You can optionally add an e-signature to the PDF. This is completely optional.</p>

                    <div class="mb-3">
                        <label for="signatoryName" class="form-label">Signatory Name</label>
                        <input type="text" class="form-control" id="signatoryName" placeholder="Enter your name">
                    </div>

                    <div class="mb-3">
                        <label for="esignatureUpload" class="form-label">E-Signature Image</label>
                        <input class="form-control" type="file" id="esignatureUpload" accept="image/*">
                        <div class="form-text">Upload an image of your signature (PNG, JPG, etc.)</div>
                    </div>

                    <div class="esignature-preview" id="esignaturePreview">
                        <span class="text-muted">Signature preview will appear here</span>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="includeSignature">
                        <label class="form-check-label" for="includeSignature">
                            Include e-signature in PDF
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="generatePdf">Generate PDF</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize jsPDF
            const {
                jsPDF
            } = window.jspdf;

            // Variables to store signature data
            let signatoryName = '';
            let signatureImage = null;

            // Image popup functionality for sidebar profile image
            const studentProfileImage = document.getElementById('student_profile_image');

            if (studentProfileImage) {
                studentProfileImage.addEventListener('click', function() {
                    const imageSrc = this.src;

                    // Create popup overlay
                    const popupOverlay = document.createElement('div');
                    popupOverlay.className = 'image-popup-overlay';
                    popupOverlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                cursor: zoom-out;
            `;

                    // Create popup content
                    const popupContent = document.createElement('div');
                    popupContent.style.cssText = `
                position: relative;
                max-width: 90%;
                max-height: 90%;
                display: flex;
                justify-content: center;
                align-items: center;
            `;

                    // Create image element
                    const popupImage = document.createElement('img');
                    popupImage.src = imageSrc;
                    popupImage.style.cssText = `
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                border: 4px solid white;
            `;

                    // Create close button
                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '&times;';
                    closeButton.style.cssText = `
                position: absolute;
                top: -40px;
                right: -40px;
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                font-size: 30px;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                justify-content: center;
                align-items: center;
                transition: background 0.3s ease;
            `;

                    // Add hover effect to close button
                    closeButton.addEventListener('mouseenter', function() {
                        this.style.background = 'rgba(255, 255, 255, 0.3)';
                    });
                    closeButton.addEventListener('mouseleave', function() {
                        this.style.background = 'rgba(255, 255, 255, 0.2)';
                    });

                    // Close popup function
                    function closePopup() {
                        document.body.removeChild(popupOverlay);
                        document.removeEventListener('keydown', handleKeyPress);
                    }

                    // Handle keyboard events
                    function handleKeyPress(e) {
                        if (e.key === 'Escape') {
                            closePopup();
                        }
                    }

                    // Add event listeners
                    closeButton.addEventListener('click', closePopup);
                    popupOverlay.addEventListener('click', function(e) {
                        if (e.target === popupOverlay) {
                            closePopup();
                        }
                    });
                    document.addEventListener('keydown', handleKeyPress);

                    // Assemble and append to body
                    popupContent.appendChild(popupImage);
                    popupContent.appendChild(closeButton);
                    popupOverlay.appendChild(popupContent);
                    document.body.appendChild(popupOverlay);

                    // Add animation
                    popupOverlay.style.opacity = '0';
                    popupOverlay.style.transition = 'opacity 0.3s ease';

                    setTimeout(() => {
                        popupOverlay.style.opacity = '1';
                    }, 10);
                });
            }

            // Export to PDF function
            document.getElementById('exportPdf').addEventListener('click', function() {

                // Reset form
                document.getElementById('signatoryName').value = '';
                document.getElementById('esignatureUpload').value = '';
                document.getElementById('includeSignature').checked = false;
                document.getElementById('esignaturePreview').innerHTML = '<span class="text-muted">Signature preview will appear here</span>';

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('esignatureModal'));
                modal.show();
            });

            document.getElementById('esignatureUpload').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        signatureImage = event.target.result;
                        document.getElementById('esignaturePreview').innerHTML =
                            `<img src="${signatureImage}" alt="Signature Preview" height='100px' weight='150px'>`;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Generate PDF button in modal
            document.getElementById('generatePdf').addEventListener('click', function() {
                // Get signatory name
                signatoryName = document.getElementById('signatoryName').value.trim();

                // Check if signature should be included
                const includeSignature = document.getElementById('includeSignature').checked;

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('esignatureModal'));
                modal.hide();

                // Generate PDF
                generatePDF(includeSignature);
            });

            function generatePDF(includeSignature) {
                // Create new PDF document
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                // Load Century Gothic font (you need to have the font file)
                // Option 1: If you have the font file locally
                const centuryGothicNormal = '../fonts/centurygothic.ttf';
                const centuryGothicBold = '../fonts/centurygothic_bold.ttf';

                // Add the font to jsPDF (you need to load the font files first)
                doc.addFont(centuryGothicNormal, 'CenturyGothic', 'normal');
                doc.addFont(centuryGothicBold, 'CenturyGothic', 'bold');
                doc.setFont('CenturyGothic');

                // Option 2: Use built-in fonts that are similar to Century Gothic
                // Since Century Gothic might not be available, use similar fonts:
                // - "helvetica" is clean and similar
                // - "arial" is widely available
                // - "verdana" is also clean and modern

                // Student information
                const studentName = "<?php foreach ($students as $student) {
                                            echo $student['lastname'] . ', ' . $student['firstname'];
                                        } ?>";
                const studentId = "<?php foreach ($students as $student) {
                                        echo $student['student_id'];
                                    } ?>";
                const currentDateTime = new Date().toLocaleString();

                // Set document properties
                doc.setProperties({
                    title: `Student Grades - ${studentName}`,
                    subject: 'Academic Transcript',
                    author: 'School Management System',
                    keywords: 'grades, transcript, academic',
                    creator: 'School Management System'
                });

                // Add logo
                const logoUrl = '../images/logo5.png';
                try {
                    // Add logo to the top left and right
                    doc.addImage(logoUrl, 'PNG', 20, 10, 30, 30);
                } catch (e) {
                    console.warn('Logo could not be loaded:', e);
                }

                // Add header - BOLD
                doc.setFontSize(20);
                doc.setFont(undefined, 'bold');
                doc.setTextColor(40, 40, 40);
                doc.text('ACADEMIC TRANSCRIPT', 105, 20, {
                    align: 'center'
                });

                // Add school information - School name BOLD, address normal
                doc.setFontSize(12);
                doc.setFont(undefined, 'bold');
                doc.setTextColor(100, 100, 100);
                doc.text('St. Michael Arcangel Technological Institute, Inc.', 105, 30, {
                    align: 'center'
                });

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');
                doc.text('101 Rodriguez St. Santulan Rd., Malabon', 105, 36, {
                    align: 'center'
                });

                // Add student information - Labels BOLD, values normal
                doc.setFontSize(10);
                doc.setTextColor(60, 60, 60);

                // Student name with bold label
                doc.setFont(undefined, 'bold');
                doc.text('Student:', 20, 50);
                doc.setFont(undefined, 'normal');
                doc.text(studentName, 40, 50);

                // Student ID with bold label
                doc.setFont(undefined, 'bold');
                doc.text('Student ID:', 20, 56);
                doc.setFont(undefined, 'normal');
                doc.text(studentId, 40, 56);

                // Date generated with bold label
                doc.setFont(undefined, 'bold');
                doc.text('Date Generated:', 20, 62);
                doc.setFont(undefined, 'normal');
                doc.text(currentDateTime, 50, 62);

                // Add signature area if requested
                if (includeSignature && (signatoryName || signatureImage)) {
                    let signatureY = 70;

                    if (signatureImage) {
                        try {
                            doc.addImage(signatureImage, 'PNG', 20, signatureY, 40, 15);
                            signatureY += 20;
                        } catch (e) {
                            console.error('Error adding signature image:', e);
                        }
                    }

                    if (signatoryName) {
                        doc.setFontSize(10);
                        doc.setTextColor(60, 60, 60);

                        doc.setFont(undefined, 'bold');
                        doc.text('Signed by:', 20, signatureY);
                        doc.setFont(undefined, 'normal');
                        doc.text(`Registrar - ${signatoryName}`, 45, signatureY);

                        signatureY += 10;
                    }
                }

                // Process each school year table
                let yPosition = includeSignature && (signatoryName || signatureImage) ? 100 : 80;
                const tables = document.querySelectorAll('.table');

                tables.forEach((table, index) => {
                    const cardHeader = table.closest('.custom-card').querySelector('.card-header h4');
                    const schoolYear = cardHeader ? cardHeader.textContent : `Semester ${index + 1}`;

                    if (yPosition > 250) {
                        doc.addPage();
                        yPosition = 20;
                    }

                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(40, 40, 40);
                    doc.text(schoolYear, 20, yPosition);
                    yPosition += 10;

                    // Extract table data
                    const headers = [];
                    const rows = [];

                    const headerCells = table.querySelectorAll('thead th');
                    headerCells.forEach(cell => {
                        headers.push(cell.textContent.trim());
                    });

                    const tableRows = table.querySelectorAll('tbody tr');
                    tableRows.forEach(row => {
                        const rowData = [];
                        const cells = row.querySelectorAll('td');
                        cells.forEach(cell => {
                            rowData.push(cell.textContent.trim());
                        });
                        rows.push(rowData);
                    });

                    // Create table in PDF with Century Gothic font
                    doc.autoTable({
                        head: [headers],
                        body: rows,
                        startY: yPosition,
                        theme: 'grid',
                        styles: {
                            fontSize: 8,
                            cellPadding: 3,
                            overflow: 'linebreak',
                            fontStyle: 'normal',
                            font: 'CenturyGothic' // Set font for table
                        },
                        headStyles: {
                            fillColor: [41, 128, 185],
                            textColor: 255,
                            fontStyle: 'bold',
                            fontSize: 9,
                            font: 'CenturyGothic' // Set font for table headers
                        },
                        alternateRowStyles: {
                            fillColor: [240, 240, 240]
                        },
                        margin: {
                            top: 10
                        },
                        didParseCell: function(data) {
                            if (data.section === 'body' && data.column.index === 0) {
                                data.cell.styles.fontStyle = 'bold';
                            }
                            // Ensure all cells use the same font
                            data.cell.styles.font = 'CenturyGothic';
                        }
                    });

                    yPosition = doc.lastAutoTable.finalY + 15;
                });

                // Add footer
                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(8);
                    doc.setTextColor(150, 150, 150);
                    doc.setFont('CenturyGothic'); // Set font for footer

                    doc.text(`Page ${i} of ${pageCount}`, 105, 290, {
                        align: 'center'
                    });

                    doc.text('Generated by SMATI - Educational Portal', 105, 293, {
                        align: 'center'
                    });
                }

                // Save the PDF
                doc.save(`Grades_${studentName.replace(', ', '_')}_${currentDateTime.replace(/\//g, '-')}.pdf`);

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'PDF Generated',
                    text: 'Student grades have been exported successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    </script>
</body>

</html>