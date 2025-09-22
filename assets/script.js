
document.addEventListener('DOMContentLoaded', function() {

  const searchInput = document.querySelector('input[name=q]');
  if (searchInput) {
    const resultsBox = document.createElement('div');
    resultsBox.className = 'mt-2 bg-white shadow rounded p-2 max-h-72 overflow-auto';
    searchInput.parentNode.parentNode.appendChild(resultsBox);

    let timeout = null;
    searchInput.addEventListener('input', function() {
      clearTimeout(timeout);
      const q = this.value.trim();
      if (q.length < 2) {
        resultsBox.innerHTML = '';
        return;
      }
      timeout = setTimeout(function() {
        fetch('api/search.php?q=' + encodeURIComponent(q))
          .then(r => r.json())
          .then(data => {
            if (!Array.isArray(data)) {
              resultsBox.innerHTML = '<div class="text-red-500">Search error</div>';
              return;
            }
            if (data.length === 0) {
              resultsBox.innerHTML = '<div class="text-sm text-gray-500 p-2">No results</div>';
              return;
            }
            resultsBox.innerHTML = '<ul class="space-y-1">'
              + data.map(d => '<li class="p-2 border rounded hover:bg-base-200 cursor-pointer" data-id="'+d.id+'"><strong>'+escapeHtml(d.name)+'</strong><br><span class="text-sm text-gray-600">'+escapeHtml(d.phone)+'</span></li>').join('')
              + '</ul>';
            resultsBox.querySelectorAll('li[data-id]').forEach(li => {
              li.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                window.location = 'customer_profile.php?id=' + id;
              });
            });
          }).catch(err => {
            resultsBox.innerHTML = '<div class="text-red-500">Search failed</div>';
            console.error(err);
          });
      }, 300);
    });
  }

  const addCustPhone = document.querySelector('form[action="save_customer.php"] input[name=phone]');
  const addCustForm = document.querySelector('form[action="save_customer.php"]');
  if (addCustPhone) {
    const warn = document.createElement('div');
    warn.className = 'text-sm text-yellow-700 mt-2';
    addCustPhone.parentNode.appendChild(warn);

    let t2 = null;
    addCustPhone.addEventListener('input', function() {
      clearTimeout(t2);
      const phone = this.value.trim();
      warn.textContent = '';
      if (phone.length < 6) return;
      t2 = setTimeout(function() {
        fetch('api/check_phone.php?phone=' + encodeURIComponent(phone))
          .then(r => r.json())
          .then(resp => {
            if (resp.found) {
              warn.innerHTML = '⚠️ Customer already exists: <strong>'+escapeHtml(resp.customer.name)+'</strong> ('+escapeHtml(resp.customer.phone)+') — <a href="customer_profile.php?id='+resp.customer.id+'" class="link">View profile</a>';
            } else {
              warn.textContent = '';
            }
          }).catch(err => console.error(err));
      }, 350);
    });

    addCustForm.addEventListener('submit', function(e) {
      const phone = addCustPhone.value.trim();
      if (!phone) return;
      e.preventDefault();
      fetch('api/check_phone.php?phone=' + encodeURIComponent(phone))
        .then(r => r.json())
        .then(resp => {
          if (resp.found) {
            const proceed = confirm('Customer with this phone already exists: ' + resp.customer.name + '\n\nClick OK to go to their profile, Cancel to return and change the number.');
            if (proceed) {
              window.location = 'customer_profile.php?id=' + resp.customer.id;
            }
          } else {
            addCustForm.submit();
          }
        }).catch(err => {
          console.error(err);
          addCustForm.submit(); 
        });
    });
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m]; });
  }
});

function toggleOtherAddress() {
  const select = document.getElementById("address_select");
  const otherInput = document.getElementById("address_other");
  if (select.value === "other") {
    otherInput.classList.remove("hidden");
    otherInput.required = true;
  } else {
    otherInput.classList.add("hidden");
    otherInput.required = false;
  }
}
