<?php
// applicant/applicant-form/applicant-form.php
$isSubmittedPHP = !empty($is_submitted);
?>

<!-- Confirmation Modal -->
<div id="submit-confirm-modal" class="af-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="af-modal-title" hidden>
    <div class="af-modal">
        <div class="af-modal__icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
        <h3 id="af-modal-title" class="af-modal__title">Submit Application?</h3>
        <p class="af-modal__body">
            Please make sure all your details are correct before submitting.
            <strong>Once submitted, you will no longer be able to edit your application.</strong>
        </p>
        <div class="af-modal__actions">
            <button type="button" id="modal-cancel-btn" class="af-modal__btn af-modal__btn--cancel">Go Back &amp; Review</button>
            <button type="button" id="modal-confirm-btn" class="af-modal__btn af-modal__btn--confirm">Yes, Submit Now</button>
        </div>
    </div>
</div>

<div class="applicant-form-container">

    <div class="applicant-form-progress-container">
        <h4 id="form-progress-h4-text"></h4>

        <div id="progress-timeline-container" class="progress-timeline-container">
            <?php
            helper('form_field');
            $enabledPages = get_enabled_pages();
            $totalPages   = count($enabledPages);
            foreach ($enabledPages as $i => $pg):
                $stepNum = $i + 1;
                $isLast  = ($stepNum === $totalPages);
            ?>
            <div class="progress-box">
                <div class="progress-circle" data-step="<?= $stepNum ?>">
                    <?= $stepNum ?>
                </div>
                <span><?= esc(mb_strimwidth($pg['title'], 0, 10, '…')) ?></span>
            </div>
            <?php if (!$isLast): ?>
            <div class="progress-line" data-step="<?= $stepNum ?>"></div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>


    <div class="applicant-form-content-container">
        <div id="form-fillup-container" class="applicant-form-fillup-container"></div>

        <div class="applicant-form-lower-actions">
            <!-- Submitted / view-only bar -->
            <span id="form-readonly-label" class="form-readonly-label" <?= $isSubmittedPHP ? '' : 'hidden' ?>>
                View Only — Application Submitted
            </span>

            <!-- Editable auto-save status -->
            <span id="save-changes-text-ui" <?= $isSubmittedPHP ? 'hidden' : '' ?>>Auto-saved as draft</span>

            <div class="applicant-form-lower-actions-btn">
                <button type="button" id="back-btn"             class="btn-primary hidden">Back</button>
                <button type="button" id="save-and-continue-btn" class="btn-primary hidden">Save &amp; Continue</button>
                <button type="button" id="submit-form"           class="btn-primary hidden">Submit Form</button>
                <button type="button" id="next-btn"              class="btn-primary hidden">Next</button>
                <!-- Shown only while editing a step reopened for revision (submitted app, this step unlocked) -->
                <button type="button" id="revision-submit-btn"   class="btn-primary hidden">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ── State ────────────────────────────────────────────────────────────────
    // Start from server-rendered value; may be updated after each AJAX step load.
    let formIsSubmitted = <?= $isSubmittedPHP ? 'true' : 'false' ?>;

    const formProgressH4Text   = document.getElementById('form-progress-h4-text');
    const backBtn              = document.getElementById('back-btn');
    const saveAndContinueBtn   = document.getElementById('save-and-continue-btn');
    const submitFormBtn        = document.getElementById('submit-form');
    const nextBtn              = document.getElementById('next-btn');
    const formFillupContainer  = document.getElementById('form-fillup-container');
    const formReadonlyLabel    = document.getElementById('form-readonly-label');
    const saveChangesTextUI    = document.getElementById('save-changes-text-ui');
    const revisionSubmitBtn    = document.getElementById('revision-submit-btn');
    const formSubmittedBanner  = document.getElementById('form-submitted-banner');

    // Modal elements
    const submitModal          = document.getElementById('submit-confirm-modal');
    const modalCancelBtn       = document.getElementById('modal-cancel-btn');
    const modalConfirmBtn      = document.getElementById('modal-confirm-btn');

    const enabledPages = <?= json_encode(array_values($enabledPages)) ?>;
    const maxSteps     = enabledPages.length;

    let currentStep    = <?= isset($initial_step) ? (int)$initial_step : 1 ?>;
    let autoSaveTimeout;

    // ── Form loading ─────────────────────────────────────────────────────────
    async function loadForm(step) {
        if (autoSaveTimeout) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = null;
        }

        try {
            const response = await fetch(`<?= base_url('applicant-form/step/') ?>${step}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();

            // ── Trust the server's per-step submitted flag on every load ──────
            // This fixes the stale-cache problem: if someone navigates back via
            // browser history the server response will tell us the real state.
            // Note: `stepIsSubmitted` (this step only) drives the lock decision and
            // the "View Only" label below, since a revision request unlocks individual
            // steps while others stay locked. `formIsSubmitted` is a separate one-way
            // latch used only for Back/Next-only navigation chrome (updateButtons()) —
            // once the applicant has submitted at all, we don't want the Save & Continue/
            // Submit Form wizard buttons to reappear just because one step is editable.
            const stepIsSubmitted = !!data.is_submitted;

            if (stepIsSubmitted && !formIsSubmitted) {
                formIsSubmitted = true;
                if (formSubmittedBanner) formSubmittedBanner.removeAttribute('hidden');
            }

            // The readonly label/autosave text reflects THIS step, not the sticky latch —
            // otherwise a step unlocked for revision would incorrectly still say "View Only".
            if (formReadonlyLabel) formReadonlyLabel.toggleAttribute('hidden', !stepIsSubmitted);
            if (saveChangesTextUI) saveChangesTextUI.toggleAttribute('hidden', stepIsSubmitted);

            // Editing a step reopened for revision on an already-submitted application —
            // show a direct Submit button right here instead of only pointing elsewhere.
            // Note: this button group hides items via the "hidden" CSS class (see
            // updateButtons()), not the HTML hidden attribute, so match that convention.
            if (revisionSubmitBtn) revisionSubmitBtn.classList.toggle('hidden', !(formIsSubmitted && !stepIsSubmitted));

            document.title = "Akademus | " + data.title;
            formProgressH4Text.textContent = `Form Progress — Step ${step} of ${maxSteps}`;
            formFillupContainer.innerHTML  = data.form;

            updateProgress(step, stepIsSubmitted);

            if (document.getElementById('program')) {
                await loadPrograms();
            }

            populateForm(`applicant-form-${step}`, data.formData);

            if (stepIsSubmitted) {
                lockFormFields();
            } else {
                unlockFormFields();
                setupAutoSave(step);
            }

            const base = '<?= base_url() ?>';
            history.replaceState(null, '', `${base}applicant-form/step/${step}`);

        } catch (error) {
            console.error("Load form error:", error);
        }
    }

    function lockFormFields() {
        if (!formFillupContainer) return;
        formFillupContainer.querySelectorAll('input, select, textarea, button[type="submit"]').forEach(el => {
            el.disabled = true;
        });
        formFillupContainer.classList.add('form-locked');
    }

    // Companion to lockFormFields() — needed because a revision request only
    // unlocks specific steps, so a step visited earlier in the same session
    // may have left the container's lock class behind (classList.add() alone
    // never gets undone otherwise, and .form-locked sets pointer-events:none
    // on the whole container regardless of each field's own disabled state).
    function unlockFormFields() {
        if (!formFillupContainer) return;
        formFillupContainer.querySelectorAll('input, select, textarea, button[type="submit"]').forEach(el => {
            el.disabled = false;
        });
        formFillupContainer.classList.remove('form-locked');
    }

    function populateForm(formId, data) {
        const form = document.getElementById(formId);
        if (!form || !data) return;
        Object.keys(data).forEach(key => {
            const value = data[key];
            if (value === null || value === undefined) return;
            const field = form.querySelector(`[name="${key}"]`);
            if (!field) return;
            if (field.type === 'radio') {
                form.querySelectorAll(`[name="${key}"]`).forEach(r => { r.checked = r.value == value; });
            } else if (field.type === 'checkbox') {
                field.checked = !!value;
            } else if (field.tagName === 'SELECT') {
                if (!field.querySelector(`option[value="${value}"]`)) {
                    const observer = new MutationObserver(() => {
                        if (field.querySelector(`option[value="${value}"]`)) { field.value = value; observer.disconnect(); }
                    });
                    observer.observe(field, { childList: true });
                } else {
                    field.value = value;
                }
            } else {
                field.value = value;
            }
        });
    }

    async function loadPrograms() {
        try {
            const response = await fetch(`<?= base_url('get-programs') ?>`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) return;
            const data = await response.json();
            if (!data.programs) return;
            const select = document.getElementById('program');
            if (!select) return;
            select.innerHTML = '<option value="" disabled selected>Select a Program</option>';
            data.programs.forEach(program => {
                const option = document.createElement('option');
                option.value = String(program.id);
                option.textContent = program.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Failed to load programs:', error);
        }
    }

    // silent=true suppresses alert popups (used by auto-save)
    async function processForm(step, action = 'draft', silent = false) {
        const applicantForm = document.getElementById(`applicant-form-${step}`);
        if (!applicantForm) return false;
        const formData = new FormData(applicantForm);
        formData.append('action', action);
        try {
            const response = await fetch(`<?= base_url('applicant-form/update/') ?>${step}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success') return data;
            if (!silent) alert(data.message || 'Something went wrong.');
            return false;
        } catch (error) {
            console.error('Form submission error:', error);
            if (!silent) alert('An error occurred while saving.');
            return false;
        }
    }

    function setupAutoSave(step) {
        const form = document.getElementById(`applicant-form-${step}`);
        if (!form) return;
        form.querySelectorAll('[name]').forEach(field => {
            field.removeEventListener('input', autoSaveHandler);
            field.removeEventListener('change', autoSaveHandler);
            field.addEventListener('input', autoSaveHandler);
            field.addEventListener('change', autoSaveHandler);
        });
    }

    async function autoSaveHandler() {
        const saveTextUI = document.getElementById('save-changes-text-ui');
        if (!saveTextUI) return;
        if (autoSaveTimeout) clearTimeout(autoSaveTimeout);
        saveTextUI.textContent = 'Saving...';
        autoSaveTimeout = setTimeout(async () => {
            const result = await processForm(currentStep, 'draft', true);
            autoSaveTimeout = null;
            if (!saveTextUI) return;
            saveTextUI.textContent = result ? 'Auto-saved' : 'Auto-save failed';
            if (result) setTimeout(() => { if (saveTextUI) saveTextUI.textContent = ''; }, 3000);
        }, 1000);
    }

    function checkAllFields(step) {
        const form = document.getElementById(`applicant-form-${step}`);
        if (!form) return false;
        let isValid = true;
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            let fieldValid = true;
            if (field.type === 'radio') {
                const group   = form.querySelectorAll(`input[name="${field.name}"]`);
                const checked = [...group].some(r => r.checked);
                if (!checked) {
                    fieldValid = false;
                    group.forEach(r => r.closest('label')?.classList.add('input-error'));
                }
            } else if (field.type === 'checkbox') {
                if (!field.checked) fieldValid = false;
            } else if (field.tagName === 'SELECT') {
                if (!field.value) fieldValid = false;
            } else {
                if (!field.value.trim()) fieldValid = false;
            }
            if (!fieldValid) {
                isValid = false;
                if (field.type !== 'radio') field.classList.add('input-error');
            }
        });
        if (!isValid) alert('Please fill out all required fields before proceeding.');
        return isValid;
    }

    function updateProgress(step, stepIsSubmitted) {
        document.querySelectorAll('.progress-circle').forEach(circle => {
            const num = +circle.dataset.step;
            // The step currently being viewed uses its OWN lock state (so a step
            // unlocked for revision shows "active", not just a plain done checkmark),
            // while every other step still reflects the overall submitted state.
            const done = num === step ? stepIsSubmitted : (formIsSubmitted || num < step);
            circle.classList.toggle('progress-circle-done',   done);
            circle.classList.toggle('progress-circle-active', !done && num === step);
            circle.textContent = done ? '✓' : String(num);
        });
        document.querySelectorAll('.progress-line').forEach(line => {
            const num = +line.dataset.step + 1;
            line.classList.toggle('progress-line-done',   formIsSubmitted || num < step);
            line.classList.toggle('progress-line-active', !formIsSubmitted && num === step);
        });
    }

    function updateButtons() {
        if (formIsSubmitted) {
            if (backBtn)            { backBtn.classList.toggle('hidden', currentStep === 1); }
            if (nextBtn)            { nextBtn.classList.toggle('hidden', currentStep === maxSteps); }
            if (saveAndContinueBtn) { saveAndContinueBtn.classList.add('hidden'); }
            if (submitFormBtn)      { submitFormBtn.classList.add('hidden'); }
        } else {
            if (backBtn)            { backBtn.classList.toggle('hidden', currentStep === 1); }
            if (nextBtn)            { nextBtn.classList.add('hidden'); }
            if (saveAndContinueBtn) { saveAndContinueBtn.classList.toggle('hidden', currentStep === maxSteps); }
            if (submitFormBtn)      { submitFormBtn.classList.toggle('hidden', currentStep !== maxSteps); }
        }
    }

    // ── Modal helpers ────────────────────────────────────────────────────────
    function openModal() {
        submitModal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        modalConfirmBtn.focus();
    }

    function closeModal() {
        submitModal.setAttribute('hidden', '');
        document.body.style.overflow = '';
    }

    // Close on backdrop click
    submitModal.addEventListener('click', e => {
        if (e.target === submitModal) closeModal();
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !submitModal.hasAttribute('hidden')) closeModal();
    });

    modalCancelBtn.addEventListener('click', closeModal);

    modalConfirmBtn.addEventListener('click', async () => {
        modalConfirmBtn.disabled = true;
        modalConfirmBtn.textContent = 'Submitting…';

        const success = await processForm(currentStep, 'submit');

        if (success) {
            closeModal();
            window.location.href = "<?= base_url('dashboard') ?>";
        } else {
            modalConfirmBtn.disabled = false;
            modalConfirmBtn.textContent = 'Yes, Submit Now';
        }
    });

    // ── Editable mode buttons ────────────────────────────────────────────────
    if (saveAndContinueBtn) {
        saveAndContinueBtn.addEventListener('click', async () => {
            const valid = checkAllFields(currentStep);
            if (!valid) return;

            const saveTextUI = document.getElementById('save-changes-text-ui');
            if (saveTextUI) saveTextUI.textContent = 'Saving...';

            const result = await processForm(currentStep, 'continue');

            if (result) {
                if (result.reload && currentStep < maxSteps) {
                    const base = '<?= base_url() ?>';
                    window.location.href = `${base}applicant-form/step/${currentStep + 1}`;
                    return;
                }
                if (currentStep < maxSteps) {
                    currentStep++;
                    await loadForm(currentStep);
                    updateButtons();
                    if (saveTextUI) {
                        saveTextUI.textContent = 'Auto-saved changes';
                        setTimeout(() => { if (saveTextUI) saveTextUI.textContent = ''; }, 3000);
                    }
                }
            }
        });
    }

    if (submitFormBtn) {
        submitFormBtn.addEventListener('click', () => {
            if (!checkAllFields(currentStep)) return;
            openModal();
        });
    }

    // ── Revision resubmit button ────────────────────────────────────────────
    // Saves this step non-silently (so a still-invalid value pops up the exact
    // validation error, reusing the same validateSchoolYears()/validateGwa()
    // checks the autosave path already enforces), then finalizes the resubmit.
    if (revisionSubmitBtn) {
        revisionSubmitBtn.addEventListener('click', async () => {
            if (!checkAllFields(currentStep)) return;

            revisionSubmitBtn.disabled = true;
            revisionSubmitBtn.textContent = 'Submitting…';

            const saved = await processForm(currentStep, 'draft', false);

            if (!saved) {
                revisionSubmitBtn.disabled = false;
                revisionSubmitBtn.textContent = 'Submit';
                return;
            }

            try {
                const res  = await fetch('<?= base_url('applicant-form/resubmit') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (data.status === 'success') {
                    window.location.href = '<?= base_url('tracking-progress') ?>';
                } else {
                    alert(data.message || 'Something went wrong.');
                    revisionSubmitBtn.disabled = false;
                    revisionSubmitBtn.textContent = 'Submit';
                }
            } catch (e) {
                alert('Network error. Please try again.');
                revisionSubmitBtn.disabled = false;
                revisionSubmitBtn.textContent = 'Submit';
            }
        });
    }

    // ── Navigation buttons (both modes) ─────────────────────────────────────
    if (backBtn) {
        backBtn.addEventListener('click', async () => {
            if (currentStep > 1) {
                currentStep--;
                await loadForm(currentStep);
                updateButtons();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', async () => {
            if (currentStep < maxSteps) {
                currentStep++;
                await loadForm(currentStep);
                updateButtons();
            }
        });
    }

    // ── Init ─────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async () => {
        await loadForm(currentStep);
        updateButtons();
    });
</script>
