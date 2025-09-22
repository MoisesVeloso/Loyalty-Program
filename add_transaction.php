<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<?php $cid = isset($_GET['id'])?intval($_GET['id']):0; ?>

<h2 class="text-2xl font-bold mb-4">Add Transaction</h2>

<form method="POST" action="save_transaction.php" class="space-y-4">
  <?php if ($cid): ?>
    <?php $c = $conn->query("SELECT id,name,phone FROM customers WHERE id=$cid")->fetch_assoc(); ?>
    <p><strong>Customer:</strong> <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['phone']) ?>)</p>
    <input type="hidden" name="customer_id" value="<?= $cid ?>">
  <?php else: ?>
    <select name="customer_id" class="select select-bordered w-full" required>
      <option value="">Select Customer</option>
      <?php $cres = $conn->query("SELECT id,name,phone FROM customers ORDER BY name"); while($cx = $cres->fetch_assoc()): ?>
        <option value="<?= $cx['id'] ?>"><?= htmlspecialchars($cx['name']).' ('.htmlspecialchars($cx['phone']).')' ?></option>
      <?php endwhile; ?>
    </select>
  <?php endif; ?>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
    <input name="date" type="date" class="input input-bordered" value="<?php echo date('Y-m-d'); ?>" required>
    <input name="invoice" placeholder="Sales Invoice" class="input input-bordered" required>
    <input name="answered_by" placeholder="Answered By" class="input input-bordered">
  </div>

  <h4 class="mt-4 font-medium">Items Purchased</h4>
  <div class="max-h-64 overflow-y-auto border rounded p-3 bg-base-200">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
      <?php 
      $itres = $conn->query("SELECT id,model FROM items WHERE status=1 ORDER BY model"); 
      while($it = $itres->fetch_assoc()) {
        $id = $it['id'];
        $model = htmlspecialchars($it['model']);
        echo <<<HTML
        <label class="flex items-center gap-2">
          <input type="checkbox" name="items[{$id}][checked]" class="checkbox item-checkbox" data-id="{$id}" require>
          <span class="flex-1">{$model}</span>
          <input type="number" min="1" name="items[{$id}][qty]" class="input input-bordered w-20 item-qty" placeholder="Qty" data-id="{$id}">
        </label>
        HTML;
      } 
      ?>
    </div>
  </div>

  <div class="mt-4">
    <button class="btn btn-primary">Save Transaction</button>
  </div>
</form>

<script>
// Sync checkbox <-> quantity
document.addEventListener('DOMContentLoaded', function() {
  const checkboxes = document.querySelectorAll('.item-checkbox');
  const qtyInputs = document.querySelectorAll('.item-qty');

  // Auto-check when qty entered
  qtyInputs.forEach(qty => {
    qty.addEventListener('input', () => {
      const id = qty.dataset.id;
      const checkbox = document.querySelector('.item-checkbox[data-id="'+id+'"]');
      if (qty.value && Number(qty.value) > 0) {
        checkbox.checked = true;
      } else {
        checkbox.checked = false;
      }
    });
  });

  // Auto-fill qty=1 when checkbox checked
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      const id = checkbox.dataset.id;
      const qty = document.querySelector('.item-qty[data-id="'+id+'"]');
      if (checkbox.checked && (!qty.value || Number(qty.value) <= 0)) {
        qty.value = 1;
      }
      if (!checkbox.checked) {
        qty.value = '';
      }
    });
  });
});
</script>

<?php include 'footer.php'; ?>
