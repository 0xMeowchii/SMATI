<?php
include '../database.php';
require_once '../session.php';

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

// Check if user is authenticated as admin
if (!isset($_SESSION['id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_number'])) {
    $regNumber = trim($_POST['reg_number']);

    if (empty($regNumber)) {
        echo json_encode(['success' => false, 'message' => 'Registration number is required']);
        exit;
    }

    try {
        $conn = connectToDB();

        // Check if registration exists
        $checkSql = "SELECT * FROM registrations WHERE reg_number = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $regNumber);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Registration not found']);
            exit;
        }

        // Get registration data for logging
        $regData = $result->fetch_assoc();

        // Delete the registration
        $deleteSql = "DELETE FROM registrations WHERE reg_number = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("s", $regNumber);

        if ($deleteStmt->execute()) {

            echo json_encode(['success' => true, 'message' => 'Registration deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete registration']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
