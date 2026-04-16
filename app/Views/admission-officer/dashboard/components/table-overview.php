<div class="table-overview card">
    <span class="table-overview__header">Recent Applicants</span>

    <div class="table-overview__body">
        <?= $this->include('admission-officer/dashboard/components/table') ?>
    </div>

    <div class="table-overview__footer">
        <button class="table-overview__footer--btn btn-primary">Review Applicants</button>
    </div>
</div>

<script>
    document.querySelector('.table-overview__footer--btn').addEventListener('click', function() {
    window.location.href = '<?= base_url('admission-officer/applicants') ?>';
});
</script>