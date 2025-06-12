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
                    <input type="text" id="add-user-username" placeholder="Lietotājvārds" 
                           pattern="[a-zA-Z0-9_]{3,20}" 
                           title="Lietotājvārds jābūt no 3 līdz 20 rakstzīmēm, var saturēt tikai burtus, ciparus un pasvītrojuma zīmi"
                           required>
                </div>
                <div class="form-group">
                    <label id="add-user-password-label">Parole</label>
                    <input type="password" id="add-user-password" placeholder="Parole" 
                           pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$"
                           title="Parolei jābūt vismaz 6 rakstzīmēm garai, jāsatur vismaz viens burts un viens cipars"
                           required>
                </div>
                <div class="form-group">
                    <label id="add-user-role-label">Lietotāja tips</label>
                    <select id="add-user-role" required>
                        <option value="worker">Noliktavas darbinieks</option>
                        <option value="shelver">Plauktu kārtotājs</option>
                        <option value="admin">Administrators</option>
                    </select>
                </div>
                <div class="modal-btns">
                    <button onclick="validateAndAddUser()">Pievienot</button>
                    <button onclick="closeAddUserModal()">Aizvērt</button>
                </div>
            </div>
        </div>
        <div class="modal-overlay" id="add-modal-overlay">
            <div class="modal-box" id="add-modal">
                <h2>Pievienot produktu</h2>
                <div class="form-group">
                    <label for="add-name">Nosaukums</label>
                    <input type="text" id="add-name" placeholder="Nosaukums" required
                           pattern="^(?![\s0]+$).+"
                           title="Nosaukums nevar būt tukšs vai saturēt tikai nulles un atstarpes">
                </div>
                <div class="form-group">
                    <label for="add-category">Kategorija</label>
                    <input type="text" id="add-category" placeholder="Kategorija" required>
                </div>
                <div class="form-group">
                    <label for="add-price">Cena</label>
                    <input type="number" id="add-price" placeholder="Cena" required
                           min="0.01" step="0.01"
                           title="Cenai jābūt vismaz 0.01">
                </div>
                <div class="form-group">
                    <label for="add-firm">Firmas ID</label>
                    <input type="text" id="add-firm" placeholder="Firmas ID" required>
                </div>
                <div class="form-group">
                    <label for="add-qty">Daudzums</label>
                    <input type="number" id="add-qty" placeholder="Daudzums" required
                           min="1"
                           title="Daudzumam jābūt pozitīvam skaitlim">
                </div>
                <div class="modal-btns">
                    <button onclick="validateAndAddProduct()">Pievienot</button>
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
                    <input type="text" id="edit-name" placeholder="Nosaukums" required
                           pattern="^(?![\s0]+$).+"
                           title="Nosaukums nevar būt tukšs vai saturēt tikai nulles un atstarpes">
                </div>
                <div class="form-group">
                    <label id="edit-category-label">Kategorija</label>
                    <input type="text" id="edit-category" placeholder="Kategorija" required>
                </div>
                <div class="form-group">
                    <label id="edit-price-label">Cena</label>
                    <input type="number" id="edit-price" placeholder="Cena" required
                           min="0.01" step="0.01"
                           title="Cenai jābūt vismaz 0.01">
                </div>
                <div class="form-group">
                    <label id="edit-firm-label">Firmas ID</label>
                    <input type="text" id="edit-firm" placeholder="Firmas ID" required>
                </div>
                <div class="form-group">
                    <label id="edit-qty-label">Daudzums</label>
                    <input type="number" id="edit-qty" placeholder="Daudzums" required
                           min="1"
                           title="Daudzumam jābūt pozitīvam skaitlim">
                </div>
                <div class="modal-btns">
                    <button onclick="validateAndSaveEditProduct()">Saglabāt</button>
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

    <div class="modal-overlay" id="manage-orders-modal-overlay">
        <div class="modal-box" id="manage-orders-modal" style="max-height: 90vh; overflow-y: auto;">
            <h2>Pārvaldīt pasūtījumus</h2>
            <div class="order-filters" style="text-align: center; margin-bottom: 20px; position: sticky; top: 0; background: white; padding: 10px 0;">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="order-date-from">No datuma:</label>
                    <input type="date" id="order-date-from" onchange="updateOrderDateToMin()">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="order-date-to">Līdz datumam:</label>
                    <input type="date" id="order-date-to">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="order-status-filter">Statuss:</label>
                    <select id="order-status-filter">
                        <option value="">Visi</option>
                        <option value="pending">Gaida apstiprinājumu</option>
                        <option value="confirmed">Apstiprināts</option>
                        <option value="completed">Pabeigts</option>
                        <option value="cancelled">Atcelts</option>
                    </select>
                </div>
                <button onclick="loadOrders()" style="margin-top: 10px;">Filtrēt</button>
            </div>
            <div class="orders-content">
                <table id="orders-table">
                    <tr>
                        <th>Datums</th>
                        <th>Produkts</th>
                        <th>Daudzums</th>
                        <th>Klients</th>
                        <th>Adrese</th>
                        <th>Statuss</th>
                        <th>Darbības</th>
                    </tr>
                </table>
            </div>
            <div class="modal-btns" style="position: sticky; bottom: 0; background: white; padding: 10px 0;">
                <button onclick="closeManageOrdersModal()">Aizvērt</button>
            </div>
        </div>
    </div>

    <script src="products.js"></script>
    <script src="orders.js"></script>

    <div id="user-list-modal-overlay" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Lietotāju saraksts</h2>
                <button onclick="closeUserListModal()" class="close-button">&times;</button>
            </div>
            <div class="modal-body">
                <div id="user-list-container">
                </div>
            </div>
        </div>
    </div>
</body>
</html> 