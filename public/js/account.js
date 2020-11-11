// copy fields in credential account
function copyText(text) {
    const tmp = document.createElement('textarea');
    tmp.value = text;
    document.body.appendChild(tmp);
    tmp.select();
    document.execCommand('copy');
    document.body.removeChild(tmp);
}

function focusPassword() {
    const password = document.getElementById('password');
    password.setAttribute('type', 'text');
}

function blurPassword() {
    const password = document.getElementById('password');
    password.setAttribute('type', 'password');
}