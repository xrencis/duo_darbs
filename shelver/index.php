<?php
require_once '../check_session.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>STASH - Plauktu kārtotājs</title>
    <link rel="stylesheet" href="../css/shelver/style.css">
</head>
<body>
    <div class="user-profile">
        <span class="icon">👤</span>
        <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <span class="role">(Plauktu kārtotājs)</span>
    </div>
    <div class="sidebar">
        <div class="logo"> <span class="icon">🏠</span> <span>STASH</span> </div>
        <ul>
            <li><span class="icon">🏠</span> Sākums</li>
            <li><span class="icon">📦</span> Izvietot preces</li>
            <li><span class="icon">📋</span> <a href="#" onclick="showReport(); return false;" style="text-decoration: none; color: inherit;">Sagatavot atskaiti</a></li>
            <li><span class="icon">📝</span> Datu ievade</li>
            <li><span class="icon">↩️</span> <a href="../logout.php" style="text-decoration: none; color: inherit;">Iziet</a></li>
        </ul>
    </div>
    <div class="main">
        <h1>Produkti</h1>
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
    <div id="report-modal-overlay" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Sagatavot atskaiti</h2>
                <button onclick="closeReportModal()" class="close-button">&times;</button>
            </div>
            <div class="modal-body">
                <div class="filter-section">
                    <div class="date-filters">
                        <div class="filter-group">
                            <label for="report-date-from">No datuma:</label>
                            <input type="date" id="report-date-from" onchange="updateDateToMin()">
                        </div>
                        <div class="filter-group">
                            <label for="report-date-to">Līdz datumam:</label>
                            <input type="date" id="report-date-to">
                        </div>
                    </div>
                    <button onclick="generateReport()" class="action-button">Ģenerēt atskaiti</button>
                </div>
                <div class="table-container">
                    <table id="report-table">
                        <thead>
                            <tr>
                                <th>Datums</th>
                                <th>Prece</th>
                                <th>Daudzums</th>
                                <th>Cena</th>
                                <th>Kopā</th>
                                <th>Klients</th>
                                <th>Piegādes adrese</th>
                            </tr>
                        </thead>
                        <tbody id="report-body">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right;"><strong>Kopā:</strong></td>
                                <td colspan="3" id="report-total">0.00 €</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="products.js"></script>
</body>
</html> 