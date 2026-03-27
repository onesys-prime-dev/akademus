// public/assets/js/pages/admission-processing-fee.js

'use strict';

// ── Account details per payment method ───────────────────────────────────────
// TODO: Replace with an API call to /payment/method-accounts if you want
//       these to be admin-configurable without a redeploy.
const METHOD_ACCOUNTS = {
    GCASH: {
        label:   'Send payment via GCash',
        number:  '0917 000 0000',
        name:    'Akademus Finance Office',
        placeholder: 'e.g. GCash ref 1234567890',
    },
    MAYA: {
        label:   'Send payment via Maya',
        number:  '0998 111 2222',
        name:    'Akademus Finance Office',
        placeholder: 'e.g. Maya ref 9876543210',
    },
    BDO: {
        label:   'Send payment via BDO',
        number:  '0012 3456 7890',
        name:    'Akademus Finance Office',
        placeholder: 'e.g. BDO ref 1122334455',
    },
    BPI: {
        label:   'Send payment via BPI',
        number:  '9876 5432 1000',
        name:    'Akademus Finance Office',
        placeholder: 'e.g. BPI ref 5544332211',
    },
};

// ── DOM refs ─────────────────────────────────────────────────────────────────
const methodBtns      = document.querySelectorAll('.method-btn');
const accountBoxLabel = document.getElementById('accountBoxLabel');
const accountNumber   = document.getElementById('accountNumber');
const accountName     = document.getElementById('accountName');
const txInput         = document.getElementById('transaction_id');
const uploadBox       = document.getElementById('uploadBox');
const fileInput       = document.getElementById('proofUpload');
const placeholder     = document.getElementById('uploadPlaceholder');
const preview         = document.getElementById('uploadPreview');
const fileNameEl      = document.getElementById('uploadedFileName');
const removeBtn       = document.getElementById('removeFile');

// ── Switch payment method ─────────────────────────────────────────────────────
function switchMethod(code) {
    const details = METHOD_ACCOUNTS[code];
    if (!details) return;

    // Update account info box
    accountBoxLabel.textContent = details.label;
    accountNumber.textContent   = details.number;
    accountName.textContent     = details.name;

    // Update transaction input placeholder
    if (txInput) txInput.placeholder = details.placeholder;
}

methodBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        methodBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        switchMethod(btn.dataset.method);
    });
});

// ── File upload ───────────────────────────────────────────────────────────────
function showPreview(file) {
    fileNameEl.textContent = file.name;
    placeholder.style.display = 'none';
    preview.style.display     = 'flex';
    uploadBox.classList.add('has-file');
}

function clearPreview() {
    fileInput.value       = '';
    placeholder.style.display = 'flex';
    preview.style.display     = 'none';
    uploadBox.classList.remove('has-file');
}

fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) showPreview(fileInput.files[0]);
});

removeBtn.addEventListener('click', clearPreview);

// Drag & drop
uploadBox.addEventListener('dragover', e => {
    e.preventDefault();
    uploadBox.classList.add('drag-over');
});

uploadBox.addEventListener('dragleave', () => {
    uploadBox.classList.remove('drag-over');
});

uploadBox.addEventListener('drop', e => {
    e.preventDefault();
    uploadBox.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (!file) return;

    // Assign to file input
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;

    showPreview(file);
});

// ── Copy buttons ──────────────────────────────────────────────────────────────
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        const text     = document.getElementById(targetId)?.textContent?.trim();
        if (!text) return;

        navigator.clipboard.writeText(text).then(() => {
            btn.textContent = '✓ Copied';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = 'Copy';
                btn.classList.remove('copied');
            }, 1800);
        });
    });
});

// ── Init ──────────────────────────────────────────────────────────────────────
switchMethod('GCASH');