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
 let vals=prompt('Ievadiet: nosaukums,kategorija,cena,firmas id,daudzums').split(',');
 fetch('products.php',{method:'POST',body:new URLSearchParams({action:'edit',id,name:vals[0],category:vals[1],price:vals[2],firm:vals[3],qty:vals[4]})})
 .then(()=>loadProducts());
} 