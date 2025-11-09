<?php

/**
 * Check if teacher can submit grades for a subject
 * Returns array with status info
 */
function canSubmitGrades($conn, $teacher_id, $subject_id, $list_id, $schoolyear_id)
{
    $sql = "SELECT sl.submission_count, sl.pending_approval, s.submission_due_date 
            FROM student_list sl
            INNER JOIN  subjects s ON sl.subject_id = s.subject_id
            WHERE s.subject_id = ? AND s.teacher_id = ? AND sl.list_id = ? AND s.schoolyear_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $subject_id, $teacher_id, $list_id, $schoolyear_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();

    if (!$subject) {
        return [
            'can_submit' => false,
            'reason' => 'Subject not found or you are not authorized',
            'submission_count' => 0
        ];
    }

    // Check if due date has passed
    if ($subject['submission_due_date']) {
        $due_date = new DateTime($subject['submission_due_date']);
        $now = new DateTime();
        if ($now > $due_date) {
            return [
                'can_submit' => false,
                'reason' => 'Submission deadline has passed (' . date('M d, Y h:i A', strtotime($subject['submission_due_date'])) . ')',
                'submission_count' => $subject['submission_count'],
                'due_date' => $subject['submission_due_date']
            ];
        }
    }

    // Check if pending approval
    if ($subject['pending_approval'] == 1) {
        return [
            'can_submit' => false,
            'reason' => 'You have a pending approval request. Please wait for admin response.',
            'submission_count' => $subject['submission_count'],
            'is_pending' => true
        ];
    }

    // Check submission count
    if ($subject['submission_count'] >= 3) {
        return [
            'can_submit' => false,
            'reason' => 'Maximum submissions reached (3/3). Please request admin approval to submit again.',
            'submission_count' => $subject['submission_count'],
            'needs_approval' => true
        ];
    }

    return [
        'can_submit' => true,
        'reason' => 'Can submit',
        'submission_count' => $subject['submission_count'],
        'submissions_left' => 3 - $subject['submission_count']
    ];
}

/**
 * Increment submission count after successful grade submission
 */
function incrementSubmissionCount($conn, $subject_id, $teacher_id, $list_id, $schoolyear_id)
{
    $sql = "UPDATE student_list sl
            INNER JOIN subjects s ON sl.subject_id = s.subject_id
            SET sl.submission_count = sl.submission_count + 1
            WHERE s.subject_id = ? AND s.teacher_id = ? AND sl.list_id = ? AND s.schoolyear_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $subject_id, $teacher_id, $list_id, $schoolyear_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Get current submission count
 */
function getSubmissionCount($conn, $subject_id, $teacher_id, $list_id, $schoolyear_id)
{
    $sql = "SELECT sl.submission_count 
            FROM student_list sl
            INNER JOIN subjects s ON sl.subject_id = s.subject_id
            WHERE s.subject_id = ? AND s.teacher_id = ? AND sl.list_id = ? AND s.schoolyear_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $subject_id, $teacher_id, $list_id, $schoolyear_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row ? $row['submission_count'] : 0;
}

/**
 * Check if teacher has pending approval request
 */
function hasPendingRequest($conn, $teacher_id, $subject_id, $list_id, $schoolyear_id)
{
    $sql = "SELECT request_id FROM approval_requests 
            WHERE teacher_id = ? AND subject_id = ? AND list_id = ? AND schoolyear_id = ? AND status = 'pending'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $teacher_id, $subject_id, $list_id, $schoolyear_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $has_pending = $result->num_rows > 0;
    $stmt->close();

    return $has_pending;
}

/**
 * Request approval from admin
 */
function requestApproval($conn, $teacher_id, $subject_id, $schoolyear_id, $list_id, $reason)
{
    // Check if already has pending request
    if (hasPendingRequest($conn, $teacher_id, $subject_id, $schoolyear_id, $list_id)) {
        return [
            'success' => false,
            'message' => 'You already have a pending approval request for this subject'
        ];
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert approval request
        $sql = "INSERT INTO approval_requests (teacher_id, subject_id, schoolyear_id, list_id, reason, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiis", $teacher_id, $subject_id, $schoolyear_id, $list_id, $reason);
        $stmt->execute();
        $stmt->close();

        // Set pending_approval flag
        $sql = "UPDATE student_list sl
                INNER JOIN subjects s ON sl.subject_id = s.subject_id
                SET sl.pending_approval = 1 
                WHERE s.subject_id = ? AND s.teacher_id = ? AND s.schoolyear_id = ? AND sl.list_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $subject_id, $teacher_id, $schoolyear_id, $list_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        return [
            'success' => true,
            'message' => 'Approval request submitted successfully. Waiting for admin response.'
        ];
    } catch (Exception $e) {
        $conn->rollback();
        return [
            'success' => false,
            'message' => 'Error submitting request: ' . $e->getMessage()
        ];
    }
}
