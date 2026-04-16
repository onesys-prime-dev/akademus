<?php
$activeTab   = $activeTab ?? 'requirements';
$examEnabled = (new \App\Models\ThemeModel())->getSetting('exam_enabled', '1') === '1';
?>
<div class="application-tabs" id="application-tabs">

    <div class="application-tabs__item <?= $activeTab === 'applicant_form' ? 'application-tabs__item--active' : '' ?>" data-tab="applicant_form">
        <span class="application-tabs__label">Applicant Form</span>
    </div>

    <div class="application-tabs__item <?= $activeTab === 'requirements' ? 'application-tabs__item--active' : '' ?>" data-tab="requirements">
        <span class="application-tabs__label">Requirements</span>
    </div>

    <?php if ($examEnabled): ?>
    <div class="application-tabs__item <?= $activeTab === 'exam' ? 'application-tabs__item--active' : '' ?>" data-tab="exam">
        <span class="application-tabs__label">Exam</span>
    </div>
    <?php endif; ?>

    <div class="application-tabs__item <?= $activeTab === 'admission_fee' ? 'application-tabs__item--active' : '' ?>" data-tab="admission_fee">
        <span class="application-tabs__label">Admission Processing Fee</span>
    </div>

    <div class="application-tabs__item <?= $activeTab === 'reservation_fee' ? 'application-tabs__item--active' : '' ?>" data-tab="reservation_fee">
        <span class="application-tabs__label">Reservation Fee</span>
    </div>

    <div class="application-tabs__item <?= $activeTab === 'clearance' ? 'application-tabs__item--active' : '' ?>" data-tab="clearance">
        <span class="application-tabs__label">Clearance</span>
    </div>

</div>
