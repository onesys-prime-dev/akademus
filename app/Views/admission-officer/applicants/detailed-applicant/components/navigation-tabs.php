<div class="application-tabs" id="application-tabs">
    <div class="application-tabs__item <?= ($activeTab ?? 'requirements') === 'applicant_form' ? 'application-tabs__item--active' : '' ?>" data-tab="applicant_form">
        <span class="application-tabs__label">Applicant Form</span>
    </div>

    <div class="application-tabs__item <?= ($activeTab ?? 'requirements') === 'requirements' ? 'application-tabs__item--active' : '' ?>" data-tab="requirements">
        <span class="application-tabs__label">Requirements</span>
    </div>

    <div class="application-tabs__item <?= ($activeTab ?? 'requirements') === 'exam' ? 'application-tabs__item--active' : '' ?>" data-tab="exam">
        <span class="application-tabs__label">Exam</span>
    </div>

    <div class="application-tabs__item <?= ($activeTab ?? 'requirements') === 'reservation_fee' ? 'application-tabs__item--active' : '' ?>" data-tab="reservation_fee">
        <span class="application-tabs__label">Reservation Fee</span>
    </div>

    <div class="application-tabs__item <?= ($activeTab ?? 'requirements') === 'clearance' ? 'application-tabs__item--active' : '' ?>" data-tab="clearance">
        <span class="application-tabs__label">Clearance</span>
    </div>
</div>