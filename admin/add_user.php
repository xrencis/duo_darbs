<?php
include '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($conn->real_escape_string($_POST['username']));
    $password = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role']);

    // Validate input
    if (empty($username) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Visiem laukiem jābūt aizpildītiem']);
        exit();
    }

    // Username validation
    if (strlen($username) < 3 || strlen($username) > 20) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds jābūt no 3 līdz 20 rakstzīmēm']);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds var saturēt tikai burtus, ciparus un pasvītrojuma zīmi']);
        exit();
    }

    // Check if username contains only numbers
    if (preg_match('/^[0-9]+$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds nevar saturēt tikai ciparus']);
        exit();
    }

    // Password validation
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Parolei jābūt vismaz 6 rakstzīmēm garai']);
        exit();
    }

    if (!preg_match('/[A-Za-z]/', $password)) {
        echo json_encode(['success' => false, 'message' => 'Parolei jāsatur vismaz viens burts']);
        exit();
    }

    if (!preg_match('/[0-9]/', $password)) {
        echo json_encode(['success' => false, 'message' => 'Parolei jāsatur vismaz viens cipars']);
        exit();
    }

    
    if (!in_array($role, ['admin', 'worker', 'shelver'])) {
        echo json_encode(['success' => false, 'message' => 'Nederīgs lietotāja tips']);
        exit();
    }

    // Check if username already exists
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds jau eksistē']);
        exit();
    }

    // Hash password using Argon2id
    $hashed_password = password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);

    // Insert new user
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Lietotājs veiksmīgi pievienots']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kļūda pievienojot lietotāju']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nederīga pieprasījuma metode']);
}
?> 