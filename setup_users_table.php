<?php
$db = new mysqli('localhost', 'root', '', 'stash');

if ($db->connect_error) {
    die('Datubāzes kļūda: ' . $db->connect_error);
}

// Create users table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'worker', 'shelver') NOT NULL DEFAULT 'worker'
)";

if ($db->query($query)) {
    echo "Users table created or already exists successfully\n";
} else {
    echo "Error creating users table: " . $db->error . "\n";
}

$db->close();
?> 