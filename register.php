<?php
session_start();
header('Content-Type: application/json');

// Database connection
$db = new mysqli('localhost', 'root', '', 'stash');

if ($db->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Datubāzes kļūda']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'Visiem laukiem jābūt aizpildītiem']);
        exit();
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Paroles nesakrīt']);
        exit();
    }

    // Check if username already exists
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
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
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, 'worker')";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        // Get the new user's ID
        $user_id = $db->insert_id;
        
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'worker';

        echo json_encode(['success' => true, 'redirect' => 'worker/index.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kļūda reģistrācijas laikā']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Nederīga pieprasījuma metode']);
}

$db->close();
?> 