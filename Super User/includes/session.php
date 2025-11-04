<?php

/**
 * Start a unique session for a specific user
 * Call this function after user authentication
 * 
 * @param string $userType - Type of user (e.g., 'student', 'teacher')
 * @param int $userId - Unique user ID (student_id or teacher_id)
 */
function startUniqueSession($userType, $userId) {
    // Close any existing session
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    
    // Create a unique session name based on user type and ID
    $uniqueSessionName = $userType . '_' . $userId;
    session_name($uniqueSessionName);
    
    // Start the session
    session_start();
    
    // Add additional security: Store user identity in session
    if (!isset($_SESSION['id'])) {
        $_SESSION['id'] = $userId;
        $_SESSION['user_type'] = $userType;
        $_SESSION['session_token'] = bin2hex(random_bytes(32)); // Extra security token
    }
}

/**
 * Validate that the current session matches the logged-in user
 * Call this on protected pages
 * 
 * @param string $userType - Expected user type
 * @param int $userId - Expected user ID
 * @return bool - True if session is valid
 */
function validateSession($userType, $userId) {
    if (session_status() === PHP_SESSION_NONE) {
        return false;
    }
    
    // Check if session data matches
    if (!isset($_SESSION['id']) || !isset($_SESSION['user_type'])) {
        return false;
    }
    
    if ($_SESSION['id'] != $userId || $_SESSION['user_type'] != $userType) {
        return false;
    }
    
    return true;
}

/**
 * Initialize guest session (for pages before login)
 */
function initGuestSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('guest_session');
        session_start();
    }
}

// Set timezone
date_default_timezone_set('Asia/Manila');

// Initialize guest session for login page
initGuestSession();

?>