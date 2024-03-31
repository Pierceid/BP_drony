const btn_register = document.getElementById('register');
const btn_login = document.getElementById('login');
const container = document.querySelector('.container');

btn_register.addEventListener('click', () => {
    container.classList.add('register-mode');
});

btn_login.addEventListener('click', () => {
    container.classList.remove('register-mode');
});