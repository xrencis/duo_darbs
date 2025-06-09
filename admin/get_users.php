<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Set proper headers
header('Content-Type: application/json');

// Start session and include database connection
session_start();
require_once __DIR__ . '/../db.php';

try {
    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    // Test database connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get users
    $query = "SELECT username, role FROM users ORDER BY username";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    // Clear any output buffer
    ob_clean();
    
    // Send JSON response
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
    
} catch(Exception $e) {
    // Log the error
    error_log("Error in get_users.php: " . $e->getMessage());
    
    // Clear any output buffer
    ob_clean();
    
    // Send error response
    http_response_code(200); // Change to 200 to ensure JSON is processed
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering and send
ob_end_flush();
?> 