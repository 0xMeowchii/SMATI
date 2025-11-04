<?php

function logActivity($conn, $userId, $userType, $action, $description = null) {

     cleanOldActivityLogs($conn);
    
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, user_type, action, description, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?,NOW())");
    $stmt->bind_param("isssss", $userId, $userType, $action, $description, $ipAddress, $userAgent);
    $stmt->execute();
    $stmt->close();
}

function cleanOldActivityLogs($conn) {
    // Delete logs older than 2 months
    // Using a random check to avoid running this on every insert (performance optimization)
    // Only runs approximately 1% of the time
    if (rand(1, 100) === 1) {
        $stmt = $conn->prepare("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 MONTH)");
        $stmt->execute();
        $stmt->close();
    }
}
?>