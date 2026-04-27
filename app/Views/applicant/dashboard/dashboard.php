<?php
// dashboard/dashboard.php

$steps = [
    1 => 'Account',
    2 => 'Form',
    3 => 'Documents',
    4 => 'Verified',
    5 => 'Enrolled',
];

// Preview first 4 requirements in the document checklist
$allReqs     = $doc_stats['all_requirements'];
$uploadedDoc = $doc_stats['uploaded_docs'];
$previewReqs = array_slice($allReqs, 0, 4);

// Only count missing (not yet uploaded) docs beyond the preview
$moreCount = count(array_filter(
    array_slice($allReqs, 4),
    fn($r) => !isset($uploadedDoc[$r['program_requirement_id']])
));

// Document progress bar CSS class
$docBarClass = '';
if ($doc_progress > 0 && $doc_progress < 100)       $docBarClass = 'progress-inprogress';
elseif ($doc_progress >= 100)                        $docBarClass = 'progress-done';

// Form progress bar CSS class
$formBarClass = '';
if ($form_progress > 0 && $form_progress < 100)      $formBarClass = 'progress-inprogress';
elseif ($form_progress >= 100)                       $formBarClass = 'progress-done';
?>

<div class="dashboard-container">

    <!-- ── Profile Banner ── -->
    <div class="profile-banner">
        <div class="top-most-span">
            <span>AKADEMOS · SY 2025–2026</span>
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
                <a href="<?= base_url('requirements') ?>">Upload Documents</a>
                <a href="<?= base_url('admission-processing-fee') ?>">Pay Admission Fee</a>
                <a href="<?= base_url('reservation-fee') ?>">Pay Reservation Fee</a>
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

                            <?php if ($num === 5): ?>
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
            'formBarClass'    => $formBarClass,
        ]) ?>

        <?= view('applicant/dashboard/components/checklist-documents', [
            'doc_stats'   => $doc_stats,
            'doc_progress'=> $doc_progress,
            'docBarClass' => $docBarClass,
            'previewReqs' => $previewReqs,
            'uploadedDoc' => $uploadedDoc,
            'moreCount'   => $moreCount,
            'allReqs'     => $allReqs,
        ]) ?>

    </div><!-- /.checklist-summary-container -->

    <!-- ── Payment Notification ── -->
    <div class="payment-notification-container">
        <div class="payment-notification-text-info">
            <p>You have been approved for admission.</p>
            <span>Pay the ₱<?= number_format($reservation_fee_amount, 2) ?> reservation fee within 5 days to officially secure your slot.</span>
        </div>
        <a href="<?= base_url('reservation-fee') ?>" class="pay-now-btn">Pay Now</a>
    </div>

</div><!-- /.dashboard-container -->
