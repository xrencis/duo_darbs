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
        <form id="signupForm" action="register.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Lietotājvārds</label>
                <input type="text" id="username" name="username" required 
                       pattern="[a-zA-Z0-9_]{3,20}" 
                       title="Lietotājvārds jābūt no 3 līdz 20 rakstzīmēm, var saturēt tikai burtus, ciparus un pasvītrojuma zīmi">
            </div>
            <div class="form-group">
                <label for="password">Parole</label>
                <input type="password" id="password" name="password" required 
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$"
                       title="Parolei jābūt vismaz 6 rakstzīmēm garai, jāsatur vismaz viens burts un viens cipars">
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
    <script>
        function validateForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorMessage = document.getElementById('errorMessage');

            // Username validation
            if (username.length < 3 || username.length > 20) {
                errorMessage.textContent = 'Lietotājvārds jābūt no 3 līdz 20 rakstzīmēm';
                errorMessage.style.display = 'block';
                return false;
            }

            if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                errorMessage.textContent = 'Lietotājvārds var saturēt tikai burtus, ciparus un pasvītrojuma zīmi';
                errorMessage.style.display = 'block';
                return false;
            }

            // Check if username contains only numbers
            if (/^[0-9]+$/.test(username)) {
                errorMessage.textContent = 'Lietotājvārds nevar saturēt tikai ciparus';
                errorMessage.style.display = 'block';
                return false;
            }

            // Password validation
            if (password.length < 6) {
                errorMessage.textContent = 'Parolei jābūt vismaz 6 rakstzīmēm garai';
                errorMessage.style.display = 'block';
                return false;
            }

            if (!/[A-Za-z]/.test(password)) {
                errorMessage.textContent = 'Parolei jāsatur vismaz viens burts';
                errorMessage.style.display = 'block';
                return false;
            }

            if (!/[0-9]/.test(password)) {
                errorMessage.textContent = 'Parolei jāsatur vismaz viens cipars';
                errorMessage.style.display = 'block';
                return false;
            }

            // Confirm password
            if (password !== confirmPassword) {
                errorMessage.textContent = 'Paroles nesakrīt';
                errorMessage.style.display = 'block';
                return false;
            }

            return true;
        }

        // Real-time password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const errorMessage = document.getElementById('errorMessage');
            
            if (password.length < 6) {
                errorMessage.textContent = 'Parolei jābūt vismaz 6 rakstzīmēm garai';
                errorMessage.style.display = 'block';
            } else if (!/[A-Za-z]/.test(password)) {
                errorMessage.textContent = 'Parolei jāsatur vismaz viens burts';
                errorMessage.style.display = 'block';
            } else if (!/[0-9]/.test(password)) {
                errorMessage.textContent = 'Parolei jāsatur vismaz viens cipars';
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        });
    </script>
    <script src="js/signup.js"></script>
</body>
</html> 