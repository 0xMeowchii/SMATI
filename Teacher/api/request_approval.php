<?php
include('../../session.php');
include('../../database.php');
include('../includes/grade_submission_helper.php');

// Find and start the teacher session
$userSessionFound = false;
foreach ($_COOKIE as $cookieName => $cookieValue) {
    if (preg_match('/^(teacher)_\d+$/', $cookieName)) {
        session_name($cookieName);
        session_start();
        $userSessionFound = true;
        break;
    }
}

if (!$userSessionFound) {
    echo json_encode(['success' => false, 'message' => 'Session not found. Please login again.']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = $_SESSION['id'] ?? null;
    $subject_id = $_POST['subject_id'] ?? null;
    $schoolyear_id = $_POST['sy_id'] ?? null;
    $list_id = $_POST['list_id'] ?? null;
    $reason = $_POST['reason'] ?? '';

    if (!$teacher_id || !$subject_id || !$schoolyear_id || !$list_id) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $conn = connectToDB();
    $result = requestApproval($conn, $teacher_id, $subject_id, $schoolyear_id, $list_id, $reason);
    $conn->close();

    echo json_encode($result);
}
