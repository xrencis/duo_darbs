document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.textContent = 'Paroles nesakrīt';
        errorMessage.style.display = 'block';
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = data.message || 'Kļūda reģistrācijas laikā';
            errorMessage.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.textContent = 'Kļūda sistēmā. Lūdzu, mēģiniet vēlreiz.';
        errorMessage.style.display = 'block';
    });
}); 