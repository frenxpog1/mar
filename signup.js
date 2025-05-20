document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    
    // Basic validation
    if (!email || !password) {
        errorMessage.textContent = 'Please fill in all fields.';
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
        return;
    }
    
    if (!isValidEmail(email)) {
        errorMessage.textContent = 'Please enter a valid email address.';
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
        return;
    }
    
    try {
        // For demo purposes, we'll simulate a successful registration
        // In a real application, you would make an API call to your backend
        localStorage.setItem('isAuthenticated', 'true');
        localStorage.setItem('userEmail', email);
        
        successMessage.innerHTML = 'Registration successful! <a href="login.html">Log in</a> to continue.';
        successMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        
        // Clear the form
        document.getElementById('signupForm').reset();
    } catch (error) {
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Add click handlers for social buttons
document.querySelector('.google-btn').addEventListener('click', function() {
    alert('Google registration functionality would be implemented here.');
});

document.querySelector('.apple-btn').addEventListener('click', function() {
    alert('Apple registration functionality would be implemented here.');
}); 