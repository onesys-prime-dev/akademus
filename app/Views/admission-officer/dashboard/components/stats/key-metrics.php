<div class="key-metrics">
    <div class="metric card">
        <div class="metric__details">
            <span class="metric__details--title">Total Applicants</span>
            <span class="metric__details--value"><?= number_format($statusCounts['all']) ?></span>
        </div>

        <div class="metric__icon metric__icon--applicant">
            <img class="metric__icon--img" src="<?= base_url('assets/images/icons/applicant.svg') ?>" alt=""></img>
        </div>
    </div>

    <div class="metric card">
        <div class="metric__details">
            <span class="metric__details--title">Pending Review</span>
            <span class="metric__details--value"><?= number_format($statusCounts['pending']) ?></span>
        </div>

        <div class="metric__icon metric__icon--pending">
            <img class="metric__icon--img" src="<?= base_url('assets/images/icons/pending.svg') ?>" alt=""></img>
        </div>
    </div>

    <div class="metric card">
        <div class="metric__details">
            <span class="metric__details--title">Submitted Applications</span>
            <span class="metric__details--value"><?= number_format($statusCounts['in_progress']) ?></span>
        </div>

        <div class="metric__icon metric__icon--application">
            <img class="metric__icon--img" src="<?= base_url('assets/images/icons/application.svg') ?>" alt=""></img>
        </div>
    </div>

    <div class="metric card">
        <div class="metric__details">
            <span class="metric__details--title">Approved (Complete)</span>
            <span class="metric__details--value"><?= number_format($statusCounts['approved'] + $statusCounts['completed']) ?></span>
        </div>

        <div class="metric__icon metric__icon--approved">
            <img class="metric__icon--img" src="<?= base_url('assets/images/icons/approved.svg') ?>" alt=""></img>
        </div>
    </div>
</div>
