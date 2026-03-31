<?php
// applicant/tracking-progress/tracking_progress.php

$appStatus     = $app_status    ?? '';
$formComplete  = $form_complete  ?? false;
$formSubmitted = $form_submitted ?? false;
$docsComplete  = $docs_complete  ?? false;

function tp_format_date(?string $date): string {
    if (!$date) return '';
    $ts = strtotime($date);
    return $ts ? date('M j, Y', $ts) : '';
}

function tp_step_icon(string $status): string {
    if ($status === 'done') {
        return '<svg viewBox="0 0 32 33" fill="none">
            <path d="M14.1067 21.4225C13.84 21.4225 13.5867 21.3125 13.4 21.12L9.62667 17.2288C9.24 16.83 9.24 16.17 9.62667 15.7713C10.0133 15.3725 10.6533 15.3725 11.04 15.7713L14.1067 18.9338L20.96 11.8663C21.3467 11.4675 21.9867 11.4675 22.3733 11.8663C22.76 12.265 22.76 12.925 22.3733 13.3237L14.8133 21.12C14.6267 21.3125 14.3733 21.4225 14.1067 21.4225Z" fill="white"/>
        </svg>';
    }
    if ($status === 'active') {
        return '<svg viewBox="0 0 32 33" fill="none">
            <circle cx="16" cy="16.5" r="5" fill="white"/>
        </svg>';
    }
    return '';
}
?>

<div class="tp-page">
    <?php
        // ── Dynamic status title & description ──────────────────────────
        if ($appStatus === 'enrolled'):
            $statusTitle = 'Enrolled';
            $statusDesc  = 'You are now officially enrolled. Welcome to the family!';

        elseif ($appStatus === 'approved'):
            // Case 4: Verified — waiting for admission decision
            $statusTitle = 'Application Approved';
            $statusDesc  = 'Your documents have been successfully verified. Please wait for the admission decision.';

        elseif ($formComplete && $docsComplete):
            // Case 3: Everything submitted — waiting for verification
            $statusTitle = 'Application Under Review';
            $statusDesc  = 'Please wait while your information is being verified. Documents are currently under review by the admissions office. Expected update within 3–5 business days.';

        elseif ($formComplete && !$docsComplete):
            // Case 2: Form done, documents not yet submitted
            $statusTitle = 'Form Completed';
            $statusDesc  = 'Your form has been completed. Please upload all required documents to continue your application process.';

        else:
            // Case 1: Form not completed or not submitted
            $statusTitle = 'Application In Progress';
            $statusDesc  = 'Please complete all required fields in the application form to proceed. Ensure that all information is accurate before submitting.';

        endif;
    ?>

    <!-- Status Header Card -->
    <div class="tp-status-card">
        <div class="tp-status-left">
            <h2 class="tp-status-title"><?= esc($statusTitle) ?></h2>
            <p class="tp-status-desc"><?= esc($statusDesc) ?></p>
        </div>
        <div class="tp-status-right">
            <div class="tp-applicant-id-label">Applicant ID</div>
            <div class="tp-applicant-id-number">#<?= esc($applicant_number) ?></div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="tp-card">
        <h3 class="tp-card-title">Overall Progress</h3>

        <?php
            $totalSteps  = count($steps);
            $doneSteps   = count(array_filter($steps, fn($s) => $s['status'] === 'done'));
            $progressPct = $totalSteps > 0 ? (int) round($doneSteps / $totalSteps * 100) : 0;
        ?>

        <div class="tp-progress-bar-wrap">
            <div class="tp-progress-bar">
                <div class="tp-progress-fill" style="width: <?= $progressPct ?>%;"></div>
            </div>
            <span class="tp-progress-pct"><?= $progressPct ?>%</span>
        </div>

        <!-- Timeline -->
        <div class="tp-timeline">
            <?php foreach ($steps as $i => $step):
                $isLast   = $i === count($steps) - 1;
                $status   = $step['status'];
                $date     = tp_format_date($step['date'] ?? null);
            ?>
            <div class="tp-step tp-step--<?= $status ?>">
                <div class="tp-step-left">
                    <div class="tp-icon tp-icon--<?= $status ?>">
                        <?= tp_step_icon($status) ?>
                    </div>
                    <?php if (!$isLast): ?>
                        <div class="tp-line tp-line--<?= $status === 'done' ? 'done' : 'pending' ?>"></div>
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
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
