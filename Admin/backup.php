<?php
require_once 'includes/session.php';
require_once '../database.php';
include '../includes/activity_logger.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dbsmati';

// Create backup directory if it doesn't exist
$backup_dir = '../backups/';
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0750, true);
}

// Generate backup filename with timestamp
$backup_file = $backup_dir . 'dbsmati_backup_' . date('Y-m-d_H-i-s') . '.sql';

// Set mysqldump path
$mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

// Build the command
$command = sprintf(
    '"%s" --user=%s --host=%s --single-transaction --routines --triggers %s > %s 2>&1',
    $mysqldump_path,
    escapeshellarg($db_user),
    escapeshellarg($db_host),
    escapeshellarg($db_name),
    escapeshellarg($backup_file)
);

// Execute and capture output
exec($command, $output, $return_var);

file_put_contents(__DIR__ . '/../backups/debug.txt', date('Y-m-d H:i:s') . " - command: $command\nreturn_var: " . var_export($return_var, true) . "\noutput:\n" . implode("\n", $output) . "\n\n", FILE_APPEND);

if ($return_var === 0 && file_exists($backup_file) && filesize($backup_file) > 0) {
    // Delete backups older than 30 days
    $files = glob($backup_dir . '*.sql');
    foreach ($files as $file) {
        if (filemtime($file) < time() - (30 * 24 * 60 * 60)) {
            unlink($file);
        }
    }

    // Save last backup info to a JSON file
    $last_backup_info = [
        'filename' => basename($backup_file),
        'date' => date('F d, Y'),
        'time' => date('h:i A'),
        'datetime' => date('Y-m-d H:i:s'),
        'size' => round(filesize($backup_file) / 1024, 2) . ' KB',
        'user_id' => $_SESSION['id'],
        'username' => $_SESSION['username']
    ];
    file_put_contents($backup_dir . 'last_backup.json', json_encode($last_backup_info));

    // Log the backup
    $conn = connectToDB();
    if ($conn) {
        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'CREATE_BACKUP', "Created database backup: " . basename($backup_file));
        $conn->close();
    }

    $log_file = $backup_dir . 'backup_log.txt';
    $log_entry = date('Y-m-d H:i:s') . " - Backup created by user ID: " . $_SESSION['id'] . " - File: " . basename($backup_file) . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);

    // CHANGED CODE: return success JSON
    echo json_encode([
        'success' => true,
        'filename' => basename($backup_file),
        'size' => round(filesize($backup_file) / 1024, 2) . ' KB',
        'message' => 'Backup created successfully'
    ]);
    exit;
} else {
    // CHANGED CODE: return failure JSON (success = false)
    echo json_encode([
        'success' => false,
        'message' => 'Backup failed. Please check server configuration.',
        'error' => implode("\n", $output)
    ]);
    exit;
}
