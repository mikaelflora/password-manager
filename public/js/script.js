const passwordField = document.getElementById('credentials_password');
const passwordEyeField = document.getElementById('credentials_password_visibility');
const passwordEyeIcon = document.getElementById('credentials_password_icon');

passwordEyeField.addEventListener('click', function (event) {
    event.preventDefault();
    if (passwordField.getAttribute('type') === 'password') {
        passwordField.setAttribute('type', 'text');
        passwordEyeIcon.classList.remove( "fa-eye-slash" );
        passwordEyeIcon.classList.add( "fa-eye" );
    } else if (passwordField.getAttribute('type') === 'text') {
        passwordField.setAttribute('type', 'password');
        passwordEyeIcon.classList.add( "fa-eye-slash" );
        passwordEyeIcon.classList.remove( "fa-eye" );
    }
});