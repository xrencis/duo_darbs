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
            <li><span class="icon">📦</span> <span id="show-place-modal">Izvietot preces</span></li>
            <li><span class="icon">📋</span> <a href="#" onclick="showReport(); return false;" style="text-decoration: none; color: inherit;">Sagatavot atskaiti</a></li>
            <li><span class="icon">📝</span> <span id="show-shelf-modal">Datu ievade</span></li>
            <li><span class="icon">↩️</span> <a href="../logout.php" style="text-decoration: none; color: inherit;">Iziet</a></li>
        </ul>
    </div>
    <div class="main">
        <h1 id="main-title">Produkti</h1>
        <div id="products-table-wrapper">
            <table id="products-table">
                <tr>
                    <th>Produkts</th>
                    <th>Kategorija</th>
                    <th>Cena</th>
                    <th>Firmas ID</th>
                    <th>Daudzums</th>
                </tr>
            </table>
        </div>
        <div id="shelves-table-wrapper" style="display:none;">
            <table id="shelves-table">
                <tr>
                    <th>Plaukta nosaukums</th>
                    <th>Kapacitāte</th>
                    <th>Darbības</th>
                </tr>
            </table>
        </div>
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
    <!-- Shelf Modal -->
    <div class="modal-overlay" id="shelf-modal-overlay">
        <div class="modal-box">
            <h2>Pievienot plauktu</h2>
            <div class="form-group">
                <label for="shelf-name">Plaukta nosaukums</label>
                <input type="text" id="shelf-name" placeholder="Plaukta nosaukums" required>
            </div>
            <div class="form-group">
                <label for="shelf-capacity">Plaukta kapacitāte (preču skaits)</label>
                <input type="number" id="shelf-capacity" placeholder="Kapacitāte" min="1" required>
            </div>
            <div class="modal-btns">
                <button onclick="addShelf()">Saglabāt</button>
                <button onclick="closeShelfModal()">Aizvērt</button>
            </div>
        </div>
    </div>
    <!-- Place Products Modal -->
    <div class="modal-overlay" id="place-modal-overlay">
        <div class="modal-box">
            <h2>Izvietot preces plauktā</h2>
            <div class="form-group">
                <label for="place-shelf">Izvēlies plauktu</label>
                <select id="place-shelf"></select>
            </div>
            <div class="form-group">
                <label for="place-product">Izvēlies produktu</label>
                <select id="place-product"></select>
            </div>
            <div class="form-group">
                <label for="place-qty">Daudzums</label>
                <input type="number" id="place-qty" min="1" value="1">
            </div>
            <div class="modal-btns">
                <button onclick="placeProductOnShelf()">Izvietot</button>
                <button onclick="closePlaceModal()">Aizvērt</button>
            </div>
        </div>
    </div>
    <!-- Edit Shelf Modal -->
    <div class="modal-overlay" id="edit-shelf-modal-overlay">
        <div class="modal-box">
            <h2>Rediģēt plauktu</h2>
            <input type="hidden" id="edit-shelf-id">
            <div class="form-group">
                <label for="edit-shelf-name">Plaukta nosaukums</label>
                <input type="text" id="edit-shelf-name" required>
            </div>
            <div class="form-group">
                <label for="edit-shelf-capacity">Kapacitāte</label>
                <input type="number" id="edit-shelf-capacity" min="1" required>
            </div>
            <div class="modal-btns">
                <button onclick="saveEditShelf()">Saglabāt</button>
                <button onclick="closeEditShelfModal()">Aizvērt</button>
            </div>
        </div>
    </div>
    <script src="products.js"></script>
    <script>
    document.getElementById('show-shelf-modal').onclick = function() {
        document.getElementById('shelf-modal-overlay').classList.add('active');
    };
    function closeShelfModal() {
        document.getElementById('shelf-modal-overlay').classList.remove('active');
    }
    document.getElementById('show-place-modal').onclick = function() {
        document.getElementById('place-modal-overlay').classList.add('active');
        loadShelvesAndProducts();
    };
    function closePlaceModal() {
        document.getElementById('place-modal-overlay').classList.remove('active');
    }
    document.getElementById('show-shelf-modal').addEventListener('click', function() {
        document.getElementById('products-table-wrapper').style.display = 'none';
        document.getElementById('shelves-table-wrapper').style.display = '';
        document.getElementById('main-title').textContent = 'Plaukti';
        loadShelvesTable();
    });
    document.getElementById('show-place-modal').addEventListener('click', function() {
        document.getElementById('products-table-wrapper').style.display = '';
        document.getElementById('shelves-table-wrapper').style.display = 'none';
        document.getElementById('main-title').textContent = 'Produkti';
    });
    function loadShelvesTable() {
        fetch('shelves.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'action=list' })
            .then(r => r.json()).then(data => {
                const table = document.getElementById('shelves-table');
                table.innerHTML = '<tr><th>Plaukta nosaukums</th><th>Kapacitāte</th><th>Darbības</th></tr>';
                if (data.shelves && data.shelves.length) {
                    data.shelves.forEach(shelf => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${shelf.name}</td><td>${shelf.capacity}</td><td><button onclick="showEditShelfModal(${shelf.id}, '${shelf.name}', ${shelf.capacity})">Rediģēt</button> <button onclick="deleteShelf(${shelf.id})">Dzēst</button></td>`;
                        table.appendChild(tr);
                    });
                }
            });
    }
    document.querySelector('.sidebar ul li:first-child').addEventListener('click', function() {
        document.getElementById('products-table-wrapper').style.display = '';
        document.getElementById('shelves-table-wrapper').style.display = 'none';
        document.getElementById('main-title').textContent = 'Produkti';
    });
    function showEditShelfModal(id, name, capacity) {
        document.getElementById('edit-shelf-id').value = id;
        document.getElementById('edit-shelf-name').value = name;
        document.getElementById('edit-shelf-capacity').value = capacity;
        document.getElementById('edit-shelf-modal-overlay').classList.add('active');
    }
    function closeEditShelfModal() {
        document.getElementById('edit-shelf-modal-overlay').classList.remove('active');
    }
    </script>
</body>
</html> 