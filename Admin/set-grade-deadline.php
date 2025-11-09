<?php

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['id'];

// Get all subjects for setting deadlines
$conn = connectToDB();
$sql = "SELECT s.*, 
               sy.schoolyear, 
               sy.semester,
               t.firstname as teacher_firstname,
               t.lastname as teacher_lastname,
               s.submission_due_date
        FROM subjects s
        INNER JOIN schoolyear sy ON s.schoolyear_id = sy.schoolyear_id
        INNER JOIN teachers t ON s.teacher_id = t.teacher_id
        WHERE sy.status = '1' AND s.status = '1'
        ORDER BY sy.schoolyear DESC, s.subject ASC";

$result = $conn->query($sql);
$subjects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

// Handle deadline setting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_deadline'])) {
    $subject_id = $_POST['subject_id'];
    $schoolyear_id = $_POST['schoolyear_id'];
    $due_date = $_POST['due_date'];

    $result = setDueDate($conn, $subject_id, $schoolyear_id, $due_date);

    if ($result['success']) {
        logActivity(
            $conn,
            $admin_id,
            $_SESSION['user_type'],
            'SET_GRADE_DEADLINE',
            "Set grade submission deadline for subject ID: $subject_id to $due_date"
        );

        // Store success flag in session to show after page reload
        $_SESSION['deadline_set_success'] = true;

        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$conn->close();
