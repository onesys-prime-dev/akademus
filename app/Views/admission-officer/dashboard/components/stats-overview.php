<div class="stats-overview">
    <?= $this->include('admission-officer/dashboard/components/stats/key-metrics') ?>

    <div class="stats-overview__row">
        <?= $this->include('admission-officer/dashboard/components/stats/passing-rate') ?>
        <?= $this->include('admission-officer/dashboard/components/stats/applicants-per-program') ?>
    </div>

    <div class="stats-overview__row">
        <?= $this->include('admission-officer/dashboard/components/stats/applicants-per-status') ?>
        <?= $this->include('admission-officer/dashboard/components/stats/to-do-list') ?>
    </div>
</div>