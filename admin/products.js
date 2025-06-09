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