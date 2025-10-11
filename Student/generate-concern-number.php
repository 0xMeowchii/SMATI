<?php
require_once '../database.php';

header('Content-Type: application/json');

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    
    function generateConcernNumber($student_id) {
        $conn = connectToDB();
        $year = date('Y');
        
        // Count total concerns for this year to generate sequential number
        $sql = "SELECT COUNT(*) as count FROM concern WHERE YEAR(concern_date) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $count = $row['count'] + 1;
        $concernNumber = "SMATI{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        $stmt->close();
        $conn->close();
        
        return $concernNumber;
    }
    
    $concernNumber = generateConcernNumber($student_id);
    echo json_encode(['concernNumber' => $concernNumber]);
} else {
    echo json_encode(['error' => 'Student ID required']);
}
?>