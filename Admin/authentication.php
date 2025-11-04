<?php
require_once '../session.php';
$userSessionFound = false;
foreach ($_COOKIE as $cookieName => $cookieValue) {
    if (preg_match('/^(admin|student|teacher)_\d+$/', $cookieName)) {
        // Found a user session cookie
        session_name($cookieName);
        session_start();
        $userSessionFound = true;
        break;
    }
}
header('Content-Type: application/json');

// Include your database connection
function connectToDB()
{
    // Your database connection code here
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbsmati";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        return false;
    }
    return $conn;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connectToDB();

    if (!$conn) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        exit;
    }

    // Get the form data
    $authKey = $_POST['authKey'] ?? '';
    $authPIN = $_POST['authPIN'] ?? '';
    $authMethod = $_POST['authMethod'] ?? '';

    // Validate inputs
    if (empty($authMethod)) {
        echo json_encode([
            'success' => false,
            'message' => 'Authentication method is required'
        ]);
        exit;
    }

    if ($authMethod === 'password' && empty($authKey)) {
        echo json_encode([
            'success' => false,
            'message' => 'Password is required'
        ]);
        exit;
    }

    if ($authMethod === 'pin' && empty($authPIN)) {
        echo json_encode([
            'success' => false,
            'message' => 'PIN is required'
        ]);
        exit;
    }

    // Perform authentication
    if ($authMethod === 'password') {
        $stmt = $conn->prepare("SELECT * FROM auth WHERE password = ?");
        $stmt->bind_param("s", $authKey);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $_SESSION['authenticated'] = true;
            $_SESSION['auth_method'] = 'password';
            $_SESSION['auth_time'] = time();

            echo json_encode([
                'success' => true,
                'message' => 'Authentication successful'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid password. Please try again.'
            ]);
        }
    } else if ($authMethod === 'pin') {
        $stmt = $conn->prepare("SELECT * FROM auth WHERE pin = ?");
        $stmt->bind_param("s", $authPIN);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $_SESSION['authenticated'] = true;
            $_SESSION['auth_method'] = 'pin';
            $_SESSION['auth_time'] = time();

            echo json_encode([
                'success' => true,
                'message' => 'Authentication successful'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid PIN. Please try again.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid authentication method'
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
