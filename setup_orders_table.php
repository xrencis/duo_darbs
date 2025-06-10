<?php
$db = new mysqli('localhost', 'root', '', 'stash');

if ($db->connect_error) {
    die('Datubāzes kļūda: ' . $db->connect_error);
}

// Create orders table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    delivery_address TEXT NOT NULL,
    order_date DATETIME NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

if ($db->query($query)) {
    echo "Orders table created or already exists successfully\n";
} else {
    echo "Error creating orders table: " . $db->error . "\n";
}

$db->close();
?> 