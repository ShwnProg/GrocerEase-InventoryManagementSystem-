const passwordInput = document.getElementById('password');
const showPassword = document.getElementById('togglePassword');

showPassword.addEventListener('click',function(){
    if(passwordInput.type == "password"){
        passwordInput.type = "text";
    }else{
        passwordInput.type = "password";
    }
    this.classList.toggle('fa-eye');
});