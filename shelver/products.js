document.addEventListener('DOMContentLoaded',loadProducts);
function loadProducts(){
 fetch('products.php',{method:'POST',body:new URLSearchParams({action:'fetch'})})
 .then(r=>r.json()).then(showProducts);
}
function showProducts(data){
 let t=document.querySelector('table');
 t.innerHTML='<tr><th>Produkts</th><th>Kategorija</th><th>Cena</th><th>Firmas ID</th><th>Daudzums</th></tr>';
 data.forEach(row=>{
  let tr=document.createElement('tr');
  tr.innerHTML=`<td>${row.name}</td><td>${row.category}</td><td>${row.price}</td><td>${row.firm}</td><td>${row.qty}</td>`;
  t.appendChild(tr);
 });
}
function deleteProduct(id){
 fetch('products.php',{method:'POST',body:new URLSearchParams({action:'delete',id})})
 .then(()=>loadProducts());
}
function editProduct(id){
 fetch('products.php',{
  method:'POST',
  body:new URLSearchParams({action:'fetch'})
 }).then(r=>r.json()).then(data=>{
  let prod=data.find(p=>p.id==id);
  if(prod){
   document.getElementById('edit-id').value=prod.id;
   document.getElementById('edit-name-label').textContent = `Nosaukums: ${prod.name}`;
   document.getElementById('edit-name').value=prod.name;
   document.getElementById('edit-category-label').textContent = `Kategorija: ${prod.category}`;
   document.getElementById('edit-category').value=prod.category;
   document.getElementById('edit-price-label').textContent = `Cena: ${prod.price}`;
   document.getElementById('edit-price').value=prod.price;
   document.getElementById('edit-firm-label').textContent = `Firmas ID: ${prod.firm}`;
   document.getElementById('edit-firm').value=prod.firm;
   document.getElementById('edit-qty-label').textContent = `Daudzums: ${prod.qty}`;
   document.getElementById('edit-qty').value=prod.qty;
   document.getElementById('edit-modal-overlay').classList.add('active');
  }
 });
}
function closeEditModal(){
 document.getElementById('edit-modal-overlay').classList.remove('active');
}
function saveEditProduct(){
 let id=document.getElementById('edit-id').value;
 let name=document.getElementById('edit-name').value;
 let category=document.getElementById('edit-category').value;
 let price=document.getElementById('edit-price').value;
 let firm=document.getElementById('edit-firm').value;
 let qty=document.getElementById('edit-qty').value;
 fetch('products.php',{
  method:'POST',
  body:new URLSearchParams({action:'edit',id,name,category,price,firm,qty})
 }).then(()=>{
  closeEditModal();
  loadProducts();
 });
}

function showReport() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    
    const thirtyDaysAgo = new Date(today);
    thirtyDaysAgo.setDate(today.getDate() - 30);

    const formatDate = (date) => {
        return date.toISOString().split('T')[0];
    };
    
    const dateFromInput = document.getElementById('report-date-from');
    const dateToInput = document.getElementById('report-date-to');
    
    dateFromInput.value = formatDate(thirtyDaysAgo);
    dateToInput.value = formatDate(tomorrow);

    dateToInput.min = dateFromInput.value;
    
    document.getElementById('report-modal-overlay').style.display = 'flex';
}

function closeReportModal() {
    document.getElementById('report-modal-overlay').style.display = 'none';
}

function updateDateToMin() {
    const dateFrom = document.getElementById('report-date-from').value;
    const dateToInput = document.getElementById('report-date-to');

    dateToInput.min = dateFrom;

    if (dateToInput.value < dateFrom) {
        dateToInput.value = dateFrom;
    }
}

function generateReport() {
    const dateFrom = document.getElementById('report-date-from').value;
    const dateTo = document.getElementById('report-date-to').value;

    if (!dateFrom || !dateTo) {
        alert('Lūdzu, ievadiet datumu diapazonu!');
        return;
    }

    const fromDate = new Date(dateFrom + 'T00:00:00');
    const toDate = new Date(dateTo + 'T23:59:59');

    if (fromDate > toDate) {
        alert('Sākuma datumam jābūt mazākam par beigu datumu!');
        return;
    }

    fetch('products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=generate_report&date_from=${dateFrom}&date_to=${dateTo}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayReport(data.orders);
        } else {
            alert(data.error || 'Kļūda ģenerējot atskaiti!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda ģenerējot atskaiti!');
    });
}

