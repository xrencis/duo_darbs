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
            </tr>
        </table>
    </div>

    <!-- Order Form Modal -->
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
                <input type="number" id="order-quantity" min="1" required>
            </div>
            <div class="form-group">
                <label for="order-customer">Klienta vārds</label>
                <input type="text" id="order-customer" required>
            </div>
            <div class="form-group">
                <label for="order-address">Piegādes adrese</label>
                <input type="text" id="order-address" required>
            </div>
            <div class="modal-btns">
                <button onclick="submitOrder()">Pasūtīt</button>
                <button onclick="closeOrderModal()">Aizvērt</button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal-overlay" id="report-modal-overlay">
        <div class="modal-box" id="report-modal">
            <h2>Pasūtījumu atskaite</h2>
            <div class="report-filters" style="text-align: center; margin-bottom: 20px;">
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
                        <th>Klients</th>
                        <th>Adrese</th>
                    </tr>
                </table>
            </div>
            <div class="modal-btns">
                <button onclick="closeReportModal()">Aizvērt</button>
            </div>
        </div>
    </div>

    <script src="products.js"></script>
</body>
</html> 