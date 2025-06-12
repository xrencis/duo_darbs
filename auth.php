<?php
session_start();

$db = new mysqli('localhost', 'root', '', 'stash');

if ($db->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Datubāzes kļūda']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $redirect = '';
            switch ($user['role']) {
                case 'admin':
                    $redirect = 'admin/index.php';
                    break;
                case 'worker':
                    $redirect = 'worker/index.php';
                    break;
                case 'shelver':
                    $redirect = 'shelver/index.php';
                    break;
                default:
                    $redirect = 'login.php';
            }

            echo json_encode(['success' => true, 'redirect' => $redirect]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nepareiza parole']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Lietotājs nav atrasts']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Nederīga pieprasījuma metode']);
}

$db->close();
?> 