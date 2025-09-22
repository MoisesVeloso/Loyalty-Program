<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

$q = isset($_GET['q']) ? $_GET['q'] : '';
$q_like = '%' . $q . '%';

$sql = "SELECT DISTINCT c.id, c.name, c.phone, c.email FROM customers c LEFT JOIN transactions t ON c.id = t.customer_id WHERE c.name LIKE ? OR c.phone LIKE ? OR t.invoice LIKE ? LIMIT 20";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => $conn->error]);
    exit;
}
$stmt->bind_param('sss', $q_like, $q_like, $q_like);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}
$stmt->close();
echo json_encode($out);
?>