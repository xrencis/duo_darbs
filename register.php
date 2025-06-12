<?php
session_start();
header('Content-Type: application/json');


$db = new mysqli('localhost', 'root', '', 'stash');

if ($db->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Datubāzes kļūda']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
        echo json_encode(['success' => false, 'message' => 'Trūkst nepieciešamie lauki']);
        exit();
    }

    $username = trim($db->real_escape_string($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds nevar būt tukšs']);
        exit();
    }

    if (strlen($username) < 3 || strlen($username) > 20) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds jābūt no 3 līdz 20 rakstzīmēm']);
        exit();
    }

    if (!preg_match('/^(?=[^A-Z]*[A-Z])(?=[^0-9]*[0-9])(?=[^_]*_)[A-Za-z0-9_]{3,20}$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārdam jābūt 3-20 simboliem, vismaz 1 lielajam burtam, 1 ciparam un 1 pasvītrojuma zīmei (_). Atļauti tikai burti, cipari un pasvītrojums.']);
        exit();
    }

    if (preg_match('/^[0-9]+$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds nevar saturēt tikai ciparus']);
        exit();
    }

    
    if (empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Parole nevar būt tukša']);
        exit();
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Parolei jābūt vismaz 6 rakstzīmēm garai']);
        exit();
    }

    if (!preg_match('/^(?=(?:[^a-z]*[a-z]){3,})(?=[^A-Z]*[A-Z])(?=(?:[^0-9]*[0-9]){2,})(?=[^-]*-)[A-Za-z0-9-]{3,20}$/', $password)) {
        echo json_encode(['success' => false, 'message' => 'Parolei jābūt 3-20 simboliem, vismaz 3 mazajiem burtiem, 1 lielajam burtam, 2 cipariem un 1 domuzīmei (-). Atļauti tikai burti, cipari un domuzīme (-)']);
        exit();
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Paroles nesakrīt']);
        exit();
    }

    
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Lietotājvārds jau eksistē']);
        exit();
    }

   
    $hashed_password = password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);

    
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, 'worker')";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        
        $user_id = $db->insert_id;
        
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'worker';
        $_SESSION['last_activity'] = time();
        $_SESSION['created_at'] = time();

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