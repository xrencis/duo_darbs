document.addEventListener('DOMContentLoaded',loadProducts);
function loadProducts(){
 fetch('products.php',{method:'POST',body:new URLSearchParams({action:'fetch'})})
 .then(r=>r.json()).then(showProducts);
}
function showProducts(data){
 let table=document.querySelector('table');
 let html='<tr><th>Produkts</th><th>Kategorija</th><th>Cena</th><th>Firmas ID</th><th>Daudzums</th><th>Darbības</th></tr>';
 data.forEach(p=>{
  html+=`<tr>
   <td>${p.name}</td>
   <td>${p.category}</td>
   <td>${p.price}</td>
   <td>${p.firm}</td>
   <td>${p.qty}</td>
   <td>
    <button onclick="editProduct(${p.id})" class="edit">Rediģēt</button>
    <button onclick="deleteProduct(${p.id})" class="delete">Dzēst</button>
   </td>
  </tr>`;
 });
 table.innerHTML=html;
}
function deleteProduct(id) {
    fetch('products.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'delete', id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadProducts();
        } else if (data.error.includes("Nevar dzēst produktu, jo tam ir saistīti")) {
            if (confirm('Šim produktam ir saistīti ieraksti. Vai vēlaties dzēst produktu kopā ar visiem saistītajiem ierakstiem?')) {
                fetch('products.php', {
                    method: 'POST',
                    body: new URLSearchParams({ action: 'delete', id, force: 'true' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadProducts();
                    } else {
                        alert(data.error || 'Kļūda dzēšot produktu!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Kļūda dzēšot produktu!');
                });
            }
        } else {
            alert(data.error || 'Kļūda dzēšot produktu!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda dzēšot produktu!');
    });
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
function validateAndSaveEditProduct() {
    const id = document.getElementById('edit-id').value;
    const name = document.getElementById('edit-name').value.trim();
    const category = document.getElementById('edit-category').value.trim();
    const price = parseFloat(document.getElementById('edit-price').value);
    const firm = document.getElementById('edit-firm').value.trim();
    const qty = parseInt(document.getElementById('edit-qty').value);
    const errorMessage = document.createElement('div');
    errorMessage.style.color = 'red';
    errorMessage.style.marginTop = '10px';
    errorMessage.style.textAlign = 'center';

    const existingError = document.querySelector('#edit-modal div[style*="color: red"]');
    if (existingError) {
        existingError.remove();
    }

    if (!name || /^[\s0]+$/.test(name)) {
        errorMessage.textContent = 'Produkta nosaukums nevar būt tukšs vai saturēt tikai nulles un atstarpes';
        document.getElementById('edit-modal').appendChild(errorMessage);
        return;
    }

    if (!category) {
        errorMessage.textContent = 'Kategorija nevar būt tukša';
        document.getElementById('edit-modal').appendChild(errorMessage);
        return;
    }

    if (isNaN(price) || price < 0.01) {
        errorMessage.textContent = 'Cenai jābūt vismaz 0.01';
        document.getElementById('edit-modal').appendChild(errorMessage);
        return;
    }

    if (!firm) {
        errorMessage.textContent = 'Firmas ID nevar būt tukšs';
        document.getElementById('edit-modal').appendChild(errorMessage);
        return;
    }

    if (isNaN(qty) || qty < 1) {
        errorMessage.textContent = 'Daudzumam jābūt pozitīvam skaitlim';
        document.getElementById('edit-modal').appendChild(errorMessage);
        return;
    }

    saveEditProduct();
}
function saveEditProduct(){
 let id=document.getElementById('edit-id').value;
 let name=document.getElementById('edit-name').value.trim();
 let category=document.getElementById('edit-category').value.trim();
 let price=document.getElementById('edit-price').value;
 let firm=document.getElementById('edit-firm').value.trim();
 let qty=document.getElementById('edit-qty').value;
 fetch('products.php',{
  method:'POST',
  body:new URLSearchParams({action:'edit',id,name,category,price,firm,qty})
 })
 .then(r=>r.json())
 .then(data=>{
  if(data.success){
   closeEditModal();
   loadProducts();
  }else{
   const errorMessage = document.createElement('div');
   errorMessage.style.color = 'red';
   errorMessage.style.marginTop = '10px';
   errorMessage.style.textAlign = 'center';
   errorMessage.textContent = data.message;
   document.getElementById('edit-modal').appendChild(errorMessage);
  }
 });
}
document.getElementById('show-add-form').onclick=function(){
 document.getElementById('add-modal-overlay').classList.add('active');
};
function closeAddModal(){
 document.getElementById('add-modal-overlay').classList.remove('active');
}
function validateAndAddProduct() {
    const name = document.getElementById('add-name').value.trim();
    const category = document.getElementById('add-category').value.trim();
    const price = parseFloat(document.getElementById('add-price').value);
    const firm = document.getElementById('add-firm').value.trim();
    const qty = parseInt(document.getElementById('add-qty').value);
    const errorMessage = document.createElement('div');
    errorMessage.style.color = 'red';
    errorMessage.style.marginTop = '10px';
    errorMessage.style.textAlign = 'center';

    const existingError = document.querySelector('#add-modal div[style*="color: red"]');
    if (existingError) {
        existingError.remove();
    }

    if (!name || /^[\s0]+$/.test(name)) {
        errorMessage.textContent = 'Produkta nosaukums nevar būt tukšs vai saturēt tikai nulles un atstarpes';
        document.getElementById('add-modal').appendChild(errorMessage);
        return;
    }

    if (!category) {
        errorMessage.textContent = 'Kategorija nevar būt tukša';
        document.getElementById('add-modal').appendChild(errorMessage);
        return;
    }

    if (isNaN(price) || price < 0.01) {
        errorMessage.textContent = 'Cenai jābūt vismaz 0.01';
        document.getElementById('add-modal').appendChild(errorMessage);
        return;
    }

    if (!firm) {
        errorMessage.textContent = 'Firmas ID nevar būt tukšs';
        document.getElementById('add-modal').appendChild(errorMessage);
        return;
    }

    if (isNaN(qty) || qty < 1) {
        errorMessage.textContent = 'Daudzumam jābūt pozitīvam skaitlim';
        document.getElementById('add-modal').appendChild(errorMessage);
        return;
    }

    addProduct();
}
function addProduct() {
    let name = document.getElementById('add-name').value.trim();
    let category = document.getElementById('add-category').value.trim();
    let price = document.getElementById('add-price').value;
    let firm = document.getElementById('add-firm').value.trim();
    let qty = document.getElementById('add-qty').value;

    fetch('products.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'add',
            name: name,
            category: category,
            price: price,
            firm: firm,
            qty: qty
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('add-name').value = '';
            document.getElementById('add-category').value = '';
            document.getElementById('add-price').value = '';
            document.getElementById('add-firm').value = '';
            document.getElementById('add-qty').value = '';
            closeAddModal();
            loadProducts();
        } else {
            const errorMessage = document.createElement('div');
            errorMessage.style.color = 'red';
            errorMessage.style.marginTop = '10px';
            errorMessage.style.textAlign = 'center';
            errorMessage.textContent = data.message;
            document.getElementById('add-modal').appendChild(errorMessage);
        }
    });
}
document.getElementById('show-add-user').onclick = function() {
    document.getElementById('add-user-modal-overlay').classList.add('active');
};
function closeAddUserModal() {
    document.getElementById('add-user-modal-overlay').classList.remove('active');
}
function validateAndAddUser() {
    const username = document.getElementById('add-user-username').value;
    const password = document.getElementById('add-user-password').value;
    const role = document.getElementById('add-user-role').value;
    const errorMessage = document.createElement('div');
    errorMessage.style.color = 'red';
    errorMessage.style.marginTop = '10px';
    errorMessage.style.textAlign = 'center';

    if (username.length < 3 || username.length > 20) {
        errorMessage.textContent = 'Lietotājvārds jābūt no 3 līdz 20 rakstzīmēm';
        document.getElementById('add-user-modal').appendChild(errorMessage);
        return;
    }

    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        errorMessage.textContent = 'Lietotājvārds var saturēt tikai burtus, ciparus un pasvītrojuma zīmi';
        document.getElementById('add-user-modal').appendChild(errorMessage);
        return;
    }

    if (/^[0-9]+$/.test(username)) {
        errorMessage.textContent = 'Lietotājvārds nevar saturēt tikai ciparus';
        document.getElementById('add-user-modal').appendChild(errorMessage);
        return;
    }

    if (password.length < 6) {
        errorMessage.textContent = 'Parolei jābūt vismaz 6 rakstzīmēm garai';
        document.getElementById('add-user-modal').appendChild(errorMessage);
        return;
    }

    if (!/[A-Za-z]/.test(password)) {
        errorMessage.textContent = 'Parolei jāsatur vismaz viens burts';
        document.getElementById('add-user-modal').appendChild(errorMessage);
        return;
    }

    if (!/[0-9]/.test(password)) {
        errorMessage.textContent = 'Parolei jāsatur vismaz viens cipars';
        document.getElementById('add-user-modal').appendChild(errorMessage);
        return;
    }

    if (!['worker', 'shelver', 'admin'].includes(role)) {
        errorMessage.textContent = 'Nederīgs lietotāja tips';
        document.getElementById('add-user-modal').appendChild(errorMessage);
        return;
    }

    const existingError = document.querySelector('#add-user-modal div[style*="color: red"]');
    if (existingError) {
        existingError.remove();
    }

    addUser();
}
function addUser() {
    let username = document.getElementById('add-user-username').value;
    let password = document.getElementById('add-user-password').value;
    let role = document.getElementById('add-user-role').value;

    fetch('add_user.php', {
        method: 'POST',
        body: new URLSearchParams({
            username: username,
            password: password,
            role: role
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('add-user-username').value = '';
            document.getElementById('add-user-password').value = '';
            document.getElementById('add-user-role').value = 'worker';
            closeAddUserModal();
        } else {
            const errorMessage = document.createElement('div');
            errorMessage.style.color = 'red';
            errorMessage.style.marginTop = '10px';
            errorMessage.style.textAlign = 'center';
            errorMessage.textContent = data.message;
            document.getElementById('add-user-modal').appendChild(errorMessage);
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const usersButton = document.getElementById('show-users');
    if (usersButton) {
        usersButton.addEventListener('click', function() {
            console.log('Users button clicked');
            loadUsers();
            document.getElementById('user-list-modal-overlay').classList.add('active');
        });
    } else {
        console.error('Users button not found');
    }
});
function closeUserListModal() {
    document.getElementById('user-list-modal-overlay').classList.remove('active');
}
function loadUsers() {
    console.log('Loading users...');

    fetch('test.php')
        .then(response => {
            console.log('Test response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Test raw response:', text);
            try {
                const testData = JSON.parse(text);
                console.log('Test parsed data:', testData);
                return fetch('get_users.php');
            } catch (e) {
                console.error('Test failed:', e);
                throw new Error('PHP test failed. Response: ' + text);
            }
        })
        .then(response => {
            console.log('Users response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Users raw response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse users JSON. Response:', text);
                throw new Error('Invalid JSON response. Response: ' + text);
            }
        })
        .then(data => {
            console.log('Parsed users data:', data);
            if (data.success) {
                const container = document.getElementById('user-list-container');
                container.innerHTML = '';
                
                if (data.users && data.users.length > 0) {
                    data.users.forEach(user => {
                        const userDiv = document.createElement('div');
                        userDiv.className = 'user-list-item';
                        
                        const userInfoDiv = document.createElement('div');
                        userInfoDiv.className = 'user-info';
                        
                        const usernameSpan = document.createElement('span');
                        usernameSpan.textContent = user.username;
                        
                        const roleSpan = document.createElement('span');
                        roleSpan.className = `user-role role-${user.role}`;
                        roleSpan.textContent = user.role;
                        
                        userInfoDiv.appendChild(usernameSpan);
                        userInfoDiv.appendChild(roleSpan);
                        
                        const actionsDiv = document.createElement('div');
                        actionsDiv.className = 'user-actions';
                        
                        const editButton = document.createElement('button');
                        editButton.className = 'edit';
                        editButton.textContent = 'Rediģēt';
                        editButton.onclick = () => showEditUserModal(user);
                        
                        const deleteButton = document.createElement('button');
                        deleteButton.className = 'delete';
                        deleteButton.textContent = 'Dzēst';
                        deleteButton.onclick = () => deleteUser(user);
                        
                        actionsDiv.appendChild(editButton);
                        actionsDiv.appendChild(deleteButton);
                        
                        userDiv.appendChild(userInfoDiv);
                        userDiv.appendChild(actionsDiv);
                        container.appendChild(userDiv);
                    });
                } else {
                    container.innerHTML = '<div class="user-list-item">Nav atrasts neviens lietotājs</div>';
                }
            } else {
                console.error('Server error:', data.message);
                alert(data.message || 'Kļūda ielādējot lietotājus');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const container = document.getElementById('user-list-container');
            container.innerHTML = `<div class="user-list-item">Kļūda: ${error.message}</div>`;
        });
}

function showEditUserModal(user) {
    const modalHTML = `
        <div class="modal-overlay" id="edit-user-modal-overlay">
            <div class="modal-box">
                <h2>Rediģēt lietotāju</h2>
                <div class="form-group">
                    <label>Lietotājvārds</label>
                    <input type="text" id="edit-username" value="${user.username}" readonly>
                </div>
                <div class="form-group">
                    <label>Lietotāja tips</label>
                    <select id="edit-role">
                        <option value="worker" ${user.role === 'worker' ? 'selected' : ''}>Noliktavas darbinieks</option>
                        <option value="shelver" ${user.role === 'shelver' ? 'selected' : ''}>Plauktu kārtotājs</option>
                        <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Administrators</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jauna parole (atstājiet tukšu, lai nemainītu)</label>
                    <input type="password" id="edit-password" placeholder="Jauna parole">
                </div>
                <div class="modal-btns">
                    <button id="save-user-btn">Saglabāt</button>
                    <button id="cancel-edit-btn">Atcelt</button>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('edit-user-modal-overlay');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const modal = document.getElementById('edit-user-modal-overlay');
    const saveBtn = document.getElementById('save-user-btn');
    const cancelBtn = document.getElementById('cancel-edit-btn');

    saveBtn.addEventListener('click', () => {
        const newRole = document.getElementById('edit-role').value;
        const newPassword = document.getElementById('edit-password').value;
        
        const data = {
            username: user.username,
            role: newRole,
            password: newPassword
        };
        
        fetch('update_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lietotājs veiksmīgi atjaunināts');
                modal.remove();
                loadUsers();
            } else {
                alert(data.message || 'Kļūda atjauninot lietotāju');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda atjauninot lietotāju');
        });
    });

    cancelBtn.addEventListener('click', () => {
        modal.remove();
    });

    requestAnimationFrame(() => {
        modal.classList.add('active');
    });
}

function deleteUser(user) {
    if (!confirm(`Vai tiešām vēlaties dzēst lietotāju "${user.username}"?`)) {
        return;
    }

    fetch('delete_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            username: user.username
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadUsers();
        } else {
            alert(data.message || 'Kļūda dzēšot lietotāju');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kļūda dzēšot lietotāju');
    });
} 