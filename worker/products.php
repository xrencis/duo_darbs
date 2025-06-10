<?php
include '../db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? '';

if ($action === 'fetch') {
    $result = $conn->query('SELECT * FROM products');
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}

if ($action === 'order') {
    $id = $conn->real_escape_string($_POST['id']);
    $quantity = (int)$_POST['quantity'];
    $customer = $conn->real_escape_string($_POST['customer']);
    $address = $conn->real_escape_string($_POST['address']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Validate input
        if ($quantity <= 0) {
            throw new Exception('Daudzumam jābūt lielākam par 0');
        }

        // Check if product exists and has enough quantity
        $query = "SELECT qty FROM products WHERE id = ? FOR UPDATE";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Produkts nav atrasts');
        }

        $product = $result->fetch_assoc();
        if ($product['qty'] < $quantity) {
            throw new Exception('Nepietiekamais produkta daudzums noliktavā');
        }

        // Update product quantity
        $new_qty = $product['qty'] - $quantity;
        $query = "UPDATE products SET qty = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $new_qty, $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Kļūda produkta daudzuma atjaunināšanā');
        }

        // Record the order
        $query = "INSERT INTO orders (product_id, quantity, customer_name, delivery_address, order_date) 
                 VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $id, $quantity, $customer, $address);
        
        if (!$stmt->execute()) {
            throw new Exception('Kļūda pasūtījuma reģistrēšanā');
        }

        // If everything is successful, commit the transaction
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // If any error occurs, rollback the transaction
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

if ($action === 'report') {
    $date_from = $conn->real_escape_string($_POST['date_from']);
    $date_to = $conn->real_escape_string($_POST['date_to']);

    $query = "SELECT o.*, p.name as product_name 
              FROM orders o 
              JOIN products p ON o.product_id = p.id 
              WHERE o.order_date BETWEEN ? AND ? 
              ORDER BY o.order_date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode($data);
    exit();
}
?> 