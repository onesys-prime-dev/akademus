<?php

$initialState = [
    'month'      => $selectedMonth ?? (int) date('n'),
    'year'       => $selectedYear ?? (int) date('Y'),
    'activeView' => $activeView ?? 'calendar',
];
?>

<div class="exam-schedule">
    <div class="exam-schedule__top-actions">
        <?= $this->include('admission-officer/exam-schedule/components/toggle-view') ?>
        <?= $this->include('admission-officer/exam-schedule/components/date-picker') ?>
    </div>

    <?= $this->include('admission-officer/exam-schedule/components/month-card') ?>

    <div class="exam-schedule__content-container card" id="exam-schedule__content-container" data-content-container>
        <?= $initialContent ?? '' ?>
    </div>

    <?= $this->include('admission-officer/exam-schedule/components/modals/index') ?>
</div>

<script>
window.examScheduleConfig = <?= json_encode([
    'baseUrl'    => base_url(),
    'month'      => $initialState['month'],
    'year'       => $initialState['year'],
    'activeView' => $initialState['activeView'],
    'contentUrl' => base_url('admission-officer/exam-schedule/content'),
]) ?>;
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/exam-schedule/main.js') ?>?v=<?= time() ?>"></script>