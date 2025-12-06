<?php

/**
 * Start a unique session for a specific user
 * Call this function after user authentication
 * 
 * @param string $userType - Type of user (e.g., 'admin', 'student', 'teacher', 'registrar', 'superuser')
 * @param int $userId - Unique user ID
 */
function startUniqueSession($userType, $userId)
{
    // Close any existing session
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    // **DELETE OLD COOKIES BEFORE STARTING NEW SESSION**
    cleanupOldSessions();

    // Create a unique session name based on user type and ID
    $uniqueSessionName = $userType . '_' . $userId;
    session_name($uniqueSessionName);

    // Start the session
    session_start();

    // Store user identity in session if not already set
    if (!isset($_SESSION['id'])) {
        $_SESSION['id'] = $userId;
        $_SESSION['user_type'] = $userType;
        $_SESSION['session_token'] = bin2hex(random_bytes(32));
        $_SESSION['logged_in'] = true;
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
function validateSession($userType, $userId)
{
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
function initGuestSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('guest_session');
        session_start();
    }
}

/**
 * Clean up old session cookies
 */
function cleanupOldSessions()
{
    $cookiesToDelete = ['guest_session', 'PHPSESSID'];
    
    foreach ($cookiesToDelete as $cookieName) {
        if (isset($_COOKIE[$cookieName])) {
            setcookie($cookieName, '', time() - 3600, '/');
            unset($_COOKIE[$cookieName]);
        }
    }
}

/**
 * Check if a session exists for a specific user type (without redirecting)
 * Useful for combined login pages that need to check multiple user types
 * 
 * @param string $userType - The user type to check (e.g., 'student', 'teacher')
 * @return bool - True if valid session exists for this user type
 */
function checkExistingSession($userType)
{
    // Check cookies for session pattern matching this user type
    foreach ($_COOKIE as $cookieName => $cookieValue) {
        if (preg_match('/^' . preg_quote($userType, '/') . '_(\d+)$/', $cookieName, $matches)) {
            $userId = $matches[1];
            
            // Save current session state
            $currentSessionId = session_id();
            
            // Try to resume this session
            session_name($cookieName);
            session_start();
            
            // Verify session is valid
            if (isset($_SESSION['id']) && 
                isset($_SESSION['user_type']) && 
                isset($_SESSION['logged_in']) && 
                $_SESSION['logged_in'] === true &&
                validateSession($userType, $userId)) {
                
                // Valid session found
                return true;
            } else {
                // Invalid session, clean it up
                session_destroy();
                setcookie($cookieName, '', time() - 3600, '/');
            }
        }
    }
    
    return false;
}

/**
 * Check for existing session cookies and redirect if found
 * Only redirects if user tries to access their OWN login page
 * Allows users to access different login pages even if logged in elsewhere
 * 
 * @param string $currentUserType - The user type expected for this login page (e.g., 'admin', 'registrar', 'superuser')
 * @param string $dashboardPath - The path to redirect to if session exists (e.g., 'admin-dashboard.php')
 */
function checkExistingSessionAndRedirect($currentUserType, $dashboardPath)
{
    // Check all cookies for session patterns
    foreach ($_COOKIE as $cookieName => $cookieValue) {
        // Check if cookie matches the CURRENT login page's user type pattern
        if (preg_match('/^' . preg_quote($currentUserType, '/') . '_(\d+)$/', $cookieName, $matches)) {
            $userId = $matches[1];
            
            // Try to resume this session
            session_name($cookieName);
            session_start();
            
            // Verify session is valid and has required data
            if (isset($_SESSION['id']) && 
                isset($_SESSION['user_type']) && 
                isset($_SESSION['logged_in']) && 
                $_SESSION['logged_in'] === true) {
                
                // Validate session integrity
                if (validateSession($currentUserType, $userId)) {
                    // Valid session found for THIS user type!
                    // Redirect to their dashboard
                    header("Location: " . $dashboardPath);
                    exit();
                } else {
                    // Invalid session data, destroy it
                    session_destroy();
                    setcookie($cookieName, '', time() - 3600, '/');
                }
            } else {
                // Incomplete session data, destroy it
                session_destroy();
                setcookie($cookieName, '', time() - 3600, '/');
            }
        }
    }
    
    // No valid session found for this user type, initialize guest session
    initGuestSession();
}

/**
 * Get the dashboard path for a specific user type
 * Customize these paths according to your directory structure
 * 
 * @param string $userType - The user type
 * @return string|null - The dashboard path or null if not found
 */
function getDashboardPath($userType)
{
    $dashboardPaths = [
        'admin' => './Admin/admin-dashboard.php',
        'student' => './Student/student-dashboard.php',
        'teacher' => './Teacher/teacher-dashboard.php',
        'registrar' => './registrar/registrar-dashboard.php',
        'superuser' => './Super User/admin-list.php'
    ];
    
    return $dashboardPaths[$userType] ?? null;
}

// Set timezone
date_default_timezone_set('Asia/Manila');

?>