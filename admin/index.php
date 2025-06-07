<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>STASH - Administrators</title>
    <link rel="stylesheet" href="../css/admin/style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo"> <span class="icon">🏠</span> <span>STASH</span> </div>
        <ul>
            <li><span class="icon">🏠</span> Sākums</li>
            <li><span class="icon">➕</span> <span id="show-add-form">Pievienot produktu</span></li>
            <li><span class="icon">➕</span> Pievienot lietotāju</li>
            <li><span class="icon">👤</span> Lietotāji</li>
            <li><span class="icon">↩️</span> Iziet</li>
        </ul>
    </div>
    <div class="main">
        <h1>Produkti</h1>
        <div class="modal-overlay" id="add-modal-overlay">
            <div class="modal-box" id="add-modal">
                <h2>Pievienot produktu</h2>
                <input type="text" id="add-name" placeholder="Nosaukums">
                <input type="text" id="add-category" placeholder="Kategorija">
                <input type="number" id="add-price" placeholder="Cena">
                <input type="text" id="add-firm" placeholder="Firmas ID">
                <input type="number" id="add-qty" placeholder="Daudzums">
                <div class="modal-btns">
                    <button onclick="addProduct()">Pievienot</button>
                    <button onclick="closeAddModal()">Aizvērt</button>
                </div>
            </div>
        </div>
        <div class="modal-overlay" id="edit-modal-overlay">
            <div class="modal-box" id="edit-modal">
                <h2>Rediģēt produktu</h2>
                <input type="hidden" id="edit-id">
                <input type="text" id="edit-name" placeholder="Nosaukums">
                <input type="text" id="edit-category" placeholder="Kategorija">
                <input type="number" id="edit-price" placeholder="Cena">
                <input type="text" id="edit-firm" placeholder="Firmas ID">
                <input type="number" id="edit-qty" placeholder="Daudzums">
                <div class="modal-btns">
                    <button onclick="saveEditProduct()">Saglabāt</button>
                    <button onclick="closeEditModal()">Aizvērt</button>
                </div>
            </div>
        </div>
        <table>
            <tr>
                <th>Produkts</th>
                <th>Kategorija</th>
                <th>Cena</th>
                <th>Firmas ID</th>
                <th>Daudzums</th>
                <th>Darbības</th>
            </tr>
        </table>
    </div>
    <script src="products.js"></script>
</body>
</html> 