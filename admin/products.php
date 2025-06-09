<?php
include '../db.php';
header('Content-Type: application/json');

function validateProductData($name, $category, $price, $firm, $qty) {
    // Check for empty fields
    if (empty($name) || empty($category) || empty($firm)) {
        return ['valid' => false, 'message' => 'Visiem laukiem jābūt aizpildītiem'];
    }

    // Check if name contains only zeros or spaces
    if (preg_match('/^[0\s]+$/', $name)) {
        return ['valid' => false, 'message' => 'Produkta nosaukums nevar saturēt tikai nulles vai atstarpes'];
    }
    
    // Price validation
    if (!is_numeric($price)) {
        return ['valid' => false, 'message' => 'Cenai jābūt skaitlim'];
    }
    
    $price = floatval($price);
    if ($price < 0.01) {
        return ['valid' => false, 'message' => 'Cenai jābūt vismaz 0.01'];
    }
    
    // Quantity validation
    if (!is_numeric($qty)) {
        return ['valid' => false, 'message' => 'Daudzumam jābūt skaitlim'];
    }
    
    $qty = intval($qty);
    if ($qty < 0) {
        return ['valid' => false, 'message' => 'Daudzumam jābūt nenegatīvam skaitlim'];
    }
    
    return ['valid' => true];
}

$action = $_POST['action'] ?? '';

if ($action === 'fetch') {
    $result = $conn->query('SELECT * FROM products');
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}

if ($action === 'delete') {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM products WHERE id=$id");
    echo json_encode(['success'=>true]);
}

if ($action === 'edit') {
    $id = intval($_POST['id']);
    $name = trim($conn->real_escape_string($_POST['name']));
    $cat = trim($conn->real_escape_string($_POST['category']));
    $price = floatval($_POST['price']);
    $firm = trim($conn->real_escape_string($_POST['firm']));
    $qty = intval($_POST['qty']);
    
    $validation = validateProductData($name, $cat, $price, $firm, $qty);
    if (!$validation['valid']) {
        echo json_encode(['success' => false, 'message' => $validation['message']]);
        exit();
    }
    
    $conn->query("UPDATE products SET name='$name', category='$cat', price=$price, firm='$firm', qty=$qty WHERE id=$id");
    echo json_encode(['success'=>true]);
}

if ($action === 'add') {
    $name = trim($conn->real_escape_string($_POST['name']));
    $cat = trim($conn->real_escape_string($_POST['category']));
    $price = floatval($_POST['price']);
    $firm = trim($conn->real_escape_string($_POST['firm']));
    $qty = intval($_POST['qty']);
    
    $validation = validateProductData($name, $cat, $price, $firm, $qty);
    if (!$validation['valid']) {
        echo json_encode(['success' => false, 'message' => $validation['message']]);
        exit();
    }
    
    $conn->query("INSERT INTO products (name, category, price, firm, qty) VALUES ('$name','$cat',$price,'$firm',$qty)");
    echo json_encode(['success'=>true]);
} 