<?php

/**
 * Get all pending approval requests
 */
function getPendingRequests($conn)
{
    $sql = "SELECT ar.*, 
                   t.firstname as teacher_firstname,
                   t.lastname as teacher_lastname,
                   subj.subject_code,
                   subj.subject,
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
            WHERE ar.status = 'pending'
            ORDER BY ar.created_at DESC";

    $result = $conn->query($sql);
    $requests = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }

    return $requests;
}

/**
 * Approve a request - reset counter to 0
 */
function approveRequest($conn, $request_id, $admin_id)
{
    $conn->begin_transaction();

    try {
        // Get request details
        $sql = "SELECT subject_id, schoolyear_id, list_id FROM approval_requests WHERE request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();
        $stmt->close();

        if (!$request) {
            throw new Exception("Request not found");
        }

        // Update request status
        $sql = "UPDATE approval_requests 
                SET status = 'approved', admin_id = ?, updated_at = CURRENT_TIMESTAMP
                WHERE request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $admin_id, $request_id);
        $stmt->execute();
        $stmt->close();

        // Reset subject submission count and pending flag
        $sql = "UPDATE student_list sl
                INNER JOIN subjects s ON sl.subject_id = s.subject_id
                SET sl.submission_count = 0, sl.pending_approval = 0
                WHERE s.subject_id = ? AND s.schoolyear_id = ? AND sl.list_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $request['subject_id'], $request['schoolyear_id'], $request['list_id']);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        return [
            'success' => true,
            'message' => 'Request approved. Teacher can now submit grades again.'
        ];
    } catch (Exception $e) {
        $conn->rollback();
        return [
            'success' => false,
            'message' => 'Error approving request: ' . $e->getMessage()
        ];
    }
}

/**
 * Reject a request
 */
function rejectRequest($conn, $request_id, $admin_id)
{
    $conn->begin_transaction();

    try {
        // Get request details
        $sql = "SELECT subject_id, schoolyear_id, list_id FROM approval_requests WHERE request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();
        $stmt->close();

        if (!$request) {
            throw new Exception("Request not found");
        }

        // Update request status
        $sql = "UPDATE approval_requests 
                SET status = 'rejected', admin_id = ?, updated_at = CURRENT_TIMESTAMP
                WHERE request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $admin_id, $request_id);
        $stmt->execute();
        $stmt->close();

        // Remove pending flag but keep submission count
        $sql = "UPDATE student_list sl
                INNER JOIN subjects s ON sl.subject_id = s.subject_id
                SET pending_approval = 0
                WHERE s.subject_id = ? AND s.schoolyear_id = ? AND sl.list_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $request['subject_id'], $request['schoolyear_id'], $request['list_id']);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        return [
            'success' => true,
            'message' => 'Request rejected.'
        ];
    } catch (Exception $e) {
        $conn->rollback();
        return [
            'success' => false,
            'message' => 'Error rejecting request: ' . $e->getMessage()
        ];
    }
}

/**
 * Set submission due date for a subject
 */
function setDueDate($conn, $subject_id, $schoolyear_id, $due_date)
{
    $sql = "UPDATE subjects 
            SET submission_due_date = ? 
            WHERE subject_id = ? AND schoolyear_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $due_date, $subject_id, $schoolyear_id);

    if ($stmt->execute()) {
        $stmt->close();
        return [
            'success' => true,
            'message' => 'Due date set successfully'
        ];
    }

    $stmt->close();
    return [
        'success' => false,
        'message' => 'Error setting due date'
    ];
}
