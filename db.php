<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'stash';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Savienojuma kļūda: ' . $conn->connect_error);
}
?> 