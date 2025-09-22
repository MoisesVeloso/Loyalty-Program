<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<h2 class="text-2xl font-bold mb-4">Customers</h2>
<a href="add_customer.php" class="btn btn-primary mb-4">➕ Add New Customer</a>

<!-- Search by Invoice Number -->
<form method="GET" class="mb-6">
  <div class="flex gap-2">
    <input 
      type="text" 
      name="invoice" 
      value="<?php echo isset($_GET['invoice']) ? htmlspecialchars($_GET['invoice']) : ''; ?>" 
      placeholder="Search by Invoice Number..." 
      class="input input-bordered flex-1"
    >
    <button class="btn btn-primary">Search</button>
  </div>
</form>

<?php
if (!empty($_GET['invoice'])) {
    $invoice = $conn->real_escape_string($_GET['invoice']);
    $sql = "SELECT c.id, c.name, c.phone, GROUP_CONCAT(t.invoice SEPARATOR ', ') AS invoices, COUNT(t.id) AS purchases 
            FROM customers c
            LEFT JOIN transactions t ON c.id = t.customer_id
            WHERE t.invoice LIKE '%$invoice%'
            GROUP BY c.id ORDER BY c.name";
} else {
    $sql = "SELECT c.id, c.name, c.phone, GROUP_CONCAT(t.invoice SEPARATOR ', ') AS invoices, COUNT(t.id) AS purchases 
            FROM customers c 
            LEFT JOIN transactions t ON c.id = t.customer_id 
            GROUP BY c.id ORDER BY c.name";
}

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    echo "<div class='overflow-x-auto'>
            <table class='table table-zebra w-full'>
              <thead>
                <tr class='bg-base-200'>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>Invoices</th>
                  <th>Purchases</th>
                  <th class='text-center'>Actions</th>
                </tr>
              </thead>
              <tbody>";

    while($r = $res->fetch_assoc()) {
        $id = $r['id'];
        $name = htmlspecialchars($r['name']);
        $phone = htmlspecialchars($r['phone']);
        $invoices = htmlspecialchars($r['invoices'] ?? '—');
        $purchases = $r['purchases'];
        $badge = ($purchases >= 5) ? ' <span class="badge badge-accent ml-2">🎁 Eligible</span>' : '';

        echo "<tr id='customer-row-$id' class='hover:bg-base-300'>
                <td class='font-medium'>$name</td>
                <td>$phone</td>
                <td class='text-sm text-gray-600'>$invoices</td>
                <td>$purchases$badge</td>
                <td class='flex justify-center gap-2'>
                  <a href='customer_profile.php?id=$id' class='btn btn-sm btn-info'>View</a>
                  <a href='add_transaction.php?id=$id' class='btn btn-sm btn-primary'>➕</a>
                  <button type='button' class='btn btn-sm btn-error' onclick='confirmDelete(this, $id)'>🗑️ Delete</button>
                </td>
              </tr>";
    }

    echo "</tbody>
          </table>
          </div>";
} else {
    echo "<div class='alert alert-info'>No customers found</div>";
}
?>

<!-- SweetAlert2 -->
<script src="assets/sweetalert2@11.js"></script>
<script>
function confirmDelete(button, customerId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the customer and all their transactions!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 mr-5 rounded',
            cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete_customer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(customerId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const row = document.getElementById('customer-row-' + customerId);
                    if (row) {
                        row.style.transition = "opacity 0.5s";
                        row.style.opacity = 0;
                        setTimeout(() => row.remove(), 500);
                    }
                    Swal.fire('Deleted!', data.message, 'success');
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'Request failed.', 'error');
            });
        }
    });
}
</script>

<?php include 'footer.php'; ?>
