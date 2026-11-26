<?php
// Custom Pages
helper('form_field');
$fields = get_field_configs($page_id ?? 0);
?>
<form action="" method="post" id="applicant-form-<?= $step_num ?>" class="applicant-form">
    <div class="form-header-text">
        <h3><?= esc($page['title'] ?? 'Additional Information') ?></h3>
        <p><?= esc($page['description'] ?? '') ?></p>
    </div>
    <div class="program-sched-container">
        <div class="application-form-fields-container">
            <?php if (!array_filter($fields, fn($f) => !empty($f['is_visible']))): ?>
            <p style="color:#aaa;font-size:13px;padding:20px 0;">No fields configured for this page yet.</p>
            <?php else: ?>
                <?= view('applicant/applicant-form/steps/custom-fields', ['fields' => $fields, 'data' => $data ?? []]) ?>
            <?php endif; ?>
        </div>
    </div>
</form>