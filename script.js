function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
document.getElementById("accessRequestForm").addEventListener("submit", function(event) {
    let fullName = document.getElementById("fullName").value;
    let email = document.getElementById("email").value;
    let role = document.getElementById("role").value;
    let organization = document.getElementById("organization").value;
    let reason = document.getElementById("reason").value;

    if (!fullName || !email || !role || !organization || !reason) {
        alert("All fields are required.");
        event.preventDefault();
    }
});
