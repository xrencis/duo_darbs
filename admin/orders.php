<?php
include '../db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

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

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("SELECT product_id, quantity FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Pasūtījums nav atrasts!");
            }

            $order = $result->fetch_assoc();

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

            $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Kļūda dzēšot pasūtījumu!");
            }

            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?> 