function displayReport(orders) {
    const reportTable = document.getElementById('report-table');
    const reportBody = document.getElementById('report-body');
    const reportTotal = document.getElementById('report-total');

    reportBody.innerHTML = '';
    reportTotal.textContent = '0.00 €';
    
    if (!orders || orders.length === 0) {
        reportBody.innerHTML = '<tr><td colspan="7" class="text-center">Nav atrasts neviens pasūtījums</td></tr>';
        return;
    }
    
    let totalSum = 0;
    
    orders.forEach(order => {
        const row = document.createElement('tr');

        const orderDate = new Date(order.order_date);
        const formattedDate = orderDate.toLocaleDateString('lv-LV', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
        
        row.innerHTML = `
            <td>${formattedDate}</td>
            <td>${order.product_name}</td>
            <td>${order.quantity}</td>
            <td>${parseFloat(order.price).toFixed(2)} €</td>
            <td>${parseFloat(order.total_cost).toFixed(2)} €</td>
            <td>${order.customer_name}</td>
            <td>${order.delivery_address}</td>
        `;
        
        reportBody.appendChild(row);
        totalSum += parseFloat(order.total_cost);
    });
    
    reportTotal.textContent = `${totalSum.toFixed(2)} €`;
}

function addShelf() {
  const name = document.getElementById('shelf-name').value.trim();
  const capacity = document.getElementById('shelf-capacity').value;
  if (!name || !capacity || capacity < 1) {
    alert('Lūdzu, ievadiet korektus datus!');
    return;
  }
  fetch('shelves.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=add&shelf_name=${encodeURIComponent(name)}&capacity=${encodeURIComponent(capacity)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert('Plaukts pievienots!');
      closeShelfModal();
    } else {
      alert(data.error || 'Kļūda pievienojot plauktu!');
    }
  })
  .catch(() => alert('Kļūda savienojumā ar serveri!'));
}

function loadShelvesAndProducts() {
  fetch('shelves.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'action=list' })
    .then(r => r.json()).then(data => {
      const shelfSelect = document.getElementById('place-shelf');
      shelfSelect.innerHTML = '';
      if (data.shelves && data.shelves.length) {
        data.shelves.forEach(shelf => {
          const opt = document.createElement('option');
          opt.value = shelf.id;
          opt.textContent = `${shelf.name} (kapacitāte: ${shelf.capacity})`;
          shelfSelect.appendChild(opt);
        });
      } else {
        shelfSelect.innerHTML = '<option disabled>Nav plauktu</option>';
      }
    });
  fetch('products.php', { method: 'POST', body: new URLSearchParams({ action: 'fetch' }) })
    .then(r => r.json()).then(data => {
      const prodSelect = document.getElementById('place-product');
      prodSelect.innerHTML = '';
      if (data && data.length) {
        data.forEach(prod => {
          const opt = document.createElement('option');
          opt.value = prod.id;
          opt.textContent = `${prod.name} (${prod.qty} gab.)`;
          prodSelect.appendChild(opt);
        });
      } else {
        prodSelect.innerHTML = '<option disabled>Nav produktu</option>';
      }
    });
}

function placeProductOnShelf() {
  const shelfId = document.getElementById('place-shelf').value;
  const productId = document.getElementById('place-product').value;
  const qty = parseInt(document.getElementById('place-qty').value, 10);
  if (!shelfId || !productId || !qty || qty < 1) {
    alert('Lūdzu, aizpildiet visus laukus korekti!');
    return;
  }
  fetch('shelves.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=place&shelf_id=${encodeURIComponent(shelfId)}&product_id=${encodeURIComponent(productId)}&qty=${encodeURIComponent(qty)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert('Prece izvietota plauktā!');
      closePlaceModal();
    } else {
      alert(data.error || 'Kļūda izvietojot preci!');
    }
  })
  .catch(() => alert('Kļūda savienojumā ar serveri!'));
}

function deleteShelf(id) {
  if (!confirm('Vai tiešām dzēst šo plauktu?')) return;
  fetch('shelves.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=delete&id=${encodeURIComponent(id)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      loadShelvesTable();
    } else {
      alert(data.error || 'Kļūda dzēšot plauktu!');
    }
  });
}

function saveEditShelf() {
  const id = document.getElementById('edit-shelf-id').value;
  const name = document.getElementById('edit-shelf-name').value.trim();
  const capacity = document.getElementById('edit-shelf-capacity').value;
  if (!name || !capacity || capacity < 1) {
    alert('Lūdzu, ievadiet korektus datus!');
    return;
  }
  fetch('shelves.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=edit&id=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}&capacity=${encodeURIComponent(capacity)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      closeEditShelfModal();
      loadShelvesTable();
    } else {
      alert(data.error || 'Kļūda saglabājot plauktu!');
    }
  });
} 