<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../db.php';

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    $query = "SELECT username, role FROM users ORDER BY username";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    ob_clean();

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
    
} catch(Exception $e) {
    error_log("Error in get_users.php: " . $e->getMessage());

    ob_clean();

    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

ob_end_flush();
?> 