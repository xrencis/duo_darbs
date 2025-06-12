<?php
include '../db.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['username'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit();
}

$username = $conn->real_escape_string($data['username']);

$query = "SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin'";
$result = $conn->query($query);
$admin_count = $result->fetch_assoc()['admin_count'];

$query = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] === 'admin' && $admin_count <= 1) {
    echo json_encode(['success' => false, 'message' => 'Nevar dzēst pēdējo administratoru']);
    exit();
}

$query = "DELETE FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Lietotājs veiksmīgi dzēsts']);
} else {
    echo json_encode(['success' => false, 'message' => 'Kļūda dzēšot lietotāju']);
}

$stmt->close();
$conn->close();
?> 