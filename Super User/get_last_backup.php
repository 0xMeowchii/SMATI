<?php
require_once 'includes/session.php';


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'superuser') {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$backup_dir = '../backups/';
$last_backup_file = $backup_dir . 'last_backup.json';

if (file_exists($last_backup_file)) {
    $last_backup = json_decode(file_get_contents($last_backup_file), true);
    
    echo json_encode([
        'success' => true,
        'last_backup' => $last_backup['date'] . ' at ' . $last_backup['time'],
        'details' => $last_backup
    ]);
} else {
    echo json_encode([
        'success' => true,
        'last_backup' => 'No backup yet',
        'details' => null
    ]);
}
?>