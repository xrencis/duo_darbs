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
if ($action === 'delete') {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM products WHERE id=$id");
    echo json_encode(['success'=>true]);
}
if ($action === 'edit') {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $cat = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $firm = $conn->real_escape_string($_POST['firm']);
    $qty = intval($_POST['qty']);
    $conn->query("UPDATE products SET name='$name', category='$cat', price=$price, firm='$firm', qty=$qty WHERE id=$id");
    echo json_encode(['success'=>true]);
}
if ($action === 'add') {
    $name = $conn->real_escape_string($_POST['name']);
    $cat = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $firm = $conn->real_escape_string($_POST['firm']);
    $qty = intval($_POST['qty']);
    $conn->query("INSERT INTO products (name, category, price, firm, qty) VALUES ('$name','$cat',$price,'$firm',$qty)");
    echo json_encode(['success'=>true]);
}
if ($action === 'generate_report') {
    try {
        if (!isset($_POST['date_from']) || !isset($_POST['date_to'])) {
            throw new Exception("Trūkst datumu diapazona!");
        }

        $date_from = $_POST['date_from'] . ' 00:00:00';
        $date_to = $_POST['date_to'] . ' 23:59:59';

        // Validate dates
        if (!strtotime($date_from) || !strtotime($date_to)) {
            throw new Exception("Nederīgs datumu formāts!");
        }

        // Get orders with product details
        $query = "
            SELECT 
                o.*,
                p.name as product_name,
                p.price,
                (p.price * o.quantity) as total_cost
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.order_date BETWEEN ? AND ?
            ORDER BY o.order_date DESC
        ";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Kļūda sagatavojot SQL vaicājumu: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $date_from, $date_to);
        if (!$stmt->execute()) {
            throw new Exception("Kļūda izpildot SQL vaicājumu: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Kļūda iegūstot rezultātus: " . $stmt->error);
        }
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        echo json_encode([
            'success' => true,
            'orders' => $orders
        ]);
    } catch (Exception $e) {
        error_log("Report generation error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
} 