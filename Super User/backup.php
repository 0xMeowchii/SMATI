<?php
require_once 'includes/session.php';
require_once '../database.php';

header('Content-Type: application/json');

// Check if user is admin
if ($_SESSION['user_type'] !== 'superuser') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

function sendError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

try {
    // Create backup directory
    $backup_dir = '../backups/';
    if (!file_exists($backup_dir)) {
        if (!mkdir($backup_dir, 0755, true)) {
            sendError('Cannot create backup directory');
        }
    }

    if (!is_writable($backup_dir)) {
        sendError('Backup directory is not writable');
    }

    // Generate backup filename
    $backup_file = $backup_dir . 'smati_backup_' . date('Y-m-d_H-i-s') . '.sql';

    // Create backup using PHP method only (since shell is disabled)
    $conn = connectToDB();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    $backup_content = "-- MySQL Database Backup\n";
    $backup_content .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $backup_content .= "-- SMATI System Backup\n\n";
    $backup_content .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Get all tables
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    if (!$result) {
        throw new Exception('Failed to get tables: ' . $conn->error);
    }
    
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    foreach ($tables as $table) {
        // Drop table
        $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";

        // Create table
        $result = $conn->query("SHOW CREATE TABLE `$table`");
        if (!$result) {
            throw new Exception('Failed to get create table for: ' . $table);
        }
        $row = $result->fetch_row();
        $backup_content .= $row[1] . ";\n\n";

        // Table data
        $result = $conn->query("SELECT * FROM `$table`");
        $numFields = $result->field_count;

        if ($result->num_rows > 0) {
            $backup_content .= "INSERT INTO `$table` VALUES\n";
            
            $first = true;
            while ($row = $result->fetch_row()) {
                if (!$first) {
                    $backup_content .= ",\n";
                }
                $first = false;
                
                $backup_content .= "(";
                for ($i = 0; $i < $numFields; $i++) {
                    if (isset($row[$i]) && $row[$i] !== null) {
                        $value = str_replace("'", "''", $row[$i]);
                        $value = str_replace("\\", "\\\\", $value);
                        $value = str_replace("\n", "\\n", $value);
                        $value = str_replace("\r", "\\r", $value);
                        $backup_content .= "'" . $value . "'";
                    } else {
                        $backup_content .= "NULL";
                    }
                    
                    if ($i < ($numFields - 1)) {
                        $backup_content .= ",";
                    }
                }
                $backup_content .= ")";
            }
            $backup_content .= ";\n\n";
        }
    }

    $backup_content .= "SET FOREIGN_KEY_CHECKS=1;\n";

    // Save backup file
    if (file_put_contents($backup_file, $backup_content) === false) {
        throw new Exception('Failed to create backup file');
    }

    // Verify backup was created
    if (!file_exists($backup_file) || filesize($backup_file) == 0) {
        throw new Exception('Backup file was not created or is empty');
    }

    // Save backup info
    $last_backup_info = [
        'filename' => basename($backup_file),
        'date' => date('F d, Y'),
        'time' => date('h:i A'),
        'size' => round(filesize($backup_file) / 1024, 2) . ' KB',
        'user_id' => $_SESSION['id'],
        'username' => $_SESSION['username'] ?? 'Admin'
    ];
    
    file_put_contents($backup_dir . 'last_backup.json', json_encode($last_backup_info));

    echo json_encode([
        'success' => true,
        'filename' => basename($backup_file),
        'size' => round(filesize($backup_file) / 1024, 2) . ' KB',
        'message' => 'Backup created successfully'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Backup failed: ' . $e->getMessage()
    ]);
    exit;
}
?>