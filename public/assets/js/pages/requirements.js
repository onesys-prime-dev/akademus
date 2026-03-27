// assets/js/pages/applicant-form.js

const { initialStep, maxSteps, baseUrl, stepUrl, updateUrl, submitUrl, documentUrl, dashboardUrl, applicationStatus } = window.requirementsConfig;

let currentStep = initialStep;

const formProgressH4Text = document.getElementById('form-progress-text');
const backBtn = document.getElementById('btn-back');
const saveAndContinueBtn = document.getElementById('btn-save');
const submitFormBtn = document.getElementById('btn-submit');
const formFillupContainer = document.getElementById('requirements-body');
const autosaveText = document.getElementById('autosave-text');

let autoSaveTimeout;

function openDocumentModal(reqId, docName) {
    const modal       = document.getElementById('document-modal');
    const frame       = document.getElementById('doc-modal-frame');
    const img         = document.getElementById('doc-modal-img');
    const downloadRow = document.getElementById('doc-modal-download');
    const downloadBtn = document.getElementById('doc-modal-download-btn');
    const title       = document.getElementById('doc-modal-title');

    const ext = (docName || '').split('.').pop().toLowerCase();
    const url = `${documentUrl}${reqId}`;
    const isImage = ['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(ext);
    const isPdf   = ext === 'pdf';

    // Reset all viewers
    frame.classList.add('hidden');       frame.src = '';
    img.classList.add('hidden');         img.src = '';
    downloadRow.classList.add('hidden');

    title.textContent = docName || 'Document Preview';

    if (isImage) {
        img.src = url;
        img.classList.remove('hidden');
    } else if (isPdf) {
        frame.src = url;
        frame.classList.remove('hidden');
    } else {
        // DOCX or other — offer download
        downloadBtn.href = url;
        downloadBtn.download = docName || 'document';
        downloadRow.classList.remove('hidden');
    }

    modal.classList.remove('hidden');
}

function closeDocumentModal(e) {
    if (e.target === document.getElementById('document-modal')) {
        document.getElementById('document-modal').classList.add('hidden');
        document.getElementById('doc-modal-frame').src = '';
        document.getElementById('doc-modal-img').src   = '';
    }
}

async function loadForm(step) {
    try {
        const response = await fetch(`${stepUrl}${step}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();

        document.title = "Akademus | " + data.title;
        formProgressH4Text.textContent = `Form Progress — Step ${step} of ${maxSteps}`;
        formFillupContainer.innerHTML = data.form;

        if (step === maxSteps && data.allRequiredComplete) {
            markAllComplete();
        } else {
            updateProgress(step);
        }

        // Populate all fields including dynamic selects
        populateForm(`requirements-${step}`, data.formData);

        updateUploadCounter(`requirements-${step}`, data.formData);

        setupAutoSave(step);

        history.replaceState(null, '', `${stepUrl}${step}`);

    } catch (error) {
        console.error("Load form error:", error);
    }
}

function populateForm(formId, data) {
    const form = document.getElementById(formId);
    if (!form || !data) return;

    Object.keys(data).forEach(requirementId => {

        const field = form.querySelector(`#requirement_${requirementId}`);
        if (!field) return;

        // Remove required so validation passes on review step
        field.removeAttribute('required');

        const label = form.querySelector(`label[for="requirement_${requirementId}"]`);
        if (!label) return;

        label.classList.add('upload-success', 'uploaded');

        const btn = label.querySelector('.requirement-upload-action');
        if (btn) {
            btn.textContent = "Uploaded ✔";
            btn.classList.add('uploaded-btn');
        }

        // Show the View button with the correct URL and file name
        const viewBtn = label.querySelector('.requirement-view-action');
        if (viewBtn) {
            viewBtn.dataset.docName = data[requirementId]?.document_name ?? '';
            viewBtn.classList.remove('hidden');
        }
    });
}

async function processForm(step) {
    const form = document.getElementById(`requirements-${step}`);
    if (!form) return;

    const formData = new FormData(form);

    try {
        const response = await fetch(`${updateUrl}${step}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();

        await loadForm(step);

        return data.status === 'success';

    } catch (error) {
        console.error('Form submission error:', error);
        alert('An error occurred while saving.');
        return false;
    }
}

function updateUploadCounter(formId, formData) {
    const counter = document.querySelector('.upload-counter');
    if (!counter) return;

    const form = document.getElementById(formId);
    if (!form) return;

    const requiredInputs = form.querySelectorAll('input[type="file"][required]');
    const totalRequired = requiredInputs.length;

    if (totalRequired === 0) {
        counter.style.display = 'none';
        return;
    }

    // Count uploaded required docs from formData
    let uploadedRequired = 0;
    requiredInputs.forEach(input => {
        const reqId = input.id.replace('requirement_', '');
        if (formData && formData[reqId]) uploadedRequired++;
    });

    counter.textContent = `${uploadedRequired}/${totalRequired} required uploaded`;
    counter.classList.toggle('upload-counter--done', uploadedRequired >= totalRequired);
}

function setupAutoSave(step) {
    const form = document.getElementById(`requirements-${step}`);
    if (!form) return;

    form.querySelectorAll('input[type="file"]').forEach(field => {
        field.removeEventListener('change', autoSaveHandler);
        field.addEventListener('change', autoSaveHandler);
    });
}

async function autoSaveHandler() {
    if (!autosaveText) return;

    if (autoSaveTimeout) clearTimeout(autoSaveTimeout);

    // Show "Saving..." while waiting for debounce
    autosaveText.textContent = 'Saving...';

    // Wait 1 second after last change before saving
    autoSaveTimeout = setTimeout(async () => {
        const success = await processForm(currentStep);

        if (success) {
            autosaveText.textContent = 'Auto-saved changes';
            // loadForm (called inside processForm) already updates the counter
        } else {
            autosaveText.textContent = 'Auto-save failed';
        }
    }, 1000);
}

function checkAllFields(step) {
    const form = document.getElementById(`requirements-${step}`);
    if (!form) return false;

    let isValid = true;

    // Remove previous error highlights
    form.querySelectorAll('.input-error').forEach(el => {
        el.classList.remove('input-error');
    });

    // Get all required inputs, selects, and textareas
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (field.type === 'file') {
            if (field.files.length === 0) {
                isValid = false;
                field.classList.add('input-error');
            }
        }
    });

    if (!isValid) {
        alert('Please fill out all required fields before proceeding.');
    }

    return isValid;
}

function updateProgress(step) {
    const circles = document.querySelectorAll('.form-progress__circle');
    const lines = document.querySelectorAll('.form-progress__line');

    circles.forEach(circle => {
        const num = Number(circle.dataset.step);
        circle.classList.toggle('form-progress__circle-done', num < step);
        circle.classList.toggle('form-progress__circle-active', num === step);
        circle.textContent = num < step ? '✔' : num;
    });

    lines.forEach(line => {
        const num = Number(line.dataset.step);
        line.classList.toggle('form-progress__line-done', num < step);
    });
}

function markAllComplete() {
    const circles = document.querySelectorAll('.form-progress__circle');
    const lines = document.querySelectorAll('.form-progress__line');

    circles.forEach(circle => {
        circle.classList.add('form-progress__circle-done');
        circle.classList.remove('form-progress__circle-active');
        circle.textContent = '✔';
    });

    lines.forEach(line => {
        line.classList.add('form-progress__line-done');
        line.classList.remove('form-progress__line-active');
    });
}

function updateButtons() {
    backBtn.classList.toggle('hidden', currentStep === 1);
    saveAndContinueBtn.classList.toggle('hidden', currentStep === maxSteps);
    submitFormBtn.classList.toggle('hidden', currentStep !== maxSteps);
}

// ── Passed / Approved modal ───────────────────────────────────────────────
const reqPassedModal = document.getElementById('req-passed-modal');
const reqPassedClose = document.getElementById('req-passed-close');

function openPassedModal() {
    reqPassedModal.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';
    reqPassedClose.focus();
}

function closePassedModal() {
    reqPassedModal.setAttribute('hidden', '');
    document.body.style.overflow = '';
}

reqPassedClose.addEventListener('click', closePassedModal);
reqPassedModal.addEventListener('click', e => { if (e.target === reqPassedModal) closePassedModal(); });
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !reqPassedModal.hasAttribute('hidden')) closePassedModal();
});

document.addEventListener('DOMContentLoaded', async () => {
    await loadForm(currentStep);
    updateButtons();

    if (applicationStatus === 'approved') {
        openPassedModal();
    }
});

// Buttons
saveAndContinueBtn.addEventListener('click', async () => {
    autosaveText.textContent = 'Saving...';

    // Upload any selected files before advancing
    const success = await processForm(currentStep);
    if (!success) return;

    if (currentStep < maxSteps) {
        currentStep++;
        await loadForm(currentStep);
        updateButtons();
        autosaveText.textContent = 'Auto-saved changes';
        setTimeout(() => { autosaveText.textContent = ''; }, 3000);
    }
});
// ── Submit confirmation modal ─────────────────────────────────────────────
const reqConfirmModal  = document.getElementById('req-confirm-modal');
const reqModalCancel   = document.getElementById('req-modal-cancel');
const reqModalConfirm  = document.getElementById('req-modal-confirm');

function openReqModal() {
    reqConfirmModal.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';
    reqModalConfirm.focus();
}

function closeReqModal() {
    reqConfirmModal.setAttribute('hidden', '');
    document.body.style.overflow = '';
}

reqConfirmModal.addEventListener('click', e => { if (e.target === reqConfirmModal) closeReqModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && !reqConfirmModal.hasAttribute('hidden')) closeReqModal(); });
reqModalCancel.addEventListener('click', closeReqModal);

reqModalConfirm.addEventListener('click', async () => {
    reqModalConfirm.disabled = true;
    reqModalConfirm.textContent = 'Submitting…';

    try {
        const response = await fetch(submitUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token_name"]')?.value ?? ''
            }
        });

        const data = await response.json();

        if (data.status === 'success') {
            closeReqModal();
            document.getElementById('submit-modal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Submit error:', error);
    } finally {
        reqModalConfirm.disabled = false;
        reqModalConfirm.textContent = 'Yes, Submit Now';
    }
});

submitFormBtn.addEventListener('click', () => {
    if (!checkAllFields(currentStep)) return;
    openReqModal();
});
backBtn.addEventListener('click', async () => {
    autosaveText.textContent = 'Saving...';

    if (currentStep > 1) {
        currentStep--;
        await loadForm(currentStep);
        updateButtons();

        autosaveText.textContent = 'Auto-saved changes';
    }
});