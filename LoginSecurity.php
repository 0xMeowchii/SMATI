<?php
class LoginSecurity {
    private $conn;
    private $maxAttempts = 3;
    private $lockoutTime = 180; 
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }
    
    /**
     * Check if user is currently locked out
     * @param string $identifier - Can be username, email, studentID, or teacherID
     * @return array ['locked' => bool, 'remaining_time' => int, 'attempts' => int]
     */
    public function checkLockout($identifier) {
        $ipAddress = $this->getClientIP();
        
        // Clean up old attempts (older than lockout time)
        $this->cleanOldAttempts();
        
        // Normalize identifier (lowercase for emails)
        $identifier = strtolower(trim($identifier));
        
        // Count recent failed attempts
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as attempts, 
                   MAX(attempt_time) as last_attempt 
            FROM login_attempts 
            WHERE username = ? 
            AND ip_address = ? 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");
        
        $stmt->bind_param("ssi", $identifier, $ipAddress, $this->lockoutTime);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $attempts = $result['attempts'];
        $locked = $attempts >= $this->maxAttempts;
        
        $remainingTime = 0;
        if ($locked && $result['last_attempt']) {
            $lastAttempt = strtotime($result['last_attempt']);
            $unlockTime = $lastAttempt + $this->lockoutTime;
            $remainingTime = max(0, $unlockTime - time());
        }
        
        return [
            'locked' => $locked,
            'remaining_time' => $remainingTime,
            'attempts' => $attempts,
            'remaining_attempts' => max(0, $this->maxAttempts - $attempts)
        ];
    }
    
    /**
     * Record a failed login attempt
     * @param string $identifier - Can be username, email, studentID, or teacherID
     */
    public function recordFailedAttempt($identifier) {
        $ipAddress = $this->getClientIP();
        
        // Normalize identifier (lowercase for emails)
        $identifier = strtolower(trim($identifier));
        
        $stmt = $this->conn->prepare("
            INSERT INTO login_attempts (username, ip_address, attempt_time) 
            VALUES (?, ?, NOW())
        ");
        
        $stmt->bind_param("ss", $identifier, $ipAddress);
        $stmt->execute();
    }
    
    /**
     * Clear attempts after successful login
     * @param string $identifier - Can be username, email, studentID, or teacherID
     */
    public function clearAttempts($identifier) {
        $ipAddress = $this->getClientIP();
        
        // Normalize identifier (lowercase for emails)
        $identifier = strtolower(trim($identifier));
        
        $stmt = $this->conn->prepare("
            DELETE FROM login_attempts 
            WHERE username = ? AND ip_address = ?
        ");
        
        $stmt->bind_param("ss", $identifier, $ipAddress);
        $stmt->execute();
    }
    
    /**
     * Clean up old attempts from database
     */
    private function cleanOldAttempts() {
        $this->conn->query("
            DELETE FROM login_attempts 
            WHERE attempt_time < DATE_SUB(NOW(), INTERVAL {$this->lockoutTime} SECOND)
        ");
    }
    
    /**
     * Get client IP address
     * @return string
     */
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
?>