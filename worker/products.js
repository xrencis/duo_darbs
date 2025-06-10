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

function validateOrder() {
    const productId = document.getElementById('order-product').value;
    const quantity = parseInt(document.getElementById('order-quantity').value);
    const customer = document.getElementById('order-customer').value.trim();
    const address = document.getElementById('order-address').value.trim();
    const errorMessage = document.createElement('div');
    errorMessage.style.color = 'red';
    errorMessage.style.marginTop = '10px';
    errorMessage.style.textAlign = 'center';

    // Remove any existing error message
    const existingError = document.querySelector('#order-modal div[style*="color: red"]');
    if (existingError) {
        existingError.remove();
    }

    // Product validation
    if (!productId) {
        errorMessage.textContent = 'Lūdzu izvēlieties produktu';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    // Quantity validation
    if (isNaN(quantity) || quantity <= 0) {
        errorMessage.textContent = 'Daudzumam jābūt lielākam par 0';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    // Customer name validation
    if (!customer) {
        errorMessage.textContent = 'Klienta vārds nevar būt tukšs';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    if (customer.length < 2 || customer.length > 100) {
        errorMessage.textContent = 'Klienta vārdam jābūt no 2 līdz 100 rakstzīmēm';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    if (/^\d+$/.test(customer)) {
        errorMessage.textContent = 'Klienta vārds nevar sastāvēt tikai no cipariem';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    // Address validation
    if (!address) {
        errorMessage.textContent = 'Piegādes adrese nevar būt tukša';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    if (address.length < 5 || address.length > 500) {
        errorMessage.textContent = 'Piegādes adresei jābūt no 5 līdz 500 rakstzīmēm';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    if (/^\d+$/.test(address)) {
        errorMessage.textContent = 'Piegādes adrese nevar sastāvēt tikai no cipariem';
        document.getElementById('order-modal').appendChild(errorMessage);
        return false;
    }

    return true;
}

function submitOrder() {
    if (!validateOrder()) {
        return;
    }

    const productId = document.getElementById('order-product').value;
    const quantity = document.getElementById('order-quantity').value;
    const customer = document.getElementById('order-customer').value.trim();
    const address = document.getElementById('order-address').value.trim();

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
            const successMessage = document.createElement('div');
            successMessage.style.color = 'green';
            successMessage.style.marginTop = '10px';
            successMessage.style.textAlign = 'center';
            successMessage.textContent = 'Pasūtījums veiksmīgi izpildīts!';
            document.getElementById('order-modal').appendChild(successMessage);
            
            // Clear form fields after successful order
            document.getElementById('order-product').value = '';
            document.getElementById('order-quantity').value = '';
            document.getElementById('order-customer').value = '';
            document.getElementById('order-address').value = '';
            
            // Optionally, close modal after a short delay or allow user to close it
            setTimeout(() => {
                closeOrderModal();
            }, 2000); // Close after 2 seconds

            // Update the product list immediately
            loadProducts();
        } else {
            const errorMessage = document.createElement('div');
            errorMessage.style.color = 'red';
            errorMessage.style.marginTop = '10px';
            errorMessage.style.textAlign = 'center';
            errorMessage.textContent = data.message || 'Kļūda pasūtījuma izpildē!';
            document.getElementById('order-modal').appendChild(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = document.createElement('div');
        errorMessage.style.color = 'red';
        errorMessage.style.marginTop = '10px';
        errorMessage.style.textAlign = 'center';
        errorMessage.textContent = 'Kļūda pasūtījuma izpildē!';
        document.getElementById('order-modal').appendChild(errorMessage);
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
        if (data.error) {
            throw new Error(data.message);
        }

        const table = document.getElementById('report-table');
        // Keep the header row
        table.innerHTML = '<tr><th>Datums</th><th>Produkts</th><th>Daudzums</th><th>Cena</th><th>Kopā</th><th>Klients</th><th>Adrese</th></tr>';
        
        if (data.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="7" style="text-align: center;">Nav atrasts neviens pasūtījums šajā periodā</td>';
            table.appendChild(row);
            return;
        }

        let totalSum = 0;
        data.forEach(order => {
            const row = document.createElement('tr');
            const orderDate = new Date(order.order_date).toLocaleString('lv-LV');
            const price = parseFloat(order.price);
            const quantity = parseInt(order.quantity);
            const totalCost = (price * quantity).toFixed(2);
            totalSum += parseFloat(totalCost);
            
            row.innerHTML = `
                <td>${orderDate}</td>
                <td>${order.product_name}</td>
                <td>${quantity}</td>
                <td>${price.toFixed(2)} €</td>
                <td>${totalCost} €</td>
                <td>${order.customer_name}</td>
                <td>${order.delivery_address}</td>
            `;
            table.appendChild(row);
        });

        // Add total sum row
        const totalRow = document.createElement('tr');
        totalRow.style.fontWeight = 'bold';
        totalRow.innerHTML = `
            <td colspan="4" style="text-align: right;">Kopējā summa:</td>
            <td>${totalSum.toFixed(2)} €</td>
            <td colspan="2"></td>
        `;
        table.appendChild(totalRow);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda atskaites ģenerēšanā: ' + error.message);
    });
} 