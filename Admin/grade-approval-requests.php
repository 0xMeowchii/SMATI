<?php

require_once '../session.php';
include 'includes/grade_submission_helper.php';

$userSessionFound = false;
foreach ($_COOKIE as $cookieName => $cookieValue) {
    if (preg_match('/^(admin)_\d+$/', $cookieName)) {
        // Found a user session cookie
        session_name($cookieName);
        session_start();
        $userSessionFound = true;
        break;
    }
}

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$admin_id = $_SESSION['id'];

// Get all pending requests
$conn = connectToDB();
$pending_requests = getPendingRequests($conn);

// Get all requests (for history)
$sql = "SELECT ar.*, 
               t.firstname as teacher_firstname,
               t.lastname as teacher_lastname,
               s.subject,
               sy.schoolyear,
               sy.semester,
               sl.student_set,
               sl.submission_count
        FROM approval_requests ar
        INNER JOIN teachers t ON ar.teacher_id = t.teacher_id
        INNER JOIN subjects subj ON ar.subject_id = subj.subject_id
        INNER JOIN subjects s ON ar.subject_id = s.subject_id
        INNER JOIN schoolyear sy ON ar.schoolyear_id = sy.schoolyear_id
        INNER JOIN student_list sl ON ar.list_id = sl.list_id
        LEFT JOIN admin a ON ar.admin_id = a.admin_id
        WHERE ar.status != 'pending'
        ORDER BY ar.updated_at DESC
        LIMIT 50";

$result = $conn->query($sql);
$history = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
}

$conn->close();
