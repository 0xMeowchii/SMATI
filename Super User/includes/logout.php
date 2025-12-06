
 
    <?php

    $sessionCookiesToDestroy = [];

    // Collect ALL user session cookies (don't break after first match)
    foreach ($_COOKIE as $cookieName => $cookieValue) {
        if (preg_match('/^(superuser)_\d+$/', $cookieName)) {
            $sessionCookiesToDestroy[] = $cookieName;

            // Start each session to destroy it properly
            session_name($cookieName);
            session_start();
            session_unset();
            session_destroy();
        }
    }

    // Clear ALL user session cookies from browser
    foreach ($sessionCookiesToDestroy as $cookieName) {
        setcookie($cookieName, '', time() - 3600, '/');
        unset($_COOKIE[$cookieName]);
    }

    // Redirect to login page
    header("Location: /SMATI/Super User/login.php");
    exit();
