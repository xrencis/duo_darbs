<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>STASH - Reģistrēties</title>
    <link rel="stylesheet" href="css/login/style.css">
</head>
<body>
    <div class="login-container">
        <h1>STASH</h1>
        <form id="signupForm" action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Lietotājvārds</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Parole</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Apstiprināt paroli</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="login-btn">Reģistrēties</button>
        </form>
        <div id="errorMessage" class="error-message"></div>
        <p style="text-align: center; margin-top: 20px;">
            Jau ir konts? <a href="login.php" style="color: #00ffff; text-decoration: none;">Pieslēgties</a>
        </p>
    </div>
    <script src="js/signup.js"></script>
</body>
</html> 