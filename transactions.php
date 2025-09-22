<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<h2 class="text-2xl font-bold mb-4">Transactions</h2>
<a href="add_transaction.php" class="btn btn-primary mb-4">➕ Add Transaction</a>

<?php
$sql = "SELECT t.id, t.date, t.invoice, t.answered_by, c.name, c.phone, t.items FROM transactions t LEFT JOIN customers c ON t.customer_id = c.id ORDER BY t.date DESC";
$res = $conn->query($sql);
if ($res && $res->num_rows>0) {
  echo "<div class='overflow-x-auto'><table class='table table-compact w-full'><thead><tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Phone</th><th>Items</th></tr></thead><tbody>";
  while($r = $res->fetch_assoc()) {
    $items_html = '<span class="text-gray-500 italic">No items recorded</span>';
    if (!empty($r['items'])) {
      $items = json_decode($r['items'], true);
      if (is_array($items) && count($items)>0) {
        $parts = array();
        foreach($items as $it) {
          $name = htmlspecialchars($it['name'] ?? $it['model'] ?? 'Unknown');
          $qty = intval($it['qty'] ?? 1);
          $parts[] = $name . ' (x' . $qty . ')';
        }
        $items_html = implode('<br>', $parts);
      }
    }
    echo "<tr class='hover:bg-base-300' ><td>".htmlspecialchars($r['date'])."</td><td>".htmlspecialchars($r['invoice'])."</td><td>".htmlspecialchars($r['name'])."</td><td>".htmlspecialchars($r['phone'])."</td><td>$items_html</td></tr>";
  }
  echo "</tbody></table></div>";
} else {
  echo "<div class='alert alert-info'>No transactions found</div>";
}
?>

<?php include 'footer.php'; ?>