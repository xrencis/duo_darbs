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

function showOrderForm() {
    // Load products into the select dropdown
    fetch('products.php', {
        method: 'POST',
        body: new URLSearchParams({action: 'fetch'})
    })
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('order-product');
        select.innerHTML = '<option value="">Izvēlieties produktu...</option>';
        data.forEach(product => {
            if (product.qty > 0) { // Only show products that are in stock
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.name} (Pieejams: ${product.qty})`;
                select.appendChild(option);
            }
        });
        document.getElementById('order-modal-overlay').classList.add('active');
    })
    .catch(error => {
        console.error('Error loading products:', error);
        alert('Kļūda produktu saraksta ielādēšanā!');
    });
}

function closeOrderModal() {
    document.getElementById('order-modal-overlay').classList.remove('active');
    // Clear form fields
    document.getElementById('order-product').value = '';
    document.getElementById('order-quantity').value = '';
    document.getElementById('order-customer').value = '';
    document.getElementById('order-address').value = '';
}

function submitOrder() {
    const productId = document.getElementById('order-product').value;
    const quantity = document.getElementById('order-quantity').value;
    const customer = document.getElementById('order-customer').value;
    const address = document.getElementById('order-address').value;

    if (!productId || !quantity || !customer || !address) {
        alert('Lūdzu aizpildiet visus laukus!');
        return;
    }

    fetch('products.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'order',
            id: productId,
            quantity: quantity,
            customer: customer,
            address: address
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Pasūtījums veiksmīgi izpildīts!');
            closeOrderModal();
            // Update the product list immediately
            loadProducts();
        } else {
            alert(data.message || 'Kļūda pasūtījuma izpildē!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda pasūtījuma izpildē!');
    });
}

function updateDateToMin() {
    const dateFrom = document.getElementById('report-date-from').value;
    const dateTo = document.getElementById('report-date-to');
    
    // Set minimum date for "to" date
    dateTo.min = dateFrom;
    
    // If current "to" date is before "from" date, update it
    if (dateTo.value && dateTo.value < dateFrom) {
        dateTo.value = dateFrom;
    }
}

function showReport() {
    // Set default date range (last 30 days to tomorrow)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(tomorrow.getDate() - 30);
    
    const dateFrom = thirtyDaysAgo.toISOString().split('T')[0];
    const dateTo = tomorrow.toISOString().split('T')[0];
    
    document.getElementById('report-date-from').value = dateFrom;
    document.getElementById('report-date-to').value = dateTo;
    document.getElementById('report-date-to').min = dateFrom;
    
    document.getElementById('report-modal-overlay').classList.add('active');
    generateReport(); // Generate initial report
}

function closeReportModal() {
    document.getElementById('report-modal-overlay').classList.remove('active');
}

function generateReport() {
    const dateFrom = document.getElementById('report-date-from').value;
    const dateTo = document.getElementById('report-date-to').value;

    // Validate dates
    if (dateTo < dateFrom) {
        alert('"Līdz datumam" nevar būt pirms "No datuma"!');
        return;
    }

    fetch('products.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'report',
            date_from: dateFrom,
            date_to: dateTo
        })
    })
    .then(r => r.json())
    .then(data => {
        const table = document.getElementById('report-table');
        // Keep the header row
        table.innerHTML = '<tr><th>Datums</th><th>Produkts</th><th>Daudzums</th><th>Klients</th><th>Adrese</th></tr>';
        
        data.forEach(order => {
            const row = document.createElement('tr');
            const orderDate = new Date(order.order_date).toLocaleString('lv-LV');
            row.innerHTML = `
                <td>${orderDate}</td>
                <td>${order.product_name}</td>
                <td>${order.quantity}</td>
                <td>${order.customer_name}</td>
                <td>${order.delivery_address}</td>
            `;
            table.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda atskaites ģenerēšanā!');
    });
} 