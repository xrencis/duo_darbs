<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get the current page's role requirement
$current_page = basename($_SERVER['PHP_SELF']);
$required_role = '';

if (strpos($current_page, 'admin') !== false) {
    $required_role = 'admin';
} elseif (strpos($current_page, 'worker') !== false) {
    $required_role = 'worker';
} elseif (strpos($current_page, 'shelver') !== false) {
    $required_role = 'shelver';
}

// Check if user has the required role
if ($required_role && $_SESSION['role'] !== $required_role) {
    header('Location: ../login.php');
    exit();
}
?> 