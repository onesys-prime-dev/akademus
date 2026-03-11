document.addEventListener('keydown', function (e) {
    if (e.target.id !== 'contact_number') return;

    const allowedKeys = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
    const isDigit = /^[0-9]$/.test(e.key);
    const isPlus = e.key === '+' && e.target.value.length === 0;
    const isAllowedKey = allowedKeys.includes(e.key);
    const isCtrlCmd = e.ctrlKey || e.metaKey;

    if (!isDigit && !isPlus && !isAllowedKey && !isCtrlCmd) {
        e.preventDefault();
        showContactError('Digits only. No letters or special characters allowed.');
    }
});

document.addEventListener('keyup', function (e) {
    if (e.target.id !== 'contact_number') return;
    clearContactError();
});

document.addEventListener('paste', function (e) {
    if (e.target.id !== 'contact_number') return;

    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    const cleaned = pasted.replace(/[^0-9+]/g, '').replace(/(?!^)\+/g, '').slice(0, 15);
    e.target.value = cleaned;

    if (pasted !== cleaned) {
        showContactError('Invalid characters were removed.');
    } else {
        clearContactError();
    }
});

function showContactError(message) {
    const error = document.getElementById('contact-error');
    if (error) {
        error.textContent = message;
        error.classList.add('show');
    }
}

function clearContactError() {
    const error = document.getElementById('contact-error');
    if (error) {
        error.textContent = '';
        error.classList.remove('show');
    }
}