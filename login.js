// Login page specific JavaScript
document.addEventListener("DOMContentLoaded", () => {
  // Get form elements
  const loginForm = document.querySelector(".auth-form");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");
  const signInButton = document.querySelector(".auth-button");

  // Add event listener to the form
  if (loginForm && usernameInput && passwordInput && signInButton) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();

      // Check for specific credentials
      if (usernameInput.value === "francis@email.com" && passwordInput.value === "123") {
        // Show success message
        showMessage("Login successful! Redirecting to dashboard...", "success");

        // Add loading state to button
        signInButton.disabled = true;
        const originalText = signInButton.textContent;
        signInButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';

        // Redirect to dashboard after short delay
        setTimeout(() => {
          window.location.href = "dashboard.html";
        }, 1000);
      } else {
        // Show error message
        showMessage("Invalid username or password. Try francis/123", "error");

        // Shake the form to indicate error
        loginForm.classList.add("shake");
        setTimeout(() => {
          loginForm.classList.remove("shake");
        }, 500);
      }
    });
  }

  // Function to show message
  function showMessage(text, type) {
    // Remove any existing messages
    const existingMessage = document.querySelector(".message");
    if (existingMessage) {
      existingMessage.remove();
    }

    // Create message element
    const message = document.createElement("div");
    message.className = `message ${type}-message`;
    message.textContent = text;

    // Insert message before the form or after the heading
    const formContainer = document.querySelector(".auth-form-container");
    const formHeader = document.querySelector(".auth-form-header");

    if (formContainer && formHeader) {
      formContainer.insertBefore(message, formHeader.nextSibling);
    }
  }

  // Add "Enter" key support
  document.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && usernameInput && passwordInput) {
      if (usernameInput.value === "francis@email.com" && passwordInput.value === "123") {
        // Show success and redirect
        showMessage("Login successful! Redirecting to dashboard...", "success");

        // Add loading state to the button
        if (signInButton) {
          signInButton.disabled = true;
          signInButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        }

        // Redirect to dashboard
        setTimeout(() => {
          window.location.href = "dashboard.html";
        }, 1000);
      } else {
        // Show error
        showMessage("Invalid username or password. Try francis/123", "error");

        // Shake the form
        if (loginForm) {
          loginForm.classList.add("shake");
          setTimeout(() => {
            loginForm.classList.remove("shake");
          }, 500);
        }
      }
    }
  });

  // Add CSS for shake animation if it doesn't exist
  if (!document.querySelector("#shake-animation")) {
    const style = document.createElement("style");
    style.id = "shake-animation";
    style.textContent = `
      @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
      }
      
      .shake {
        animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
      }
      
      .message {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        text-align: center;
      }
      
      .success-message {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.2);
      }
      
      .error-message {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
      }
    `;
    document.head.appendChild(style);
  }
});