<?php
// Variables expected:
// $form_pages, $form_completion, $form_completed, $form_total, $form_progress, $formBarClass
?>

<div class="dashboard-checklist-summary card">
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

    </div>

    <div class="bottom-dashboard-container">
        <span class="checklist-completed-container"><?= $form_completed ?> of <?= $form_total ?> completed</span>
        <a href="<?= base_url('applicant-form') ?>" class="continue-dashboard-btn">
            <?= $form_completed >= $form_total && $form_total > 0 ? 'Review' : 'Continue' ?>
        </a>
    </div>
</div>
