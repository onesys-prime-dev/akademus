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

<!-- Submit Success Modal -->
<div id="submit-modal" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-icon">✓</div>
        <h3>Documents Submitted!</h3>
        <p>You have successfully submitted all your required documents. Your application is now under review.</p>
        <a href="<?= base_url('dashboard') ?>" class="btn-primary modal-dashboard-btn">Go to Dashboard</a>
    </div>
</div>

<script>
window.requirementsConfig = <?= json_encode([
    "initialStep"  => $initial_step ?? 1,
    "maxSteps"     => $max_steps ?? 5,
    "baseUrl"      => base_url(),
    "stepUrl"      => base_url('requirements/step/'),
    "updateUrl"    => base_url('requirements/update/'),
    "submitUrl"    => base_url('requirements/submit'),
    "documentUrl"  => base_url('requirements/document/'),
    "dashboardUrl" => base_url('dashboard'),
]) ?>;
</script>

<script src="<?= base_url('assets/js/pages/requirements.js') . '?v=' . time(); ?>"></script>