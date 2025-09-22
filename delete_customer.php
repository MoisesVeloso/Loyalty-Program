<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid customer ID.']);
    exit;
}

$customer_id = intval($_POST['id']);

$stmt = $conn->prepare("SELECT name, phone FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Customer not found.']);
    exit;
}
$customer = $result->fetch_assoc();

$del_stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
$del_stmt->bind_param("i", $customer_id);

if ($del_stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Customer deleted successfully.',
        'id' => $customer_id,
        'name' => $customer['name'],
        'phone' => $customer['phone']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting customer.']);
}
