<?php
include '../../database.php';
include '../../session.php';
include '../../includes/activity_logger.php';
include '../includes/grade_submission_helper.php';

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

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['id'];
    $request_id = $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? null; // 'approve' or 'reject'

    if (!$request_id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $conn = connectToDB();

    // Get request details for logging
    $request_details_sql = "SELECT ar.*, 
                                   t.firstname as teacher_firstname,
                                   t.lastname as teacher_lastname,
                                   subj.subject_code,
                                   subj.subject,
                                   sy.schoolyear,
                                   sy.semester,
                                   sl.student_set
                            FROM approval_requests ar
                            INNER JOIN teachers t ON ar.teacher_id = t.teacher_id
                            INNER JOIN subjects subj ON ar.subject_id = subj.subject_id
                            INNER JOIN schoolyear sy ON ar.schoolyear_id = sy.schoolyear_id
                            INNER JOIN student_list sl ON ar.list_id = sl.list_id
                            WHERE ar.request_id = ?";
    
    $stmt = $conn->prepare($request_details_sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request_details_result = $stmt->get_result();
    $request_details = $request_details_result->fetch_assoc();
    $stmt->close();

    if ($action === 'approve') {
        $result = approveRequest($conn, $request_id, $admin_id);

        if ($result['success'] && $request_details) {
            $subject_name = $request_details['subject_code'] . ' - ' . $request_details['subject'];
            $schoolyear_semester = $request_details['schoolyear'] . ', ' . $request_details['semester'] . ' Semester';
            $teacher_name = $request_details['teacher_firstname'] . ' ' . $request_details['teacher_lastname'];
            $student_set = $request_details['student_set'];
            
            logActivity(
                $conn,
                $admin_id,
                $_SESSION['user_type'],
                'APPROVE_GRADE_REQUEST',
                "Approved grade submission for {$subject_name} ({$student_set}) - {$schoolyear_semester} - Teacher: {$teacher_name}"
            );
        }
    } elseif ($action === 'reject') {
        $result = rejectRequest($conn, $request_id, $admin_id);

        if ($result['success'] && $request_details) {
            $subject_name = $request_details['subject_code'] . ' - ' . $request_details['subject'];
            $schoolyear_semester = $request_details['schoolyear'] . ', ' . $request_details['semester'] . ' Semester';
            $teacher_name = $request_details['teacher_firstname'] . ' ' . $request_details['teacher_lastname'];
            $student_set = $request_details['student_set'];
            
            logActivity(
                $conn,
                $admin_id,
                $_SESSION['user_type'],
                'REJECT_GRADE_REQUEST',
                "Rejected grade submission for {$subject_name} ({$student_set}) - {$schoolyear_semester} - Teacher: {$teacher_name}"
            );
        }
    } else {
        $result = ['success' => false, 'message' => 'Invalid action'];
    }

    $conn->close();
    echo json_encode($result);
}