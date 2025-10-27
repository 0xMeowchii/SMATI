<?php
require_once 'includes/session.php';
require_once '../database.php';
include '../includes/activity_logger.php';

// Check if user is admin
if (!isset($_SESSION['id']) || $_SESSION['user_type'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['success' => false, 'message' => 'No file uploaded or upload error']));
}

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dbsmati';

$uploaded_file = $_FILES['backup_file']['tmp_name'];
$file_name = $_FILES['backup_file']['name'];

// Validate file extension
$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
if ($file_ext !== 'sql') {
    die(json_encode(['success' => false, 'message' => 'Invalid file type. Only .sql files allowed']));
}

// Create a safety backup before restore
$backup_dir = '../backups/';
$safety_backup = $backup_dir . 'pre_restore_backup_' . date('Y-m-d_H-i-s') . '.sql';

// Path to mysqldump
$mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$mysql_path = 'C:\\xampp\\mysql\\bin\\mysql.exe';

// Create safety backup
$backup_command = sprintf(
    '"%s" --user=%s --host=%s %s > %s 2>&1',
    $mysqldump_path,
    escapeshellarg($db_user),
    escapeshellarg($db_host),
    escapeshellarg($db_name),
    escapeshellarg($safety_backup)
);

exec($backup_command, $backup_output, $backup_return);

if ($backup_return !== 0) {
    die(json_encode([
        'success' => false,
        'message' => 'Failed to create safety backup before restore'
    ]));
}

// Restore database
$restore_command = sprintf(
    '"%s" --user=%s --host=%s %s < %s 2>&1',
    $mysql_path,
    escapeshellarg($db_user),
    escapeshellarg($db_host),
    escapeshellarg($db_name),
    escapeshellarg($uploaded_file)
);

exec($restore_command, $restore_output, $restore_return);

if ($restore_return === 0) {
    // Log the restore
    $conn = connectToDB();
    if ($conn) {
        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'RESTORE_DATABASE', "Restored database from backup file: $file_name");
        $conn->close();
    }
    
    $log_file = $backup_dir . 'backup_log.txt';
    $log_entry = date('Y-m-d H:i:s') . " - Database restored by user ID: " . $_SESSION['id'] . " - File: " . $file_name . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'message' => 'Database restored successfully! A safety backup was created: ' . basename($safety_backup)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Restore failed. Your data is safe - a backup was created at: ' . basename($safety_backup),
        'error' => implode("\n", $restore_output)
    ]);
}
?>