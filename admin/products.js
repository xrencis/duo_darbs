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
 })
 .then(r=>r.json())
 .then(data=>{
  if(data.success){
   closeEditModal();
   loadProducts();
  }else{
   alert(data.message);
  }
 });
}
document.getElementById('show-add-form').onclick=function(){
 document.getElementById('add-modal-overlay').classList.add('active');
};
function closeAddModal(){
 document.getElementById('add-modal-overlay').classList.remove('active');
}
function addProduct(){
 let name=document.getElementById('add-name').value;
 let category=document.getElementById('add-category').value;
 let price=document.getElementById('add-price').value;
 let firm=document.getElementById('add-firm').value;
 let qty=document.getElementById('add-qty').value;
 fetch('products.php',{
  method:'POST',
  body:new URLSearchParams({action:'add',name,category,price,firm,qty})
 })
 .then(r=>r.json())
 .then(data=>{
  if(data.success){
   document.getElementById('add-name').value='';
   document.getElementById('add-category').value='';
   document.getElementById('add-price').value='';
   document.getElementById('add-firm').value='';
   document.getElementById('add-qty').value='';
   closeAddModal();
   loadProducts();
  }else{
   alert(data.message);
  }
 });
}
document.getElementById('show-add-user').onclick = function() {
    document.getElementById('add-user-modal-overlay').classList.add('active');
};
function closeAddUserModal() {
    document.getElementById('add-user-modal-overlay').classList.remove('active');
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
            alert(data.message);
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
    
    // First test if PHP is working
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
                // If test works, proceed to get users
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
                        
                        const usernameSpan = document.createElement('span');
                        usernameSpan.textContent = user.username;
                        
                        const roleSpan = document.createElement('span');
                        roleSpan.className = `user-role role-${user.role}`;
                        roleSpan.textContent = user.role;
                        
                        userDiv.appendChild(usernameSpan);
                        userDiv.appendChild(roleSpan);
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