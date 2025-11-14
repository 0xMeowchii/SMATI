<?php
require_once 'database.php';

header('Content-Type: application/json');

function checkDuplicateField($field, $value)
{
    $conn = connectToDB();
    
    if (!$conn) {
        return ['exists' => false, 'error' => 'Database connection failed'];
    }

    try {
        // Validate field name to prevent SQL injection
        $allowed_fields = ['email', 'firstname', 'lastname', 'phone'];
        
        if (!in_array($field, $allowed_fields)) {
            return ['exists' => false, 'error' => 'Invalid field name'];
        }

        // Prepare SQL statement
        $sql = "SELECT COUNT(*) as count FROM registrations WHERE $field = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $exists = $row['count'] > 0;
        
        $stmt->close();
        $conn->close();
        
        return ['exists' => $exists];
        
    } catch (Exception $e) {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
        return ['exists' => false, 'error' => $e->getMessage()];
    }
}

function checkMultipleFields($data)
{
    $conn = connectToDB();
    
    if (!$conn) {
        return ['exists' => false, 'error' => 'Database connection failed'];
    }

    try {
        // Check for exact match of firstname, lastname, and phone
        $sql = "SELECT COUNT(*) as count FROM registrations 
                WHERE firstname = ? AND lastname = ? AND phone = ?";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $data['firstname'], $data['lastname'], $data['phone']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $exists = $row['count'] > 0;
        
        $stmt->close();
        $conn->close();
        
        return ['exists' => $exists, 'message' => 'A registration with this name and phone number already exists'];
        
    } catch (Exception $e) {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
        return ['exists' => false, 'error' => $e->getMessage()];
    }
}

// Handle GET and POST requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Single field check via GET
    if (isset($_GET['field']) && isset($_GET['value'])) {
        $field = $_GET['field'];
        $value = trim($_GET['value']);
        
        if (empty($value)) {
            echo json_encode(['exists' => false]);
            exit;
        }
        
        $result = checkDuplicateField($field, $value);
        echo json_encode($result);
    } else {
        echo json_encode(['exists' => false, 'error' => 'Missing parameters']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Multiple fields check via POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['exists' => false, 'error' => 'Invalid input data']);
        exit;
    }
    
    // Check single field
    if (isset($input['field']) && isset($input['value'])) {
        $result = checkDuplicateField($input['field'], trim($input['value']));
        echo json_encode($result);
    }
    // Check multiple fields (firstname + lastname + phone)
    elseif (isset($input['firstname']) && isset($input['lastname']) && isset($input['phone'])) {
        $result = checkMultipleFields($input);
        echo json_encode($result);
    } else {
        echo json_encode(['exists' => false, 'error' => 'Missing required fields']);
    }
    
} else {
    echo json_encode(['exists' => false, 'error' => 'Invalid request method']);
}
?>