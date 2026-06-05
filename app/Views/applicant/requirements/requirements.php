<!-- dashboard/pages/requirements.php  -->

<div class="requirements">

    <?= $this->include('applicant/requirements/components/progress') ?>

    <section class="requirements__content card">

        <div id="requirements-body" class="requirements__body"></div>

        <?= $this->include('applicant/requirements/components/actions') ?>

    </section>

</div>

<!-- Document Viewer Modal -->
<div id="document-modal" class="modal-overlay hidden" onclick="closeDocumentModal(event)">
    <div class="modal-box doc-modal-box">
        <div class="doc-modal-header">
            <span id="doc-modal-title">Document Preview</span>
            <button class="doc-modal-close" onclick="document.getElementById('document-modal').classList.add('hidden')">&times;</button>
        </div>
        <div class="doc-modal-body">
            <!-- Image viewer -->
            <img id="doc-modal-img" src="" class="hidden" alt="Document Preview">
            <!-- PDF viewer -->
            <iframe id="doc-modal-frame" src="" class="hidden" frameborder="0"></iframe>
            <!-- Unsupported file type (DOCX, etc.) -->
            <div id="doc-modal-download" class="doc-modal-unsupported hidden">
                <p>This file type cannot be previewed.</p>
                <a id="doc-modal-download-btn" href="#" class="btn-primary">Download File</a>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="req-confirm-modal" class="req-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="req-modal-title" hidden>
    <div class="req-modal">
        <div class="req-modal__icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
        <h3 id="req-modal-title" class="req-modal__title">Submit Documents?</h3>
        <p class="req-modal__body">
            Please make sure all your documents are correct and complete before submitting.
            <strong>Once submitted, your documents will be sent to the admissions office for review.</strong>
        </p>
        <div class="req-modal__actions">
            <button type="button" id="req-modal-cancel" class="req-modal__btn req-modal__btn--cancel">Go Back &amp; Review</button>
            <button type="button" id="req-modal-confirm" class="req-modal__btn req-modal__btn--confirm">Yes, Submit Now</button>
        </div>
    </div>
</div>

<style>
.req-modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex; align-items: center; justify-content: center;
    z-index: 9999;
    padding: 16px;
}
.req-modal-backdrop[hidden] { display: none; }

.req-modal {
    background: #fff;
    border-radius: calc(var(--border-radius, 8px) * 1.2);
    padding: 36px 32px 28px;
    max-width: 420px; width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    animation: req-modal-in 0.22s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes req-modal-in {
    from { opacity: 0; transform: scale(0.9) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.req-modal__icon {
    width: 56px; height: 56px;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px;
    color: var(--primary);
}

.req-modal__title {
    font-size: 18px; font-weight: 700;
    color: #1a202c; margin: 0 0 10px;
}

.req-modal__body {
    font-size: 13.5px; color: #4a5568;
    line-height: 1.6; margin: 0 0 24px;
}

.req-modal__actions {
    display: flex; gap: 10px; justify-content: center;
    flex-wrap: wrap;
}

.req-modal__btn {
    padding: 10px 22px; border-radius: calc(var(--border-radius, 8px) * 0.8);
    font-size: 13.5px; font-weight: 600; cursor: pointer;
    border: none; transition: background 0.15s, transform 0.1s;
    font-family: var(--font, inherit);
}
.req-modal__btn:active { transform: scale(0.97); }

.req-modal__btn--cancel {
    background: #edf2f7; color: #4a5568;
}
.req-modal__btn--cancel:hover { background: #e2e8f0; }

.req-modal__btn--confirm {
    background: var(--primary); color: #fff;
}
.req-modal__btn--confirm:hover  { background: var(--primary-dark); }
.req-modal__btn--confirm:disabled { opacity: 0.6; cursor: not-allowed; }
</style>

<!-- Submit Success Modal -->
<div id="submit-modal" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-icon">✓</div>
        <h3>Documents Submitted!</h3>
        <p>You have successfully submitted all your required documents. Your application is now under review.</p>
        <a href="<?= base_url('dashboard') ?>" class="btn-primary modal-dashboard-btn">Go to Dashboard</a>
    </div>
</div>

<!-- Passed / Approved Modal -->
<div id="req-passed-modal" class="req-modal-backdrop <?= $requiredDocsVerified ? '' : 'hidden' ?>" role="dialog" aria-modal="true" aria-labelledby="req-passed-title" <?= $requiredDocsVerified ? '' : 'hidden' ?>>
    <div class="req-modal">
        <div class="req-modal__icon req-modal__icon--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
        <h3 id="req-passed-title" class="req-modal__title">Application Approved!</h3>
        <p class="req-modal__body">
            Congratulations! You have successfully passed all the required steps.
            Your application has been reviewed and <strong>approved</strong> by the admissions office.
        </p>
        <div class="req-modal__actions">
            <button type="button" id="req-passed-close" class="req-modal__btn req-modal__btn--confirm">Got it, thanks!</button>
        </div>
    </div>
</div>

<style>
.req-modal__icon--success {
    background: color-mix(in srgb, #16a34a 12%, transparent);
    color: #16a34a;
}
</style>

<script>
window.requirementsConfig = <?= json_encode([
    "initialStep"         => $initial_step ?? 1,
    "maxSteps"            => $max_steps ?? 5,
    "baseUrl"             => base_url(),
    "stepUrl"             => base_url('requirements/step/'),
    "updateUrl"           => base_url('requirements/update/'),
    "submitUrl"           => base_url('requirements/submit'),
    "documentUrl"         => base_url('requirements/document/'),
    "dashboardUrl"        => base_url('dashboard'),
    "applicationStatus"   => $application_status ?? '',
]) ?>;
</script>

<script src="<?= base_url('assets/js/pages/requirements.js') . '?v=' . time(); ?>"></script>