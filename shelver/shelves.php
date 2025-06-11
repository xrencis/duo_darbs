<?php
require_once '../db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? '';
if ($action === 'add') {
    $name = $conn->real_escape_string($_POST['shelf_name'] ?? '');
    $capacity = intval($_POST['capacity'] ?? 0);
    if (!$name || $capacity < 1) {
        echo json_encode(['success' => false, 'error' => 'Nepareizi dati!']);
        exit;
    }
    $result = $conn->query("INSERT INTO shelves (name, capacity) VALUES ('$name', $capacity)");
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}
if ($action === 'list') {
    $result = $conn->query('SELECT * FROM shelves');
    $shelves = [];
    while ($row = $result->fetch_assoc()) $shelves[] = $row;
    echo json_encode(['success' => true, 'shelves' => $shelves]);
    exit;
}
if ($action === 'place') {
    $shelf_id = intval($_POST['shelf_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);
    $qty = intval($_POST['qty'] ?? 0);
    if (!$shelf_id || !$product_id || $qty < 1) {
        echo json_encode(['success' => false, 'error' => 'Nepareizi dati!']);
        exit;
    }
    // Plaukta kapacitātes pārbaude
    $shelf = $conn->query("SELECT capacity FROM shelves WHERE id=$shelf_id")->fetch_assoc();
    if (!$shelf) {
        echo json_encode(['success' => false, 'error' => 'Plaukts nav atrasts!']);
        exit;
    }
    $placed = $conn->query("SELECT SUM(qty) as total FROM shelf_products WHERE shelf_id=$shelf_id")->fetch_assoc();
    $already = intval($placed['total'] ?? 0);
    if ($already + $qty > $shelf['capacity']) {
        echo json_encode(['success' => false, 'error' => 'Plaukta kapacitāte pārsniegta!']);
        exit;
    }
    // Produkta daudzuma pārbaude
    $prod = $conn->query("SELECT qty FROM products WHERE id=$product_id")->fetch_assoc();
    if (!$prod || $prod['qty'] < $qty) {
        echo json_encode(['success' => false, 'error' => 'Nepietiek produktu!']);
        exit;
    }
    // Atjauno produktu daudzumu
    $conn->query("UPDATE products SET qty=qty-$qty WHERE id=$product_id");
    // Samazina plaukta kapacitāti
    $conn->query("UPDATE shelves SET capacity=capacity-$qty WHERE id=$shelf_id");
    // Saglabā izvietojumu
    $conn->query("INSERT INTO shelf_products (shelf_id, product_id, qty) VALUES ($shelf_id, $product_id, $qty)");
    echo json_encode(['success' => true]);
    exit;
}
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Nepareizs ID!']);
        exit;
    }
    $conn->query("DELETE FROM shelves WHERE id=$id");
    $conn->query("DELETE FROM shelf_products WHERE shelf_id=$id");
    echo json_encode(['success' => true]);
    exit;
}
if ($action === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $capacity = intval($_POST['capacity'] ?? 0);
    if (!$id || !$name || $capacity < 1) {
        echo json_encode(['success' => false, 'error' => 'Nepareizi dati!']);
        exit;
    }
    $conn->query("UPDATE shelves SET name='$name', capacity=$capacity WHERE id=$id");
    echo json_encode(['success' => true]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Nederīga darbība!']); 