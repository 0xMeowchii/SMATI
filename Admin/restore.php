<?php
require_once '../session.php';
require_once '../database.php';
include '../includes/activity_logger.php';

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

// Check if user is admin
if (!isset($_SESSION['id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

function sendError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
    sendError('No file uploaded or upload error: ' . $_FILES['backup_file']['error']);
}

$uploaded_file = $_FILES['backup_file']['tmp_name'];
$file_name = $_FILES['backup_file']['name'];

// Validate file
if (!file_exists($uploaded_file)) {
    sendError('Uploaded file not found');
}

// Validate file extension
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
if ($file_ext !== 'sql') {
    sendError('Invalid file type. Only .sql files allowed');
}

// Check file size
if (filesize($uploaded_file) == 0) {
    sendError('Uploaded file is empty');
}

// Read and validate file content
$sql_content = file_get_contents($uploaded_file);
if (empty($sql_content)) {
    sendError('Could not read backup file content');
}

// Check if this is a valid SQL backup (not mysqldump error output)
if (strpos($sql_content, 'mysqldump:') !== false || 
    strpos($sql_content, 'Deprecated program name') !== false) {
    sendError('Invalid backup file. This appears to be a mysqldump error message, not a valid SQL backup.');
}

// Check for SQL signature
if (strpos($sql_content, 'MySQL dump') === false && 
    strpos($sql_content, 'CREATE TABLE') === false &&
    strpos($sql_content, 'INSERT INTO') === false) {
    sendError('File does not appear to be a valid SQL backup');
}

try {
    $conn = connectToDB();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Create safety backup directory
    $backup_dir = '../backups/';
    if (!file_exists($backup_dir)) {
        if (!mkdir($backup_dir, 0755, true)) {
            throw new Exception('Cannot create backup directory');
        }
    }

    // Create safety backup filename
    $safety_backup = $backup_dir . 'pre_restore_backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Create safety backup using PHP method
    $safety_content = "-- Safety Backup Before Restore\n";
    $safety_content .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $safety_content .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Get all tables
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    if (!$result) {
        throw new Exception('Failed to get tables list: ' . $conn->error);
    }
    
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    // Backup each table
    foreach ($tables as $table) {
        // Skip if table doesn't exist (shouldn't happen, but just in case)
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check->num_rows == 0) continue;

        // Drop table
        $safety_content .= "DROP TABLE IF EXISTS `$table`;\n";

        // Create table
        $result = $conn->query("SHOW CREATE TABLE `$table`");
        if (!$result) {
            throw new Exception('Failed to get table structure for: ' . $table);
        }
        $row = $result->fetch_row();
        $safety_content .= $row[1] . ";\n\n";

        // Table data
        $result = $conn->query("SELECT * FROM `$table`");
        $numFields = $result->field_count;

        if ($result->num_rows > 0) {
            $safety_content .= "INSERT INTO `$table` VALUES\n";
            
            $first = true;
            while ($row = $result->fetch_row()) {
                if (!$first) {
                    $safety_content .= ",\n";
                }
                $first = false;
                
                $safety_content .= "(";
                for ($i = 0; $i < $numFields; $i++) {
                    if (isset($row[$i]) && $row[$i] !== null) {
                        $value = str_replace("'", "''", $row[$i]);
                        $value = str_replace("\\", "\\\\", $value);
                        $value = str_replace("\n", "\\n", $value);
                        $value = str_replace("\r", "\\r", $value);
                        $safety_content .= "'" . $value . "'";
                    } else {
                        $safety_content .= "NULL";
                    }
                    
                    if ($i < ($numFields - 1)) {
                        $safety_content .= ",";
                    }
                }
                $safety_content .= ")";
            }
            $safety_content .= ";\n\n";
        }
    }

    $safety_content .= "SET FOREIGN_KEY_CHECKS=1;\n";

    // Save safety backup
    if (file_put_contents($safety_backup, $safety_content) === false) {
        throw new Exception('Failed to create safety backup file');
    }

    // Now restore from the uploaded backup file
    // Split SQL content into individual queries
    $queries = [];
    $current_query = '';
    
    // Split by semicolons, but be careful about semicolons in strings
    $lines = explode("\n", $sql_content);
    
    foreach ($lines as $line) {
        // Skip comments and empty lines
        $trimmed_line = trim($line);
        if (empty($trimmed_line) || strpos($trimmed_line, '--') === 0) {
            continue;
        }
        
        $current_query .= $line . "\n";
        
        // If line ends with semicolon, we have a complete query
        if (substr(trim($line), -1) === ';') {
            $queries[] = trim($current_query);
            $current_query = '';
        }
    }
    
    // Add any remaining query
    if (!empty(trim($current_query))) {
        $queries[] = trim($current_query);
    }

    // Disable foreign key checks for restore
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    
    // Execute each query individually for better error handling
    $executed_queries = 0;
    $total_queries = count($queries);
    
    foreach ($queries as $query) {
        $query = trim($query);
        
        // Skip empty queries
        if (empty($query)) {
            continue;
        }
        
        // Skip comments
        if (strpos($query, '--') === 0) {
            continue;
        }
        
        // Execute query
        if ($conn->query($query) === false) {
            // If it's a DROP TABLE error and the table doesn't exist, it's okay
            if (strpos($query, 'DROP TABLE') === 0) {
                // Continue with next query
                continue;
            } else {
                throw new Exception('Query failed: ' . $conn->error . ' | Query: ' . substr($query, 0, 100));
            }
        }
        
        $executed_queries++;
    }
    
    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    $conn->close();

    // Log the successful restore
    $conn = connectToDB();
    if ($conn) {
        logActivity($conn, $_SESSION['id'], $_SESSION['user_type'], 'RESTORE_DATABASE', 
            "Restored database from backup: $file_name. Executed $executed_queries/$total_queries queries. Safety backup: " . basename($safety_backup));
        $conn->close();
    }

    echo json_encode([
        'success' => true,
        'message' => "Database restored successfully! Executed $executed_queries queries. Safety backup created: " . basename($safety_backup)
    ]);
    exit;

} catch (Exception $e) {
    // Try to re-enable foreign key checks even on error
    if (isset($conn) && $conn) {
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        $conn->close();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Restore failed: ' . $e->getMessage() . '. Safety backup saved as: ' . basename($safety_backup)
    ]);
    exit;
}
?>