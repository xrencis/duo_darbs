function showManageOrders() {
    document.getElementById('manage-orders-modal-overlay').style.display = 'flex';
    loadOrders();
}

function closeManageOrdersModal() {
    document.getElementById('manage-orders-modal-overlay').style.display = 'none';
}

function updateOrderDateToMin() {
    const fromDate = document.getElementById('order-date-from').value;
    if (fromDate) {
        document.getElementById('order-date-to').min = fromDate;
    }
}

function loadOrders() {
    const dateFrom = document.getElementById('order-date-from').value;
    const dateTo = document.getElementById('order-date-to').value;
    const status = document.getElementById('order-status-filter').value;

    const formData = new FormData();
    formData.append('action', 'manage_orders');
    if (dateFrom) formData.append('date_from', dateFrom);
    if (dateTo) formData.append('date_to', dateTo);
    if (status) formData.append('status', status);

    fetch('orders.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }
        displayOrders(data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda ielādējot pasūtījumus!');
    });
}

function displayOrders(orders) {
    const table = document.getElementById('orders-table');
    table.innerHTML = `
        <tr>
            <th>Datums</th>
            <th>Produkts</th>
            <th>Daudzums</th>
            <th>Klients</th>
            <th>Adrese</th>
            <th>Statuss</th>
            <th>Darbības</th>
        </tr>
    `;

    orders.forEach(order => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${new Date(order.order_date).toLocaleString('lv-LV')}</td>
            <td>${order.product_name}</td>
            <td>${order.quantity}</td>
            <td>${order.customer_name}</td>
            <td>${order.delivery_address}</td>
            <td>${getStatusText(order.status)}</td>
            <td>
                ${getActionButtons(order)}
            </td>
        `;
        table.appendChild(tr);
    });
}

function getStatusText(status) {
    const statusMap = {
        'pending': 'Gaida apstiprinājumu',
        'confirmed': 'Apstiprināts',
        'completed': 'Pabeigts',
        'cancelled': 'Atcelts'
    };
    return statusMap[status] || status;
}

function getActionButtons(order) {
    let buttons = '';
    
    if (order.status === 'pending') {
        buttons += `<button onclick="updateOrderStatus(${order.id}, 'confirmed')" class="btn btn-success">Apstiprināt</button>`;
        buttons += `<button onclick="updateOrderStatus(${order.id}, 'cancelled')" class="btn btn-danger">Atcelt</button>`;
    } else if (order.status === 'confirmed') {
        buttons += `<button onclick="updateOrderStatus(${order.id}, 'completed')" class="btn btn-success">Pabeigt</button>`;
        buttons += `<button onclick="updateOrderStatus(${order.id}, 'cancelled')" class="btn btn-danger">Atcelt</button>`;
    } else if (order.status === 'cancelled') {
        buttons += `<button onclick="deleteOrder(${order.id})" class="btn btn-danger">Dzēst</button>`;
    }
    
    return buttons;
}

function deleteOrder(orderId) {
    if (!confirm('Vai tiešām vēlaties dzēst šo pasūtījumu? Šo darbību nevar atsaukt.')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_order');
    formData.append('order_id', orderId);

    fetch('orders.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadOrders(); // Refresh the orders list
        } else {
            alert(data.error || 'Kļūda dzēšot pasūtījumu!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda dzēšot pasūtījumu!');
    });
}

function updateOrderStatus(orderId, newStatus) {
    if (!confirm('Vai tiešām vēlaties mainīt pasūtījuma statusu?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update_order_status');
    formData.append('order_id', orderId);
    formData.append('status', newStatus);

    fetch('orders.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadOrders(); // Refresh the orders list
        } else {
            alert(data.error || 'Kļūda atjauninot pasūtījuma statusu!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda atjauninot pasūtījuma statusu!');
    });
} 