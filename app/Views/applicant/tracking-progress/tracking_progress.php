<?php
// applicant/tracking-progress/tracking_progress.php

$appStatus      = $app_status       ?? '';
$formComplete   = $form_complete    ?? false;
$formSubmitted  = $form_submitted   ?? false;
$docsComplete   = $docs_complete    ?? false;
$decisionRemarks = $decision_remarks ?? null;

$revisionRequested = $revision_requested ?? false;
$revisionNote      = $revision_note      ?? null;
$revisionPages     = $revision_pages     ?? [];

$isApproved = in_array($appStatus, ['approved', 'enrolled']);
$isRejected = $appStatus === 'rejected';
$isEnrolled = $appStatus === 'enrolled';

function tp_format_date(?string $date): string {
    if (!$date) return '';
    $ts = strtotime($date);
    return $ts ? date('M j, Y', $ts) : '';
}

function tp_step_icon(string $status): string {
    if ($status === 'done') {
        return '<svg viewBox="0 0 32 33" fill="none">
            <path d="M14.1067 21.4225C13.84 21.4225 13.5867 21.3125 13.4 21.12L9.62667 17.2288C9.24 16.83 9.24 16.17 9.62667 15.7713C10.0133 15.3725 10.6533 15.3725 11.04 15.7713L14.1067 18.9338L20.96 11.8663C21.3467 11.4675 21.9867 11.4675 22.3733 11.8663C22.76 12.265 22.76 12.925 22.3733 13.3237L14.8133 21.12C14.6267 21.3125 14.3733 21.4225 14.1067 21.4225Z" fill="#31a646"/>
        </svg>';
    }
    if ($status === 'rejected') {
        return '<svg viewBox="0 0 32 33" fill="none">
            <path d="M20 13.5L12 21.5M12 13.5L20 21.5" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round"/>
        </svg>';
    }
    if ($status === 'active') {
        return '<svg viewBox="0 0 32 33" fill="none">
            <circle cx="16" cy="16.5" r="5" fill="#ffb200"/>
        </svg>';
    }
    return '';
}
?>

