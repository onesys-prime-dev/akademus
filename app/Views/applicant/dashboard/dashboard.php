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
$moreCount   = max(0, count($allReqs) - 4);

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
                <a href="">Pay Reservation Fee</a>
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

        <!-- Applicant Form Card -->
        <div class="dashboard-checklist-summary">
            <div class="top-dashboard-container">

                <div class="top-dashboard-header">
                    <h4>Applicant Form</h4>
                    <p><?= $form_total ?> section<?= $form_total !== 1 ? 's' : '' ?> to complete</p>
                </div>

                <?php if ($form_total > 0): ?>
                <div class="documents-progress-container">
                    <div class="documents-progress-bar">
                        <div class="documents-current-progress <?= $formBarClass ?>"
                             style="width:<?= $form_progress ?>%"></div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="dashboard-checklist-row-container">
                    <?php foreach ($form_pages as $page):
                        $done = !empty($form_completion[$page['id']]);
                    ?>
                        <div class="dashboard-checklist-row <?= $done ? 'checklist-row-done' : '' ?>">
                            <div class="dashboard-checklist-box <?= $done ? 'checklist-box-done' : '' ?>">
                                <?= $done ? '✓' : '' ?>
                            </div>
                            <p><?= esc($page['title']) ?></p>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($form_pages)): ?>
                        <div class="dashboard-checklist-row">
                            <span>No form sections configured.</span>
                        </div>
                    <?php endif; ?>
                </div>

            </div><!-- /.top-dashboard-container -->

            <div class="bottom-dashboard-container">
                <span class="checklist-completed-container"><?= $form_completed ?> of <?= $form_total ?> completed</span>
                <a href="<?= base_url('applicant-form') ?>" class="continue-dashboard-btn">
                    <?= $form_completed >= $form_total && $form_total > 0 ? 'Review' : 'Continue' ?>
                </a>
            </div>
        </div><!-- /.dashboard-checklist-summary -->

        <!-- Documents Card -->
        <div class="document-checklist-summary">
            <div class="top-dashboard-container">

                <div class="top-dashboard-header">
                    <h4>Documents</h4>
                    <p><?= $doc_stats['uploaded_required'] ?> of <?= $doc_stats['total_required'] ?> required uploaded</p>
                </div>

                <div class="documents-progress-container">
                    <div class="documents-progress-bar">
                        <div class="documents-current-progress <?= $docBarClass ?>"
                             style="width:<?= $doc_progress ?>%"></div>
                    </div>
                    <div class="documents-progress-legend-container">
                        <div class="document-progress-legend">
                            <div class="documents-progress-legend-circle legend-uploaded"></div>
                            <p>Uploaded <span><?= $doc_stats['uploaded_required'] ?></span></p>
                        </div>
                        <div class="document-progress-legend">
                            <div class="documents-progress-legend-circle legend-missing"></div>
                            <p>Missing <span><?= $doc_stats['missing_required'] ?></span></p>
                        </div>
                        <?php if ($doc_stats['total_optional'] > 0): ?>
                        <div class="document-progress-legend">
                            <div class="documents-progress-legend-circle legend-optional"></div>
                            <p>Optional <span><?= $doc_stats['total_optional'] ?></span></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-checklist-row-container">
                    <?php foreach ($previewReqs as $req):
                        $uploaded = isset($uploadedDoc[$req['program_requirement_id']]);
                    ?>
                        <div class="dashboard-checklist-row <?= $uploaded ? 'checklist-row-done' : '' ?>">
                            <div class="dashboard-checklist-box <?= $uploaded ? 'checklist-box-done' : '' ?>">
                                <?= $uploaded ? '✓' : '' ?>
                            </div>
                            <p><?= esc($req['requirement_name']) ?></p>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($moreCount > 0): ?>
                        <div class="dashboard-checklist-row">
                            <span>+<?= $moreCount ?> more document<?= $moreCount > 1 ? 's' : '' ?>…</span>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($allReqs)): ?>
                        <div class="dashboard-checklist-row">
                            <span>No documents required for your program yet.</span>
                        </div>
                    <?php endif; ?>
                </div>

            </div><!-- /.top-dashboard-container -->

            <div class="bottom-dashboard-container">
                <span class="checklist-completed-container"><?= $doc_progress ?>% Completed</span>
                <a href="<?= base_url('requirements') ?>" class="continue-dashboard-btn">
                    <?= $doc_progress >= 100 ? 'Review' : 'Upload' ?>
                </a>
            </div>
        </div><!-- /.document-checklist-summary -->

    </div><!-- /.checklist-summary-container -->

    <!-- ── Payment Notification ── -->
    <div class="payment-notification-container">
        <div class="payment-notification-text-info">
            <p>You have been approved for admission.</p>
            <span>Pay the ₱2,000 reservation fee within 5 days to officially secure your slot.</span>
        </div>
        <a href="" class="pay-now-btn">Pay Now</a>
    </div>

</div><!-- /.dashboard-container -->
