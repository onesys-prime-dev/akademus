<div class="applicant-profile">
    <div class="applicant-profile__header">
        <div class="applicant-profile__identity">
            <img class="applicant-profile__avatar" src="<?= base_url('assets/images/icons/sample-profile.png') ?>" alt="">

            <div class="applicant-profile__info">
                <span class="applicant-profile__name"><?= esc($profile['applicant_name'] ?? 'Unknown Applicant') ?></span>
                <span class="applicant-profile__id">#<?= esc($profile['applicant_number'] ?? 'N/A') ?></span>
            </div>
        </div>

        <div class="applicant-profile__status status-badge status-badge--<?= esc($profile['status'] ?? 'pending') ?>">
            <?= esc(ucwords(str_replace('_', ' ', $profile['application_status'] ?? 'Pending'))) ?>
        </div>
    </div>

    <div class="applicant-profile__summaries">
        <div class="applicant-profile__summary">
            <span class="applicant-profile__label">Applicant Type</span>
            <span class="applicant-profile__value"><?= esc($profile['applicant_type'] ?? '') ?></span>
        </div>

        <div class="applicant-profile__summary">
            <span class="applicant-profile__label">Program</span>
            <span class="applicant-profile__value"><?= esc($profile['program_name'] ?? '-') ?></span>
        </div>

        <div class="applicant-profile__summary">
            <span class="applicant-profile__label">Date Added</span>
            <span class="applicant-profile__value">
                <?= !empty($profile['application_date']) ? esc(date('F j, Y', strtotime($profile['application_date']))) : '-' ?>
            </span>
        </div>

        <div class="applicant-profile__summary no-border">
            <span class="applicant-profile__label">Last Progress</span>
            <span class="applicant-profile__value"><?= esc($profile['last_progress'] ?? 'Requirements') ?></span>
        </div>
    </div>
</div>