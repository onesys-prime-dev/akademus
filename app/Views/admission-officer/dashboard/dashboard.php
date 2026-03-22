<div class="dashboard">
    <?= $this->include('admission-officer/dashboard/components/top-controls') ?>
    
    <?= $this->include('admission-officer/dashboard/components/table') ?>

    <?= $this->include('admission-officer/dashboard/components/bulk-actions') ?>
</div>

<script>
window.applicantsConfig = <?= json_encode([
    "baseUrl"=>base_url(),
]) ?>;
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/main.js') ?>?v=<?= time() ?>"></script>