<div class="tp-page">

    <?php
        // ── Dynamic status card ──────────────────────────────────────────
        if ($isEnrolled):
            $statusTitle = 'Enrolled';
            $statusDesc  = 'You are now officially enrolled. Welcome to the family!';
            $statusMod   = 'tp-status-card--success';

        elseif ($isRejected):
            $statusTitle = 'Application Declined';
            $statusDesc  = 'Your application was not approved at this time. Please review the remarks below for details.';
            $statusMod   = 'tp-status-card--rejected';

        elseif ($isApproved):
            $statusTitle = 'Application Approved';
            $statusDesc  = 'Congratulations! Your application has been approved. Please proceed with the next steps.';
            $statusMod   = 'tp-status-card--success';

        elseif ($revisionRequested):
            $statusTitle = 'Revision Needed';
            $statusDesc  = 'The admissions office has requested changes to your application. Please review the details below and resubmit.';
            $statusMod   = 'tp-status-card--revision';

        elseif ($formComplete && $docsComplete):
            // Case 3: waiting for verification
            $statusTitle = 'Application Under Review';
            $statusDesc  = 'Please wait while your information is being verified. Documents are currently under review by the admissions office. Expected update within 3–5 business days.';
            $statusMod   = '';

        elseif ($formComplete && !$docsComplete):
            // Case 2: form done, docs missing
            $statusTitle = 'Form Completed';
            $statusDesc  = 'Your form has been completed. Please upload all required documents to continue your application process.';
            $statusMod   = '';

        else:
            // Case 1: form not done
            $statusTitle = 'Application In Progress';
            $statusDesc  = 'Please complete all required fields in the application form to proceed. Ensure that all information is accurate before submitting.';
            $statusMod   = '';

        endif;
    ?>

    <!-- Status Header Card -->
    <div class="tp-status-card <?= $statusMod ?>">
        <div class="tp-status-left">
            <h2 class="tp-status-title"><?= esc($statusTitle) ?></h2>
            <p class="tp-status-desc"><?= esc($statusDesc) ?></p>
        </div>
        <div class="tp-status-right">
            <div class="tp-applicant-id-label">Applicant ID</div>
            <div class="tp-applicant-id-number">#<?= esc($applicant_number) ?></div>
        </div>
    </div>

    <!-- Remarks card (only when a decision has been made) -->
    <?php if ($decisionRemarks && ($isApproved || $isRejected)): ?>
    <div class="tp-remarks-card tp-remarks-card--<?= $isRejected ? 'rejected' : 'approved' ?>">
        <div class="tp-remarks-header">
            <?php if ($isApproved): ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Admission Office Remarks</span>
            <?php else: ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Reason for Decline</span>
            <?php endif; ?>
        </div>
        <p class="tp-remarks-body"><?= esc($decisionRemarks) ?></p>
    </div>
    <?php endif; ?>

    <!-- Revision-requested card -->
    <?php if ($revisionRequested): ?>
    <div class="tp-remarks-card tp-remarks-card--revision">
        <div class="tp-remarks-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            <span>Revision Requested</span>
        </div>

        <?php if (!empty($revisionPages)): ?>
        <p class="tp-remarks-body" style="margin-bottom:8px;">
            <strong>Steps to update:</strong> <?= esc(implode(', ', $revisionPages)) ?>
        </p>
        <?php endif; ?>

        <p class="tp-remarks-body"><?= esc($revisionNote) ?></p>

        <div style="margin-top:14px; display:flex; gap:10px;">
            <a href="<?= base_url('applicant-form') ?>" class="tp-remarks-cta-link">Go to Application Form</a>
            <button type="button" id="tp-resubmit-btn" class="tp-remarks-cta-btn">Resubmit Application</button>
        </div>
        <p id="tp-resubmit-error" style="color:#dc2626; font-size:12px; margin-top:6px;"></p>
    </div>
    <?php endif; ?>

    <!-- Progress Section -->
    <div class="tp-card">
        <h3 class="tp-card-title">Overall Progress</h3>

        <?php
            $totalSteps  = count($steps);
            $doneSteps   = count(array_filter($steps, fn($s) => in_array($s['status'], ['done', 'rejected'])));
            $progressPct = $totalSteps > 0 ? (int) round($doneSteps / $totalSteps * 100) : 0;
        ?>

        <div class="tp-progress-bar-wrap">
            <div class="tp-progress-bar">
                <div class="tp-progress-fill <?= $isRejected ? 'tp-progress-fill--rejected' : '' ?>" style="width: <?= $progressPct ?>%;"></div>
            </div>
            <span class="tp-progress-pct <?= $isRejected ? 'tp-progress-pct--rejected' : '' ?>"><?= $progressPct ?>%</span>
        </div>

        <!-- Timeline -->
        <div class="tp-timeline">
            <?php foreach ($steps as $i => $step):
                $isLast  = $i === count($steps) - 1;
                $status  = $step['status'];
                $date    = tp_format_date($step['date'] ?? null);
                $remarks = $step['remarks'] ?? null;

                $lineClass = match(true) {
                    $status === 'done'     => 'done',
                    $status === 'rejected' => 'rejected',
                    default                => 'pending',
                };
            ?>
            <div class="tp-step tp-step--<?= $status ?>">
                <div class="tp-step-left">
                    <div class="tp-icon tp-icon--<?= $status ?>">
                        <?= tp_step_icon($status) ?>
                    </div>
                    <?php if (!$isLast): ?>
                        <div class="tp-line tp-line--<?= $lineClass ?>"></div>
                    <?php endif; ?>
                </div>

                <div class="tp-step-content">
                    <div class="tp-step-title"><?= esc($step['title']) ?></div>
                    <div class="tp-step-subtitle"><?= esc($step['subtitle']) ?></div>

                    <?php if ($date): ?>
                        <div class="tp-step-date"><?= esc($date) ?></div>
                    <?php elseif ($status === 'active'): ?>
                        <div class="tp-step-date tp-step-date--active">In progress</div>
                    <?php elseif ($status === 'pending'): ?>
                        <div class="tp-step-date">Pending</div>
                    <?php elseif ($status === 'rejected'): ?>
                        <div class="tp-step-date tp-step-date--rejected">Declined</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if ($revisionRequested): ?>
<script>
document.getElementById('tp-resubmit-btn')?.addEventListener('click', async function () {
    const btn = this;
    const err = document.getElementById('tp-resubmit-error');
    btn.disabled = true;
    btn.textContent = 'Resubmitting…';
    try {
        const res  = await fetch('<?= base_url('applicant-form/resubmit') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();
        if (data.status === 'success') {
            location.reload();
        } else {
            err.textContent = data.message || 'Something went wrong.';
            btn.disabled = false;
            btn.textContent = 'Resubmit Application';
        }
    } catch (e) {
        err.textContent = 'Network error. Please try again.';
        btn.disabled = false;
        btn.textContent = 'Resubmit Application';
    }
});
</script>
<?php endif; ?>
