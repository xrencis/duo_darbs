<?php
require_once '../check_session.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>STASH - Administrators</title>
    <link rel="stylesheet" href="../css/admin/style.css">
</head>
<body>
    <div class="user-profile">
        <span class="icon">👤</span>
        <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <span class="role">(Administrators)</span>
    </div>
    <div class="sidebar">
        <div class="logo"> <span class="icon">🏠</span> <span>STASH</span> </div>
        <ul>
            <li><span class="icon">🏠</span> Sākums</li>
            <li><span class="icon">➕</span> <span id="show-add-form">Pievienot produktu</span></li>
            <li><span class="icon">➕</span> <span id="show-add-user">Pievienot lietotāju</span></li>
            <li><span class="icon">👤</span> <span id="show-users">Lietotāji</span></li>
            <li><span class="icon">↩️</span> <a href="../logout.php" style="text-decoration: none; color: inherit;">Iziet</a></li>
        </ul>
    </div>
    <div class="main">
        <h1>Produkti</h1>
        <div class="modal-overlay" id="add-user-modal-overlay">
            <div class="modal-box" id="add-user-modal">
                <h2>Pievienot lietotāju</h2>
                <div class="form-group">
                    <label id="add-user-username-label">Lietotājvārds</label>
                    <input type="text" id="add-user-username" placeholder="Lietotājvārds">
                </div>
                <div class="form-group">
                    <label id="add-user-password-label">Parole</label>
                    <input type="password" id="add-user-password" placeholder="Parole">
                </div>
                <div class="form-group">
                    <label id="add-user-role-label">Lietotāja tips</label>
                    <select id="add-user-role">
                        <option value="worker">Noliktavas darbinieks</option>
                        <option value="shelver">Plauktu kārtotājs</option>
                        <option value="admin">Administrators</option>
                    </select>
                </div>
                <div class="modal-btns">
                    <button onclick="addUser()">Pievienot</button>
                    <button onclick="closeAddUserModal()">Aizvērt</button>
                </div>
            </div>
        </div>
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
                <div class="form-group">
                    <label id="edit-name-label">Nosaukums</label>
                    <input type="text" id="edit-name" placeholder="Nosaukums">
                </div>
                <div class="form-group">
                    <label id="edit-category-label">Kategorija</label>
                    <input type="text" id="edit-category" placeholder="Kategorija">
                </div>
                <div class="form-group">
                    <label id="edit-price-label">Cena</label>
                    <input type="number" id="edit-price" placeholder="Cena">
                </div>
                <div class="form-group">
                    <label id="edit-firm-label">Firmas ID</label>
                    <input type="text" id="edit-firm" placeholder="Firmas ID">
                </div>
                <div class="form-group">
                    <label id="edit-qty-label">Daudzums</label>
                    <input type="number" id="edit-qty" placeholder="Daudzums">
                </div>
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

    <!-- User List Modal -->
    <div id="user-list-modal-overlay" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Lietotāju saraksts</h2>
                <button onclick="closeUserListModal()" class="close-button">&times;</button>
            </div>
            <div class="modal-body">
                <div id="user-list-container">
                    <!-- Users will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</body>
</html> 