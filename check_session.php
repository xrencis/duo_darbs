<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    session_destroy();
    header('Location: ../login.php');
    exit();
}


$current_page = basename($_SERVER['PHP_SELF']);
$required_role = '';


$current_dir = dirname($_SERVER['PHP_SELF']);
if (strpos($current_dir, '/admin') !== false) {
    $required_role = 'admin';
} elseif (strpos($current_dir, '/worker') !== false) {
    $required_role = 'worker';
} elseif (strpos($current_dir, '/shelver') !== false) {
    $required_role = 'shelver';
}

// Strict role checking
if ($required_role && $_SESSION['role'] !== $required_role) {
    session_destroy();
    header('Location: ../login.php');
    exit();
}

// Additional security: Prevent direct access to PHP files
$allowed_files = ['index.php', 'products.php'];
if (!in_array($current_page, $allowed_files)) {
    header('Location: index.php');
    exit();
}
?> 