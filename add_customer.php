<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<h2 class="text-2xl font-bold mb-4">Add New Customer + First Transaction</h2>

<div id="msg"></div>

<form id="customerForm" method="POST" action="save_customer.php" class="space-y-4">
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <input name="name" class="input input-bordered" placeholder="Full Name" required>
    <input name="phone" id="phone" class="input input-bordered" placeholder="Contact Number" required maxlength="11"  oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
    <input name="email" class="input input-bordered" placeholder="Email"> 
    <select name="address_select" id="address_select" class="select select-bordered" onchange="toggleOtherAddress()" required>
      <option value="">-- Select City --</option>
      <option>Angono</option>
      <option>Antipolo</option>
      <option>Bacoor</option>
      <option>Biñan</option>
      <option>Binangonan</option>
      <option>Bocaue</option>
      <option>Cabuyao</option>
      <option>Cainta</option>
      <option>Caloocan</option>
      <option>Cavite City</option>
      <option>Dasmariñas</option>
      <option>General Trias</option>
      <option>Imus</option>
      <option>Kawit</option>
      <option>Las Piñas</option>
      <option>Makati</option>
      <option>Malabon</option>
      <option>Malolos</option>
      <option>Mandaluyong</option>
      <option>Manila</option>
      <option>Marikina</option>
      <option>Marilao</option>
      <option>Meycauayan</option>
      <option>Muntinlupa</option>
      <option>Navotas</option>
      <option>Parañaque</option>
      <option>Pasay</option>
      <option>Pasig</option>
      <option>Quezon City</option>
      <option>Rodriguez (Montalban)</option>
      <option>San Jose del Monte</option>
      <option>San Juan</option>
      <option>San Mateo</option>
      <option>San Pedro</option>
      <option>Santa Rosa</option>
      <option>Silang</option>
      <option>Taguig</option>
      <option>Taytay</option>
      <option>Trece Martires</option>
      <option>Valenzuela</option>
      <option value="other">Others</option>
    </select>

    <input type="text" name="address_other" id="address_other" placeholder="Enter City" 
           class="input input-bordered mt-2 hidden" />

    <input name="birthday" type="date" class="input input-bordered">
    <input name="age" type="number" class="input input-bordered" placeholder="Age">
    <select name="gender" class="select select-bordered" required>
      <option value="">Gender</option>
      <option>Male</option>
      <option>Female</option>
      <option>Non-Binary</option>
      <option>Prefer not to say</option>
    </select>
    <select name="marital_status" class="select select-bordered" required>
      <option value="" selected disabled hidden>Marital Status</option>
      <option>Single</option>
      <option>Married with Kids</option>
      <option>Married without Kids</option>
      <option>Divorced</option>
      <option>Widowed</option>
    </select>
  </div>

  <hr>

  <h3 class="text-lg font-semibold">First Transaction</h3>
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
    <input name="date" type="date" value="<?php echo date('Y-m-d'); ?>" class="input input-bordered" required>
    <input name="invoice" placeholder="Sales Invoice" class="input input-bordered" required>
    <input name="answered_by" placeholder="Answered By" class="input input-bordered">
  </div>

  <h4 class="mt-4 font-medium">Items Purchased</h4>
  <div class="max-h-64 overflow-y-auto border rounded p-3 bg-base-200">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
      <?php
      $itres = $conn->query("SELECT id, model FROM items WHERE status=1 ORDER BY model");
      while ($it = $itres->fetch_assoc()) {
          $id = $it['id'];
          $model = htmlspecialchars($it['model']);
          echo <<<HTML
          <label class="flex items-center gap-2">
            <input type="checkbox" name="items[{$id}][checked]" class="checkbox item-checkbox" data-id="{$id}">
            <span class="flex-1">{$model}</span>
            <input type="number" min="1" name="items[{$id}][qty]" class="input input-bordered w-20 item-qty" placeholder="Qty" data-id="{$id}">
          </label>
          HTML;
      }
      ?>
    </div>
  </div>

  <div class="mt-4">
    <button class="btn btn-primary">Save Customer + Transaction</button>
  </div>
</form>

<!-- SweetAlert2 -->
<script src="assets/sweetalert2@11"></script>
<script>
function toggleOtherAddress() {
  let select = document.getElementById("address_select");
  let otherInput = document.getElementById("address_other");
  if (select.value === "other") {
    otherInput.classList.remove("hidden");
    otherInput.required = true;
  } else {
    otherInput.classList.add("hidden");
    otherInput.required = false;
  }
}

// Validate phone before submit
document.getElementById("customerForm").addEventListener("submit", function(event) {
  let phone = document.getElementById("phone").value.trim();

  if (!/^\d{11}$/.test(phone)) {
    event.preventDefault(); // stop form submit
    Swal.fire({
      icon: 'error',
      title: 'Invalid Phone Number',
      text: 'Phone number must be exactly 11 digits.',
      confirmButtonText: 'OK',
      buttonsStyling: false,
      customClass: {
        confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded'
      }
    });
    return; // stop here
  }
});

// Link quantity ↔ checkbox
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

  // If checkbox is ticked, set qty=1 (if empty)
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
