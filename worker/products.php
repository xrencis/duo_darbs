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

    // Validate input
    $errors = [];

    // Product ID validation
    if (empty($id)) {
        $errors[] = 'Produkts nav izvēlēts';
    }

    // Quantity validation
    if ($quantity <= 0) {
        $errors[] = 'Daudzumam jābūt lielākam par 0';
    }

    // Customer name validation
    if (empty($customer)) {
        $errors[] = 'Klienta vārds nevar būt tukšs';
    } elseif (strlen($customer) < 2 || strlen($customer) > 100) {
        $errors[] = 'Klienta vārdam jābūt no 2 līdz 100 rakstzīmēm';
    } elseif (preg_match('/^\d+$/', $customer)) {
        $errors[] = 'Klienta vārds nevar sastāvēt tikai no cipariem';
    }

    // Address validation
    if (empty($address)) {
        $errors[] = 'Piegādes adrese nevar būt tukša';
    } elseif (strlen($address) < 5 || strlen($address) > 500) {
        $errors[] = 'Piegādes adresei jābūt no 5 līdz 500 rakstzīmēm';
    } elseif (preg_match('/^\d+$/', $address)) {
        $errors[] = 'Piegādes adrese nevar sastāvēt tikai no cipariem';
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
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
    try {
        $date_from = $conn->real_escape_string($_POST['date_from']);
        $date_to = $conn->real_escape_string($_POST['date_to']);

        $query = "SELECT o.*, p.name as product_name, p.price, 
                  (o.quantity * p.price) as total_cost
                  FROM orders o 
                  JOIN products p ON o.product_id = p.id 
                  WHERE o.order_date BETWEEN ? AND ? 
                  ORDER BY o.order_date DESC";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }

        $stmt->bind_param("ss", $date_from, $date_to);
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Failed to get result: " . $stmt->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        echo json_encode($data);
    } catch (Exception $e) {
        error_log("Report generation error: " . $e->getMessage());
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit();
}
?> 