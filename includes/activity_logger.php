<?php

function logActivity($conn, $userId, $userType, $action, $description = null) {
    
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, user_type, action, description, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?,NOW())");
    $stmt->bind_param("isssss", $userId, $userType, $action, $description, $ipAddress, $userAgent);
    $stmt->execute();
    $stmt->close();
}