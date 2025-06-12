<?php
require_once '../check_session.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>STASH - Noliktavas darbinieks</title>
    <link rel="stylesheet" href="../css/worker/style.css">
</head>
<body>
    <div class="user-profile">
        <span class="icon">👤</span>
        <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <span class="role">(Noliktavas darbinieks)</span>
    </div>
    <div class="sidebar">
        <div class="logo"> <span class="icon">🏠</span> <span>STASH</span> </div>
        <ul>
            <li><span class="icon">🏠</span> Sākums</li>
            <li><span class="icon">🚚</span> <a href="#" onclick="showOrderForm(); return false;" style="text-decoration: none; color: inherit;">Veikt pasūtījumu</a></li>
            <li><span class="icon">📄</span> <a href="#" onclick="showReport(); return false;" style="text-decoration: none; color: inherit;">Izveidot atskaiti</a></li>
            <li><span class="icon">↩️</span> <a href="../logout.php" style="text-decoration: none; color: inherit;">Iziet</a></li>
        </ul>
    </div>
    <div class="main">
        <h1>Produkti</h1>
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

    <div class="modal-overlay" id="add-product-modal-overlay">
        <div class="modal-box" id="add-product-modal">
            <h2>Pievienot jaunu produktu</h2>
            <div class="form-group">
                <label for="add-name">Nosaukums</label>
                <input type="text" id="add-name" required
                       minlength="2" maxlength="100"
                       pattern="^(?![\s0]+$).+"
                       title="Nosaukumam jābūt no 2 līdz 100 rakstzīmēm">
            </div>
            <div class="form-group">
                <label for="add-category">Kategorija</label>
                <input type="text" id="add-category" required
                       minlength="2" maxlength="50"
                       pattern="^(?![\s0]+$).+"
                       title="Kategorijai jābūt no 2 līdz 50 rakstzīmēm">
            </div>
            <div class="form-group">
                <label for="add-price">Cena</label>
                <input type="number" id="add-price" required
                       min="0.01" step="0.01"
                       title="Cenai jābūt vismaz 0.01">
            </div>
            <div class="form-group">
                <label for="add-firm">Firmas ID</label>
                <input type="text" id="add-firm" required
                       minlength="2" maxlength="50"
                       pattern="^(?![\s0]+$).+"
                       title="Firmas ID jābūt no 2 līdz 50 rakstzīmēm">
            </div>
            <div class="form-group">
                <label for="add-qty">Daudzums</label>
                <input type="number" id="add-qty" required
                       min="0"
                       title="Daudzumam jābūt nenegatīvam skaitlim">
            </div>
            <div class="modal-btns">
                <button onclick="submitAddProduct()">Pievienot</button>
                <button onclick="closeAddProductModal()">Aizvērt</button>
            </div>
        </div>
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

    <div class="modal-overlay" id="order-modal-overlay">
        <div class="modal-box" id="order-modal">
            <h2>Veikt pasūtījumu</h2>
            <div class="form-group">
                <label for="order-product">Izvēlieties produktu</label>
                <select id="order-product" required>
                    <option value="">Izvēlieties produktu...</option>
                </select>
            </div>
            <div class="form-group">
                <label for="order-quantity">Daudzums</label>
                <input type="number" id="order-quantity" min="1" required
                       title="Daudzumam jābūt lielākam par 0">
            </div>
            <div class="form-group">
                <label for="order-customer">Klienta vārds</label>
                <input type="text" id="order-customer" required
                       minlength="2" maxlength="100"
                       pattern="^(?![\s0]+$).+"
                       title="Klienta vārdam jābūt no 2 līdz 100 rakstzīmēm">
            </div>
            <div class="form-group">
                <label for="order-address">Piegādes adrese</label>
                <input type="text" id="order-address" required
                       minlength="5" maxlength="500"
                       pattern="^(?![\s0]+$).+"
                       title="Piegādes adresei jābūt no 5 līdz 500 rakstzīmēm">
            </div>
            <div class="modal-btns">
                <button onclick="submitOrder()">Pasūtīt</button>
                <button onclick="closeOrderModal()">Aizvērt</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="report-modal-overlay">
        <div class="modal-box" id="report-modal" style="max-height: 90vh; overflow-y: auto;">
            <h2>Pasūtījumu atskaite</h2>
            <div class="report-filters" style="text-align: center; margin-bottom: 20px; position: sticky; top: 0; background: white; padding: 10px 0;">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="report-date-from">No datuma:</label>
                    <input type="date" id="report-date-from" onchange="updateDateToMin()">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="report-date-to">Līdz datumam:</label>
                    <input type="date" id="report-date-to">
                </div>
                <button onclick="generateReport()" style="margin-top: 10px;">Ģenerēt atskaiti</button>
            </div>
            <div class="report-content">
                <table id="report-table">
                    <tr>
                        <th>Datums</th>
                        <th>Produkts</th>
                        <th>Daudzums</th>
                        <th>Cena</th>
                        <th>Kopā</th>
                        <th>Klients</th>
                        <th>Adrese</th>
                    </tr>
                </table>
            </div>
            <div class="modal-btns" style="position: sticky; bottom: 0; background: white; padding: 10px 0;">
                <button onclick="closeReportModal()">Aizvērt</button>
            </div>
        </div>
    </div>

    <script src="products.js"></script>
</body>
</html> 