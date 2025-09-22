<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $customer_id = intval($_POST['customer_id']);
  $date = $_POST['date'];
  $invoice = $_POST['invoice'];
  $answered = $_POST['answered_by'];

  $items_out = array();
  if (isset($_POST['items']) && is_array($_POST['items'])) {
    foreach ($_POST['items'] as $iid => $idata) {
      if (isset($idata['checked'])) {
        $qty = isset($idata['qty']) && is_numeric($idata['qty'])? intval($idata['qty']):1;
        $r = $conn->query("SELECT model FROM items WHERE id=".intval($iid));
        $row = $r->fetch_assoc();
        $items_out[] = array('id'=>intval($iid),'name'=>$row['model'],'qty'=>$qty);
      }
    }
  }
  $items_json = json_encode($items_out);

  $stmt = $conn->prepare("INSERT INTO transactions (customer_id,date,invoice,answered_by,items) VALUES (?,?,?,?,?)");
  $stmt->bind_param('issss', $customer_id, $date, $invoice, $answered, $items_json);
  $stmt->execute();
  $stmt->close();

  header('Location: customer_profile.php?id=' . $customer_id);
  exit;
}
?>