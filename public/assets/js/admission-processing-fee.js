// assets/js/admission-processing-fee.js

let selectedMethodId  = null;
let selectedAccountId = null;

// ── Method tab selection ──────────────────────────────────────────
function selectMethod(btn) {
    document.querySelectorAll('.apf-method-btn')
            .forEach(b => b.classList.remove('apf-method-btn--active'));

    btn.classList.add('apf-method-btn--active');

    selectedMethodId  = btn.dataset.methodId  || null;
    selectedAccountId = btn.dataset.accountId || null;

    const method = PAYMENT_DETAILS.find(m => m.payment_method_id == selectedMethodId);

    if (!method) return;

    document.getElementById('methodLabel').textContent =
        method.label || ('Send payment via ' + method.name);

    // Show '–' when the method has no linked account (e.g. Cash, Bank Transfer)
    document.getElementById('infoAccountNumber').textContent =
        method.account_number || '–';

    document.getElementById('infoAccountName').textContent =
        method.account_name || '–';
}

// Initialise with the first button on page load
document.addEventListener('DOMContentLoaded', function () {
    const firstBtn = document.querySelector('.apf-method-btn');
    if (firstBtn) selectMethod(firstBtn);
});

// drag & drop
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('uploadZone').classList.add('apf-upload-zone--active');
}

function handleDragLeave() {
    document.getElementById('uploadZone').classList.remove('apf-upload-zone--active');
}

function handleDrop(e) {
    e.preventDefault();
    handleDragLeave();
    const file = e.dataTransfer.files[0];
    if (file) showPreview(file);
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) showPreview(file);
}

function showPreview(file) {
    document.getElementById('previewIcon').textContent     = file.type.startsWith('image/') ? '🖼️' : '📄';
    document.getElementById('previewFileName').textContent = file.name;
    document.getElementById('previewFileSize').textContent = formatBytes(file.size);
    document.getElementById('uploadIdle').style.display    = 'none';
    document.getElementById('uploadPreview').style.display = 'flex';
}

function removeFile(e) {
    e.stopPropagation();
    document.getElementById('proofFile').value             = '';
    document.getElementById('uploadIdle').style.display    = 'flex';
    document.getElementById('uploadPreview').style.display = 'none';
}

function formatBytes(bytes) {
    if (bytes < 1024)        return bytes + ' B';
    if (bytes < 1048576)     return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

// Error display helper 
function showError(msg) {
    const box = document.getElementById('clientError');
    document.getElementById('clientErrorMsg').textContent = msg;
    box.style.display = 'flex';
    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function clearError() {
    document.getElementById('clientError').style.display = 'none';
}

// submit
async function submitPayment() {
    clearError();

    const ref      = document.getElementById('transactionRef').value.trim();
    const file     = document.getElementById('proofFile').files[0];
    const btn      = document.getElementById('submitBtn');
    const btnLabel = btn.innerHTML; // save original label for restore on error

    // client-side pre-checks (server re-validates everything anyway)
    if (!selectedMethodId) {
        showError('Please select a payment method.');
        return;
    }
    if (!ref) {
        showError('Please enter the transaction / reference number.');
        document.getElementById('transactionRef').focus();
        return;
    }
    if (!file) {
        showError('Please upload your proof of payment.');
        return;
    }

    // Loading state
    btn.disabled  = true;
    btn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             style="animation:spin .8s linear infinite">
            <polyline points="23 4 23 10 17 10"/>
            <path d="M20.49 15a9 9 0 1 1-.49-4.5"/>
        </svg>
        Submitting…`;

    const formData = new FormData();
    formData.append('payment_method_id',         selectedMethodId);
    formData.append('payment_method_account_id', selectedAccountId ?? '');
    formData.append('reference_num',             ref);
    formData.append('proof_of_payment',          file);

    try {
        const res  = await fetch('/admission-processing-fee/submit', {
            method:  'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body:    formData,
        });

        const data = await res.json();

        if (data.success) {
            // Reload so the view re-renders with the existing_payment
            // summary block and the form locked (status = PENDING)
            location.reload();
        } else {
            showError(data.message || 'Something went wrong. Please try again.');
            btn.disabled  = false;
            btn.innerHTML = btnLabel;
        }

    } catch (err) {
        // Network failure or non-JSON response
        showError('Network error. Please check your connection and try again.');
        btn.disabled  = false;
        btn.innerHTML = btnLabel;
    }
}