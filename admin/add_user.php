<?php
include '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role']);

    // Validate input
    if (empty($username) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Visiem laukiem jābūt aizpildītiem']);
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

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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