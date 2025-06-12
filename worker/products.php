<?php
include '../db.php';
header('Content-Type: application/json');

// Check and add status column if it doesn't exist
$check_column = $conn->query("SHOW COLUMNS FROM orders LIKE 'status'");
if ($check_column->num_rows === 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
}

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

if ($action === 'edit') {
    try {
        // Validate required fields
        $required_fields = ['id', 'name', 'category', 'price', 'firm', 'qty'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Lauks '$field' ir obligāts!");
            }
        }

        // Validate and sanitize inputs
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $firm = filter_var($_POST['firm'], FILTER_SANITIZE_STRING);
        $qty = filter_var($_POST['qty'], FILTER_VALIDATE_INT);

        // Additional validation
        if (!$id) {
            throw new Exception("Nederīgs produkta ID!");
        }
        if (strlen($name) < 2 || strlen($name) > 100) {
            throw new Exception("Nosaukumam jābūt no 2 līdz 100 rakstzīmēm!");
        }
        if (strlen($category) < 2 || strlen($category) > 50) {
            throw new Exception("Kategorijai jābūt no 2 līdz 50 rakstzīmēm!");
        }
        if ($price <= 0) {
            throw new Exception("Cenai jābūt lielākai par 0!");
        }
        if (strlen($firm) < 2 || strlen($firm) > 50) {
            throw new Exception("Firmas ID jābūt no 2 līdz 50 rakstzīmēm!");
        }
        if ($qty < 1) {
            throw new Exception("Daudzumam jābūt pozitīvam skaitlim!");
        }

        // Update product
        $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, firm = ?, qty = ? WHERE id = ?");
        $stmt->bind_param("ssdsii", $name, $category, $price, $firm, $qty, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Kļūda rediģējot produktu datubāzē!");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete') {
    try {
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            throw new Exception("Produkta ID nav norādīts!");
        }

        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception("Nederīgs produkta ID!");
        }

        // Delete product
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Kļūda dzēšot produktu datubāzē!");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'add') {
    try {
        // Validate required fields
        $required_fields = ['name', 'category', 'price', 'firm', 'qty'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Lauks '$field' ir obligāts!");
            }
        }

        // Validate and sanitize inputs
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $firm = filter_var($_POST['firm'], FILTER_SANITIZE_STRING);
        $qty = filter_var($_POST['qty'], FILTER_VALIDATE_INT);

        // Additional validation
        if (strlen($name) < 2 || strlen($name) > 100) {
            throw new Exception("Nosaukumam jābūt no 2 līdz 100 rakstzīmēm!");
        }
        if (strlen($category) < 2 || strlen($category) > 50) {
            throw new Exception("Kategorijai jābūt no 2 līdz 50 rakstzīmēm!");
        }
        if ($price <= 0) {
            throw new Exception("Cenai jābūt lielākai par 0!");
        }
        if (strlen($firm) < 2 || strlen($firm) > 50) {
            throw new Exception("Firmas ID jābūt no 2 līdz 50 rakstzīmēm!");
        }
        if ($qty < 1) {
            throw new Exception("Daudzumam jābūt pozitīvam skaitlim!");
        }

        // Insert new product
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, firm, qty) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $name, $category, $price, $firm, $qty);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Kļūda pievienojot produktu datubāzē!");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'manage_orders') {
    try {
        $date_from = isset($_POST['date_from']) ? $conn->real_escape_string($_POST['date_from']) : null;
        $date_to = isset($_POST['date_to']) ? $conn->real_escape_string($_POST['date_to']) : null;
        $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : null;

        $query = "SELECT o.*, p.name as product_name 
                 FROM orders o 
                 JOIN products p ON o.product_id = p.id 
                 WHERE 1=1";
        $params = [];
        $types = "";

        if ($date_from) {
            $query .= " AND o.order_date >= ?";
            $params[] = $date_from;
            $types .= "s";
        }
        if ($date_to) {
            $query .= " AND o.order_date <= ?";
            $params[] = $date_to;
            $types .= "s";
        }
        if ($status) {
            $query .= " AND o.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $query .= " ORDER BY o.order_date DESC";

        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Kļūda ielādējot pasūtījumus!");
        }

        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        echo json_encode($orders);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_order_status') {
    try {
        if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
            throw new Exception("Trūkst nepieciešamie dati!");
        }

        $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
        $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

        if (!$order_id) {
            throw new Exception("Nederīgs pasūtījuma ID!");
        }

        // Validate status
        $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            throw new Exception("Nederīgs status!");
        }

        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Kļūda atjauninot pasūtījuma statusu!");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_order') {
    try {
        if (!isset($_POST['order_id'])) {
            throw new Exception("Trūkst pasūtījuma ID!");
        }

        $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
        if (!$order_id) {
            throw new Exception("Nederīgs pasūtījuma ID!");
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            // Get order details to restore product quantity
            $stmt = $conn->prepare("SELECT product_id, quantity FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Pasūtījums nav atrasts!");
            }

            $order = $result->fetch_assoc();

            // Restore product quantity if order was not cancelled
            $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $order_status = $result->fetch_assoc()['status'];

            if ($order_status !== 'cancelled') {
                $stmt = $conn->prepare("UPDATE products SET qty = qty + ? WHERE id = ?");
                $stmt->bind_param("ii", $order['quantity'], $order['product_id']);
                if (!$stmt->execute()) {
                    throw new Exception("Kļūda atjauninot produkta daudzumu!");
                }
            }

            // Delete the order
            $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Kļūda dzēšot pasūtījumu!");
            }

            // If everything is successful, commit the transaction
            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // If any error occurs, rollback the transaction
            $conn->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?> 