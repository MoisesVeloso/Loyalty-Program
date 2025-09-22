<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // handle address selection
  $address = ($_POST['address_select'] === 'other')
    ? $_POST['address_other']
    : $_POST['address_select'];

  // Basic insert customer
  $stmt = $conn->prepare("INSERT INTO customers (name,address,phone,email,birthday,age,gender,marital_status) VALUES (?,?,?,?,?,?,?,?)");
  $stmt->bind_param(
    'sssssiss',
    $_POST['name'],
    $address,
    $_POST['phone'],
    $_POST['email'],
    $_POST['birthday'],
    $_POST['age'],
    $_POST['gender'],
    $_POST['marital_status']
  );
  $stmt->execute();
  $customer_id = $stmt->insert_id;
  $stmt->close();

  // build items array
  $items_out = array();
  if (isset($_POST['items']) && is_array($_POST['items'])) {
    foreach ($_POST['items'] as $iid => $idata) {
      if (isset($idata['checked'])) {
        $qty = isset($idata['qty']) && is_numeric($idata['qty']) ? intval($idata['qty']) : 1;
        // get item name
        $r = $conn->query("SELECT model FROM items WHERE id=" . intval($iid));
        $row = $r->fetch_assoc();
        $items_out[] = array('id' => intval($iid), 'name' => $row['model'], 'qty' => $qty);
      }
    }
  }
  $items_json = json_encode($items_out);

  // insert transaction
  $tstmt = $conn->prepare("INSERT INTO transactions (customer_id,date,invoice,answered_by,items) VALUES (?,?,?,?,?)");
  $tstmt->bind_param('issss', $customer_id, $_POST['date'], $_POST['invoice'], $_POST['answered_by'], $items_json);
  $tstmt->execute();
  $tstmt->close();

  header('Location: customer_profile.php?id=' . $customer_id);
  exit;
}
?>
