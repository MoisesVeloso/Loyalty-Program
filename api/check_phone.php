<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
if ($phone === '') {
    echo json_encode(['found'=>false]);
    exit;
}
$stmt = $conn->prepare('SELECT id,name,phone FROM customers WHERE phone = ? LIMIT 1');
$stmt->bind_param('s', $phone);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows>0) {
    $row = $res->fetch_assoc();
    echo json_encode(['found'=>true,'customer'=>$row]);
} else {
    echo json_encode(['found'=>false]);
}
$stmt->close();
?>