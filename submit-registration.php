<?php
require_once 'database.php';

header('Content-Type: application/json');

function checkExistingRegistration($email, $ipAddress)
{
    $conn = connectToDB();
    
    if (!$conn) {
        return false; // If DB connection fails, allow registration to proceed
    }

    try {
        // Check if user with same email or IP address registered within last 24 hours
        $sql = "SELECT COUNT(*) as count FROM registrations 
                WHERE (email = ? OR ip_address = ?) 
                AND reg_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("ss", $email, $ipAddress);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        $conn->close();
        
        return $row['count'] > 0;
    } catch (Exception $e) {
        return false;
    }
}

function submitRegistration($data)
{
    $conn = connectToDB();
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }

    try {
        // Check for existing registration first
        if (checkExistingRegistration($data['email'], $ipAddress)) {
            return [
                'success' => false, 
                'message' => 'You have already registered recently. Please wait 24 hours before submitting another registration.'
            ];
        }

        // Prepare the SQL statement
        $sql = "INSERT INTO registrations (
                    reg_number, 
                    firstname, 
                    lastname, 
                    birthdate, 
                    gender, 
                    email, 
                    phone, 
                    address, 
                    school_visit, 
                    program,
                    program_details,
                    ip_address,
                    reg_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "ssssssssssss",
            $data['reg_number'],
            $data['firstname'],
            $data['lastname'],
            $data['birthdate'],
            $data['gender'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['school_visit'],
            $data['program'],
            $data['program_details'],
            $ipAddress
        );

        // Execute the statement
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Registration submitted successfully'];
        } else {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }
    } catch (Exception $e) {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
        return ['success' => false, 'message' => 'Error submitting registration: ' . $e->getMessage()];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    // Validate required fields
    $required_fields = [
        'reg_number',
        'firstname',
        'lastname',
        'birthdate',
        'gender',
        'email',
        'phone',
        'address',
        'school_visit',
        'program',
        'program_details'
    ];

    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            exit;
        }
    }

    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    // Submit the registration
    $result = submitRegistration($input);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>