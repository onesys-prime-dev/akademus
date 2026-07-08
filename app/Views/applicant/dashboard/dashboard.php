<?php
// dashboard/dashboard.php

$steps = $steps ?? [];

// Preview first 4 requirements in the document checklist
$allReqs     = $doc_stats['all_requirements'];
$uploadedDoc = $doc_stats['uploaded_docs'];
$previewReqs = array_slice($allReqs, 0, 4);

// Only count missing (not yet uploaded) docs beyond the preview
$moreCount = count(array_filter(
    array_slice($allReqs, 4),
    fn($r) => !isset($uploadedDoc[$r['program_requirement_id']])
));

function progressBar(int $percent): array
{
    $percent = max(0, min(100, $percent));

    // interpolate yellow -> green
    $start = [255, 178, 0];
    $end   = [49, 166, 70];

    $ratio = $percent / 100;

    $r = (int) round($start[0] + (($end[0] - $start[0]) * $ratio));
    $g = (int) round($start[1] + (($end[1] - $start[1]) * $ratio));
    $b = (int) round($start[2] + (($end[2] - $start[2]) * $ratio));

    return [
        'width' => $percent . '%',
        'color' => "rgba($r, $g, $b, 0.35)",
    ];
}

$docBar  = progressBar($doc_progress);
$formBar = progressBar($form_progress);
?>

<div class="dashboard-container">

    <!-- ── Profile Banner ── -->
    <div class="profile-banner">
        <div class="top-most-span">
            <span>AKADEMUS · SY 2025–2026</span>
        </div>

        <div class="banner-name-header">
            <h1>Hello, <span><?= esc($firstname) ?></span></h1>
        </div>

        <div class="banner-header-info">
            <p><?= esc($banner_message) ?></p>
        </div>

        <div class="lower-profile-banner">
            <div class="profile-banner-applicant-id-box">
                <span>Applicant ID</span>
                <p>#<?= esc($applicant_number) ?></p>
            </div>

            <div class="quick-actions">
                <a class="btn-accent" href="<?= base_url('requirements') ?>">Upload Documents</a>
                <a class="btn-accent" href="<?= base_url('admission-processing-fee') ?>">Pay Admission Fee</a>
                <?php if (!empty($is_fast_track) && empty($fast_track_fee_paid)): ?>
                    <a class="btn-accent" href="<?= base_url('fast-track-fee') ?>">Pay Fast Track Fee</a>
                <?php endif; ?>
                <?php if ($app_status === 'approved'): ?>
                    <a class="btn-accent btn-reservation" href="<?= base_url('reservation-fee') ?>">
                        Pay Reservation Fee
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Overall Progress ── -->
    <div class="application-progress">
        <div class="app-progress-header">
            <h2>Overall Application Progress</h2>
            <span><?= $progress ?>%</span>
        </div>

        <div class="steps-container">
            <?php foreach ($steps as $num => $label):
                $status      = $step_statuses[$num] ?? '';
                $barClass    = $status === 'done' ? 'step-bar-done'    : ($status === 'inprogress' ? 'step-bar-inprogress'    : '');
                $lineClass   = $status === 'done' ? 'step-line-done'   : ($status === 'inprogress' ? 'step-line-inprogress'   : '');
                $circleClass = $status === 'done' ? 'step-circle-done' : ($status === 'inprogress' ? 'step-circle-inprogress' : '');
                $labelClass  = $status === 'done' ? 'step-label-done'  : ($status === 'inprogress' ? 'step-label-inprogress'  : '');
            ?>
                <div class="step-box">
                    <div class="step-bar-container">
                        <div class="step-bar <?= $barClass ?>"></div>
                    </div>
                    <div class="step-bottom">
                        <div class="step-ring">
                            <?php if ($num === 1): ?>
                                <div class="step-line left-line"></div>
                            <?php else: ?>
                                <div class="step-line <?= $lineClass ?>"></div>
                            <?php endif; ?>

                            <div class="step-circle <?= $circleClass ?>">
                                <?= $status === 'done' ? '✓' : $num ?>
                            </div>

                            <?php if ($num === count($steps)): ?>
                                <div class="step-line right-line"></div>
                            <?php else: ?>
                                <div class="step-line <?= $lineClass ?>"></div>
                            <?php endif; ?>
                        </div>
                        <span class="<?= $labelClass ?>"><?= $label ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Checklist Cards ── -->
    <div class="checklist-summary-container">

        <?= view('applicant/dashboard/components/checklist-form', [
            'form_pages'      => $form_pages,
            'form_completion' => $form_completion,
            'form_completed'  => $form_completed,
            'form_total'      => $form_total,
            'form_progress'   => $form_progress,
            'formBar'         => $formBar,
        ]) ?>

        <?= view('applicant/dashboard/components/checklist-documents', [
            'doc_stats'   => $doc_stats,
            'doc_progress' => $doc_progress,
            'docBar'      => $docBar,
            'previewReqs' => $previewReqs,
            'uploadedDoc' => $uploadedDoc,
            'moreCount'   => $moreCount,
            'allReqs'     => $allReqs,
        ]) ?>

        <?= view('applicant/dashboard/components/checklist-payment-fees', [
            'paymentCheckList' => $paymentCheckList
        ]) ?>

    </div><!-- /.checklist-summary-container -->

    <!-- ── Payment Notification ── -->
    <?php if ($showPaymentNotification): ?>
        <div class="payment-notification-container">
            <div class="payment-notification-text-info">
                <p>You have been approved for admission.</p>
                <span>
                    Pay the ₱<?= number_format($reservation_fee_amount, 2) ?>
                    reservation fee within 5 days to officially secure your slot.
                </span>
            </div>
            <a href="<?= base_url('reservation-fee') ?>" class="pay-now-btn">Pay Now</a>
        </div>
    <?php endif; ?>

    <!-- ── Revision Notification (persistent banner) ── -->
    <?php if ($revision_requested): ?>
        <div class="revision-notification-container">
            <div class="revision-notification-text-info">
                <p>Revision requested on your application.</p>
                <span>
                    <?php if (!empty($revision_pages)): ?>
                        Please update: <strong><?= esc(implode(', ', $revision_pages)) ?></strong>.
                    <?php endif; ?>
                    <?= esc($revision_note ?? '') ?>
                </span>
            </div>
            <a href="<?= base_url('applicant-form') ?>" class="revision-now-btn">Review &amp; Fix</a>
        </div>
    <?php endif; ?>

    <!-- ── Fast Track Reminder (persistent banner) ── -->
    <?php if (!empty($is_fast_track) && !empty($pending_fast_track_pages)): ?>
        <div class="fast-track-notification-container">
            <div class="fast-track-notification-text-info">
                <p>Fast Track: complete the rest of your application when you're ready.</p>
                <span>
                    Still needed: <strong><?= esc(implode(', ', $pending_fast_track_pages)) ?></strong>.
                </span>
            </div>
            <a href="<?= base_url('applicant-form/step/3') ?>" class="fast-track-now-btn">Complete Now</a>
        </div>
    <?php endif; ?>

