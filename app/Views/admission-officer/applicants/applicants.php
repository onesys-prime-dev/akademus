<div class="applicants">
    <?= $this->include('admission-officer/applicants/components/top-controls') ?>
    
    <?= $this->include('admission-officer/applicants/components/table') ?>

    <?= $this->include('admission-officer/applicants/components/bulk-actions') ?>
</div>


<!-- Bulk Decision Modal -->
<div class="decision-modal-overlay" id="bulk-modal-overlay">
    <div class="decision-modal">

        <!-- Header -->
        <div class="decision-modal__header" id="bulk-modal-header">
            <div class="decision-modal__header-icon" id="bulk-modal-icon"></div>

            <div>
                <h3 class="decision-modal__title" id="bulk-modal-title">
                    Bulk Approve Applicants
                </h3>
                <p class="decision-modal__subtitle" id="bulk-modal-subtitle">
                    0 applicants selected
                </p>
            </div>

            <button class="decision-modal__close" id="bulk-modal-close">
                &times;
            </button>
        </div>

        <!-- Body -->
        <div class="decision-modal__body">

            <div class="decision-modal__section-label">
                Selected applicants
            </div>

            <div class="bulk-modal-list" id="bulk-modal-list"></div>

        </div>

        <!-- Footer -->
        <div class="decision-modal__footer">
            <button class="decision-modal__cancel" id="bulk-modal-cancel">
                Cancel
            </button>

            <button class="decision-modal__submit" id="bulk-modal-submit">
                Confirm
            </button>
        </div>

    </div>
</div>

</div>


<script>
window.applicantsConfig = <?= json_encode([
    "baseUrl"=>base_url(),
]) ?>;
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/applicants/main.js') ?>?v=<?= time() ?>"></script>