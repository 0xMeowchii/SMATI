<?php
require_once 'includes/session.php';
include('../database.php');

$conn = connectToDB();
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();

$students = array();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
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
            $sql = "SELECT * FROM schoolyear ORDER BY schoolyear_id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            $schoolyearlist = array();
            while ($row = $result->fetch_assoc()) {
                $schoolyearlist[] = $row;
            }
            ?>

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
                                                echo "<tr>";
                                                echo "<td>" . $row['subject'] . "</td>";
                                                echo "<td>" . $row["lastname"] . ", " . $row["firstname"] . "</td>";
                                                echo "<td>" . $row['prelim'] . "</td>";
                                                echo "<td>" . $row['midterm'] . "</td>";
                                                echo "<td>" . $row['finals'] . "</td>";
                                                echo "<td>" . $row['average'] . "</td>";
                                                echo "<td>" . $row['equivalent'] . "</td>";
                                                echo "<td>" . $row['remarks'] . "</td>";
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
        </div>


    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize jsPDF
            const {
                jsPDF
            } = window.jspdf;

            // Export to PDF function
            document.getElementById('exportPdf').addEventListener('click', function() {
                generatePDF();
            });

            function generatePDF() {
                // Create new PDF document
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                // Student information
                const studentName = "<?php foreach ($students as $student) {
                                            echo $student['lastname'] . ", " . $student['firstname'];
                                        } ?>";
                const studentId = "<?php foreach ($students as $student) {
                                        echo $student['student_id'];
                                    } ?>";
                const currentDate = new Date().toLocaleDateString();

                // Set document properties
                doc.setProperties({
                    title: `Student Grades - ${studentName}`,
                    subject: 'Academic Transcript',
                    author: 'School Management System',
                    keywords: 'grades, transcript, academic',
                    creator: 'School Management System'
                });

                // Add header
                doc.setFontSize(20);
                doc.setTextColor(40, 40, 40);
                doc.text('ACADEMIC TRANSCRIPT', 105, 20, {
                    align: 'center'
                });

                // Add school information
                doc.setFontSize(12);
                doc.setTextColor(100, 100, 100);
                doc.text('St. Michael Technological Institute, Inc.', 105, 30, {
                    align: 'center'
                });
                doc.text('101 Rodriguez St. Santulan Rd., Malabon, Philippines', 105, 36, {
                    align: 'center'
                });

                // Add student information
                doc.setFontSize(10);
                doc.setTextColor(60, 60, 60);
                doc.text(`Student: ${studentName}`, 20, 50);
                doc.text(`Student ID: ${studentId}`, 20, 56);
                doc.text(`Date Generated: ${currentDate}`, 20, 62);

                // Process each school year table
                let yPosition = 80;
                const tables = document.querySelectorAll('.table');

                tables.forEach((table, index) => {
                    // Get school year from the card header
                    const cardHeader = table.closest('.custom-card').querySelector('.card-header h4');
                    const schoolYear = cardHeader ? cardHeader.textContent : `Semester ${index + 1}`;

                    // Add school year header
                    if (yPosition > 250) {
                        doc.addPage();
                        yPosition = 20;
                    }

                    doc.setFontSize(14);
                    doc.setTextColor(40, 40, 40);
                    doc.text(schoolYear, 20, yPosition);
                    yPosition += 10;

                    // Extract table data
                    const headers = [];
                    const rows = [];

                    // Get table headers
                    const headerCells = table.querySelectorAll('thead th');
                    headerCells.forEach(cell => {
                        headers.push(cell.textContent.trim());
                    });

                    // Get table rows
                    const tableRows = table.querySelectorAll('tbody tr');
                    tableRows.forEach(row => {
                        const rowData = [];
                        const cells = row.querySelectorAll('td');
                        cells.forEach(cell => {
                            rowData.push(cell.textContent.trim());
                        });
                        rows.push(rowData);
                    });

                    // Create table in PDF
                    doc.autoTable({
                        head: [headers],
                        body: rows,
                        startY: yPosition,
                        theme: 'grid',
                        styles: {
                            fontSize: 8,
                            cellPadding: 3,
                            overflow: 'linebreak'
                        },
                        headStyles: {
                            fillColor: [41, 128, 185],
                            textColor: 255,
                            fontStyle: 'bold'
                        },
                        alternateRowStyles: {
                            fillColor: [240, 240, 240]
                        },
                        margin: {
                            top: 10
                        }
                    });

                    // Update yPosition for next table
                    yPosition = doc.lastAutoTable.finalY + 15;
                });

                // Add footer
                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(8);
                    doc.setTextColor(150, 150, 150);
                    doc.text(`Page ${i} of ${pageCount}`, 105, 290, {
                        align: 'center'
                    });
                    doc.text('Generated by SMATI - Educational Portal', 105, 293, {
                        align: 'center'
                    });
                }

                // Save the PDF
                doc.save(`Grades_${studentName.replace(', ', '_')}_${currentDate.replace(/\//g, '-')}.pdf`);

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'PDF Generated',
                    text: 'Student grades have been exported successfully!',
                    timer: 4000,
                    showConfirmButton: false
                });
            }
        });
    </script>
</body>

</html>