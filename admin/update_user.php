<?php
include '../db.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit();
}

$username = $conn->real_escape_string($data['username']);
$newRole = $conn->real_escape_string($data['role']);
$newPassword = $data['password'];

if (!in_array($newRole, ['admin', 'worker', 'shelver'])) {
    echo json_encode(['success' => false, 'message' => 'Nederīgs lietotāja tips']);
    exit();
}

$query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Lietotājs nav atrasts']);
    exit();
}

if (!empty($newPassword)) {
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'Parolei jābūt vismaz 6 rakstzīmēm garai']);
        exit();
    }

    if (!preg_match('/[A-Za-z]/', $newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Parolei jāsatur vismaz viens burts']);
        exit();
    }

    if (!preg_match('/[0-9]/', $newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Parolei jāsatur vismaz viens cipars']);
        exit();
    }

    $hashed_password = password_hash($newPassword, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);

    $query = "UPDATE users SET role = ?, password = ? WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $newRole, $hashed_password, $username);
} else {
    $query = "UPDATE users SET role = ? WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $newRole, $username);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Lietotājs veiksmīgi atjaunināts']);
} else {
    echo json_encode(['success' => false, 'message' => 'Kļūda atjauninot lietotāju']);
}

$stmt->close();
$conn->close();
?> 