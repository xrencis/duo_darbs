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