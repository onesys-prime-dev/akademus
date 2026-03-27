<div class="applicants">
    <?= $this->include('admission-officer/applicants/components/top-controls') ?>
    
    <?= $this->include('admission-officer/applicants/components/table') ?>

    <?= $this->include('admission-officer/applicants/components/bulk-actions') ?>
</div>

<script>
window.applicantsConfig = <?= json_encode([
    "baseUrl"=>base_url(),
]) ?>;
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/applicants/main.js') ?>?v=<?= time() ?>"></script>