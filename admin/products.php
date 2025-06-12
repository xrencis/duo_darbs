<?php
include '../db.php';
header('Content-Type: application/json');

function validateProductData($name, $category, $price, $firm, $qty) {
    if (empty($name) || empty($category) || empty($firm)) {
        return ['valid' => false, 'message' => 'Visiem laukiem jābūt aizpildītiem'];
    }

    if (preg_match('/^[0\s]+$/', $name)) {
        return ['valid' => false, 'message' => 'Produkta nosaukums nevar saturēt tikai nulles vai atstarpes'];
    }
    
    if (!is_numeric($price)) {
        return ['valid' => false, 'message' => 'Cenai jābūt skaitlim'];
    }

    $price = floatval($price);
    if ($price < 0.01) {
        return ['valid' => false, 'message' => 'Cenai jābūt vismaz 0.01'];
    }
    
    if (!is_numeric($qty)) {
        return ['valid' => false, 'message' => 'Daudzumam jābūt skaitlim'];
    }
    
    $qty = intval($qty);
    if ($qty < 1) {
        return ['valid' => false, 'message' => 'Daudzumam jābūt pozitīvam skaitlim'];
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
    try {
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            throw new Exception("Produkta ID nav norādīts!");
        }

        $id = intval($_POST['id']);
        if (!$id) {
            throw new Exception("Nederīgs produkta ID!");
        }

        $check = $conn->query("SELECT id FROM products WHERE id = $id");
        if ($check->num_rows === 0) {
            throw new Exception("Produkts nav atrasts!");
        }

        $orders_check = $conn->query("SELECT COUNT(*) as order_count FROM orders WHERE product_id = $id");
        $shelf_check = $conn->query("SELECT COUNT(*) as shelf_count FROM shelf_products WHERE product_id = $id");
        $order_count = $orders_check->fetch_assoc()['order_count'];
        $shelf_count = $shelf_check->fetch_assoc()['shelf_count'];
        
        if ($order_count > 0 || $shelf_count > 0) {
            if (!isset($_POST['force']) || $_POST['force'] !== 'true') {
                $error_msg = [];
                if ($order_count > 0) $error_msg[] = "pasūtījumi";
                if ($shelf_count > 0) $error_msg[] = "plauktu ieraksti";
                throw new Exception("Nevar dzēst produktu, jo tam ir saistīti " . implode(" un ", $error_msg) . "!");
            }
            
            if ($order_count > 0) {
                if (!$conn->query("DELETE FROM orders WHERE product_id = $id")) {
                    throw new Exception("Kļūda dzēšot saistītos pasūtījumus!");
                }
            }
            if ($shelf_count > 0) {
                if (!$conn->query("DELETE FROM shelf_products WHERE product_id = $id")) {
                    throw new Exception("Kļūda dzēšot saistītos plauktu ierakstus!");
                }
            }
        }

        if (!$conn->query("DELETE FROM products WHERE id = $id")) {
            throw new Exception("Kļūda dzēšot produktu datubāzē!");
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
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