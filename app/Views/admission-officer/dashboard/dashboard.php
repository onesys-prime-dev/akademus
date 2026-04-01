<div class="dashboard">

    <?= $this->include('admission-officer/dashboard/components/top-controls') ?>

    <div class="dashboard__section--middle">
        <?= $this->include('admission-officer/dashboard/components/stats-overview') ?>
        <?= $this->include('admission-officer/dashboard/components/calendar-overview') ?>
    </div>

    <?= $this->include('admission-officer/dashboard/components/table-overview') ?>
    
</div>

<script>
window.dashboardConfig = <?= json_encode([
    'baseUrl'    => base_url(),
]) ?>;
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/dashboard/main.js') ?>?v=<?= time() ?>"></script>