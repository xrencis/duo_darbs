document.addEventListener('DOMContentLoaded',loadProducts);
function loadProducts(){
 fetch('products.php',{method:'POST',body:new URLSearchParams({action:'fetch'})})
 .then(r=>r.json()).then(showProducts);
}
function showProducts(data){
 let t=document.querySelector('table');
 t.innerHTML='<tr><th>Produkts</th><th>Kategorija</th><th>Cena</th><th>Firmas ID</th><th>Daudzums</th><th>Darbības</th></tr>';
 data.forEach(row=>{
  let tr=document.createElement('tr');
  tr.innerHTML=`<td>${row.name}</td><td>${row.category}</td><td>${row.price}</td><td>${row.firm}</td><td>${row.qty}</td><td><button class='delete' onclick='deleteProduct(${row.id})'>Dzēst</button> <button class='edit' onclick='editProduct(${row.id})'>Rediģēt</button></td>`;
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
    // Set default date range (last 30 days to tomorrow)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    
    const thirtyDaysAgo = new Date(today);
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        return date.toISOString().split('T')[0];
    };
    
    const dateFromInput = document.getElementById('report-date-from');
    const dateToInput = document.getElementById('report-date-to');
    
    dateFromInput.value = formatDate(thirtyDaysAgo);
    dateToInput.value = formatDate(tomorrow);
    
    // Set min date for "to" input to be the same as "from" date
    dateToInput.min = dateFromInput.value;
    
    document.getElementById('report-modal-overlay').style.display = 'flex';
}

function closeReportModal() {
    document.getElementById('report-modal-overlay').style.display = 'none';
}

function updateDateToMin() {
    const dateFrom = document.getElementById('report-date-from').value;
    const dateToInput = document.getElementById('report-date-to');
    
    // Set min date for "to" input
    dateToInput.min = dateFrom;
    
    // If current "to" date is before new min date, update it
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

    // Add time to make the date range inclusive
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
    
    // Clear previous results
    reportBody.innerHTML = '';
    reportTotal.textContent = '0.00 €';
    
    if (!orders || orders.length === 0) {
        reportBody.innerHTML = '<tr><td colspan="7" class="text-center">Nav atrasts neviens pasūtījums</td></tr>';
        return;
    }
    
    let totalSum = 0;
    
    orders.forEach(order => {
        const row = document.createElement('tr');
        
        // Format the date
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