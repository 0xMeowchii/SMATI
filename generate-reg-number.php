<?php
require_once '../database.php';

header('Content-Type: application/json');

function generateRegisterNumber() {
    $conn = connectToDB();
    
    if (!$conn) {
        return "SMATIReg" . date('Y') . "-001";
    }

    $year = date('Y');

    try {
        // First, check if the table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'registrations'");
        if ($tableCheck->num_rows == 0) {
            // Table doesn't exist, return fallback number
            return "SMATIReg" . $year . "-001";
        }

        // Count total registrations for this year to generate sequential number
        $sql = "SELECT COUNT(*) as count FROM registrations WHERE YEAR(reg_date) = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param("s", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $count = $row['count'] + 1;
        $registerNumber = "SMATIReg" . $year . "-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        $stmt->close();
        $conn->close();

        return $registerNumber;
    } catch (Exception $e) {
        // Fallback generation if any error occurs
        $timestamp = time() % 1000;
        return "SMATIReg" . $year . "-" . str_pad($timestamp, 3, '0', STR_PAD_LEFT);
    }
}

$registerNumber = generateRegisterNumber();
echo json_encode(['registerNumber' => $registerNumber]);
?>