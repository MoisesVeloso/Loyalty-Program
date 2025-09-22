<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<?php
// Guard: require id
if (!isset($_GET['id'])) {
    echo '<div class="alert alert-info">No customer selected.</div>';
    include 'footer.php';
    exit;
}

$customer_id = intval($_GET['id']);

// Fetch customer (prepared statement)
$cstmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
if (!$cstmt) {
    echo '<div class="alert alert-error">Database error.</div>';
    include 'footer.php';
    exit;
}
$cstmt->bind_param('i', $customer_id);
$cstmt->execute();
$customer_res = $cstmt->get_result();
$customer = $customer_res->fetch_assoc();
$cstmt->close();

if (!$customer) {
    echo '<div class="alert alert-info">Customer not found.</div>';
    include 'footer.php';
    exit;
}

// Fetch transactions (prepared)
$tstmt = $conn->prepare("SELECT * FROM transactions WHERE customer_id = ? ORDER BY date DESC");
$tstmt->bind_param('i', $customer_id);
$tstmt->execute();
$txs = $tstmt->get_result();
$tstmt->close();

// Count transactions (stamp count)
$count_stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM transactions WHERE customer_id = ?");
$count_stmt->bind_param('i', $customer_id);
$count_stmt->execute();
$count_res = $count_stmt->get_result()->fetch_assoc();
$stamp_count = intval($count_res['cnt'] ?? 0);
$count_stmt->close();
?>

<h2 class="text-2xl font-bold mb-4">Customer Profile</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
  <div>
    <p><strong>Name:</strong> <?= htmlspecialchars($customer['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($customer['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
  </div>
  <div>
    <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($customer['address'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
    <p><strong>Birthday:</strong> <?= htmlspecialchars($customer['birthday'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Age:</strong> <?= htmlspecialchars($customer['age'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
  </div>
</div>

<div class="mt-4">
  <p><strong>Stamp Count:</strong> <?= $stamp_count ?> <?php if ($stamp_count >= 5) echo '<span class="badge badge-accent">🎁 Eligible</span>'; ?></p>
  <a class="btn btn-primary mt-2" href="add_transaction.php?id=<?= $customer_id ?>">➕ Add Transaction</a>
</div>

<h3 class="text-xl font-semibold mt-6">Purchase History</h3>
<div class="overflow-x-auto">
  <table class="table table-zebra w-full">
    <thead><tr><th>Date</th><th>Invoice</th><th>Items</th></tr></thead>
    <tbody>
      <?php while ($t = $txs->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($t['date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($t['invoice'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <ul class="list-disc pl-5">
            <?php
              $items = json_decode($t['items'] ?? '[]', true);
              if (is_array($items) && count($items) > 0) {
                  foreach ($items as $it) {
                      $iname = htmlspecialchars($it['name'] ?? ($it['model'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8');
                      $iqty = intval($it['qty'] ?? 1);
                      echo "<li>{$iname} (x{$iqty})</li>";
                  }
              } else {
                  echo '<li class="text-gray-500 italic">No items recorded</li>';
              }
            ?>
            </ul>
          </td>
          <td><?= htmlspecialchars($t['answered_by'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include 'footer.php'; ?>
