const passwordInput = document.getElementById('password');
const showPassword = document.getElementById('togglePassword');

if (passwordInput && showPassword) {
    showPassword.addEventListener('click', function() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            showPassword.classList.remove('fa-eye-slash');
            showPassword.classList.add('fa-eye');
        } else {
            passwordInput.type = "password";
            showPassword.classList.remove('fa-eye');
            showPassword.classList.add('fa-eye-slash');
        }
    });
}