<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>STASH - Pieslēgties</title>
    <link rel="stylesheet" href="css/login/style.css">
</head>
<body>
    <div class="login-container">
        <h1>STASH</h1>
        <form id="loginForm" action="auth.php" method="POST">
            <div class="form-group">
                <label for="username">Lietotājvārds</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Parole</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Pieslēgties</button>
        </form>
        <div id="errorMessage" class="error-message"></div>
    </div>
    <script src="js/login.js"></script>
</body>
</html> 