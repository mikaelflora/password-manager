function openModal() {
    return false;
}

function closeModal() {
    const form = document.getElementById('form');
    form.onsubmit = sendForm();
    form.submit();
    form.onsubmit = openModal();

    return true;
}

function sendForm() {
    return true;
}