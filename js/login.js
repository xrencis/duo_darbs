document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = data.message || 'Nepareizs lietotājvārds vai parole';
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