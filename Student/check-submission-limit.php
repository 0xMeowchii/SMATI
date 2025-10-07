<?php
require_once '../database.php';

function getTodaySubmissionsCount($student_id) {
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

header('Content-Type: application/json');

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $count = getTodaySubmissionsCount($student_id);
    
    echo json_encode(['count' => $count]);
} else {
    echo json_encode(['error' => 'Student ID required']);
}
?>