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
                <input type="text" id="username" name="username" required 
                       pattern="^(?=[^A-Z]*[A-Z])(?=[^0-9]*[0-9])(?=[^_]*_)[A-Za-z0-9_]{3,20}$"
                       title="Lietotājvārdam jābūt 3-20 simboliem, vismaz 1 lielajam burtam, 1 ciparam un 1 pasvītrojuma zīmei (_). Atļauti tikai burti, cipari un pasvītrojums.">
            </div>
            <div class="form-group">
                <label for="password">Parole</label>
                <input type="password" id="password" name="password" required 
                       pattern="^(?=(?:[^a-z]*[a-z]){3,})(?=[^A-Z]*[A-Z])(?=(?:[^0-9]*[0-9]){2,})(?=[^-]*-)[A-Za-z0-9-]{3,20}$"
                       title="Parolei jābūt 3-20 simboliem, vismaz 3 mazajiem burtiem, 1 lielajam burtam, 2 cipariem un 1 domuzīmei (-). Atļauti tikai burti, cipari un domuzīme (-)">
            </div>
            <button type="submit" class="login-btn">Pieslēgties</button>
        </form>
        <div id="errorMessage" class="error-message"></div>
        <p style="text-align: center; margin-top: 20px;">
            Nav konta? <a href="signup.php" style="color: #00ffff; text-decoration: none;">Reģistrēties</a>
        </p>
    </div>
    <script src="js/login.js"></script>
</body>
</html> 