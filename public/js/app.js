const togglePassword = document.getElementById('togglePassword');
const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');

[togglePassword, togglePasswordConfirmation].forEach(toggle => {
    if (toggle) { // ✅ null check
        toggle.addEventListener('click', function () {
            const input = this.previousElementSibling;
            if (!input) return; // agar sibling na ho toh kuch na karo
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.querySelector('i')?.classList.toggle('bi-eye');
            this.querySelector('i')?.classList.toggle('bi-eye-slash');
        });
    }
});
