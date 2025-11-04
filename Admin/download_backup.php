<?php
require_once '../session.php';
require_once '../database.php';

$userSessionFound = false;
foreach ($_COOKIE as $cookieName => $cookieValue) {
    if (preg_match('/^(admin)_\d+$/', $cookieName)) {
        // Found a user session cookie
        session_name($cookieName);
        session_start();
        $userSessionFound = true;
        break;
    }
}

// Check if user is admin
if (!isset($_SESSION['id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    die('Unauthorized access');
}

if (!isset($_GET['file'])) {
    http_response_code(400);
    die('No file specified');
}

$filename = basename($_GET['file']); // Prevent directory traversal
$filepath = '../backups/' . $filename;

// Verify file exists and is a .sql file
if (!file_exists($filepath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'sql') {
    http_response_code(404);
    die('File not found');
}

// Set headers for download
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

// Output file
readfile($filepath);
exit;
?>