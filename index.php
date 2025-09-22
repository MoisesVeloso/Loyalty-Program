<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<h2 class="text-2xl font-bold mb-4">Search Customer</h2>

<form method="GET" class="mb-6">
  <div class="flex gap-2">
    <input type="text" name="q" value="<?php echo isset($_GET['q'])?htmlspecialchars($_GET['q']):'';?>" placeholder="Search by name, phone or invoice..." class="input input-bordered flex-1">
    <button class="btn btn-primary">Search</button>
  </div>
</form>

<?php
if (!empty($_GET['q'])) {
  $q_raw = $_GET['q'];
  $q_like = '%' . $q_raw . '%';

  $sql = "SELECT DISTINCT c.* FROM customers c LEFT JOIN transactions t ON c.id = t.customer_id
          WHERE c.name LIKE ? OR c.phone LIKE ? OR t.invoice LIKE ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    echo "<div class='alert alert-error'>Search error.</div>";
  } else {
    $stmt->bind_param('sss', $q_like, $q_like, $q_like);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
      echo "<div class='overflow-x-auto'><table class='table table-zebra w-full'><thead><tr><th>Name</th><th>Phone</th><th>Action</th></tr></thead><tbody>";
      while($row = $res->fetch_assoc()) {
        $id = (int)$row['id'];
        $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8');
        echo "<tr><td>$name</td><td>$phone</td><td><a href='customer_profile.php?id=$id' class='btn btn-sm btn-info mr-2'>View Profile</a><a href='add_transaction.php?id=$id' class='btn btn-sm btn-primary'>➕ Add Transaction</a></td></tr>";
      }
      echo "</tbody></table></div>";
    } else {
      echo "<div class='alert alert-info'>No results found</div>";
    }
    $stmt->close();
  }
}
?>

<?php include 'footer.php'; ?>