</div><!-- /.dashboard-container -->

<!-- ── Fast Track Popup Modal (shown once per session) ── -->
<?php if (!empty($is_fast_track) && !empty($pending_fast_track_pages)): ?>
<div id="fast-track-modal-backdrop" class="fast-track-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="fast-track-modal-title" hidden>
    <div class="fast-track-modal">
        <div class="fast-track-modal__icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <h3 id="fast-track-modal-title" class="fast-track-modal__title">You're on Fast Track</h3>
        <p class="fast-track-modal__body">
            You submitted your initial application with just the basics.
            <br><strong>Still needed:</strong> <?= esc(implode(', ', $pending_fast_track_pages)) ?>
        </p>
        <div class="fast-track-modal__actions">
            <button type="button" id="fast-track-modal-dismiss" class="fast-track-modal__btn fast-track-modal__btn--cancel">Remind Me Later</button>
            <a href="<?= base_url('applicant-form/step/3') ?>" class="fast-track-modal__btn fast-track-modal__btn--confirm">Complete Now</a>
        </div>
    </div>
</div>
<script>
(function () {
    var seenKey = 'akademus_fast_track_modal_seen';
    if (sessionStorage.getItem(seenKey)) return;

    var backdrop = document.getElementById('fast-track-modal-backdrop');
    var dismissBtn = document.getElementById('fast-track-modal-dismiss');
    if (!backdrop) return;

    backdrop.removeAttribute('hidden');
    sessionStorage.setItem(seenKey, '1');

    function close() { backdrop.setAttribute('hidden', ''); }

    dismissBtn.addEventListener('click', close);
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) close();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !backdrop.hasAttribute('hidden')) close();
    });
})();
</script>
<?php endif; ?>

<!-- ── Revision Popup Modal (shown once per session) ── -->
<?php if ($revision_requested): ?>
<div id="revision-modal-backdrop" class="revision-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="revision-modal-title" hidden>
    <div class="revision-modal">
        <div class="revision-modal__icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                <path d="M3 3v5h5"/>
            </svg>
        </div>
        <h3 id="revision-modal-title" class="revision-modal__title">Revision Needed</h3>
        <p class="revision-modal__body">
            The admissions office has requested changes to your application.
            <?php if (!empty($revision_pages)): ?>
                <br><strong>Steps to update:</strong> <?= esc(implode(', ', $revision_pages)) ?>
            <?php endif; ?>
        </p>
        <?php if (!empty($revision_note)): ?>
            <p class="revision-modal__note"><?= esc($revision_note) ?></p>
        <?php endif; ?>
        <div class="revision-modal__actions">
            <button type="button" id="revision-modal-dismiss" class="revision-modal__btn revision-modal__btn--cancel">Remind Me Later</button>
            <a href="<?= base_url('applicant-form') ?>" class="revision-modal__btn revision-modal__btn--confirm">Go to Application Form</a>
        </div>
    </div>
</div>
<script>
(function () {
    var seenKey = 'akademus_revision_modal_seen';
    if (sessionStorage.getItem(seenKey)) return;

    var backdrop = document.getElementById('revision-modal-backdrop');
    var dismissBtn = document.getElementById('revision-modal-dismiss');
    if (!backdrop) return;

    backdrop.removeAttribute('hidden');
    sessionStorage.setItem(seenKey, '1');

    function close() { backdrop.setAttribute('hidden', ''); }

    dismissBtn.addEventListener('click', close);
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) close();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !backdrop.hasAttribute('hidden')) close();
    });
})();
</script>
<?php endif; ?>

<script>
    const paymentNotificationCard = document.querySelector('.payment-notification-container');
</script>