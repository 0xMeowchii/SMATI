
 
    <?php

    // logout.php

    include 'session.php';

    // Start the user's unique session to access their data
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
        startUniqueSession($_SESSION['user_type'], $_SESSION['user_id']);
    }

    // Unset all session variables
    $_SESSION = array();

    // Delete the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header('location:/SMATI/Super User/login.php');
    exit();
    ?>