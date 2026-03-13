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
            <?php if (empty($fields)): ?>
            <p style="color:#aaa;font-size:13px;padding:20px 0;">No fields configured for this page yet.</p>
            <?php else: ?>
                <?php foreach ($fields as $key => $f): ?>
                <?php if (!$f['is_visible']) continue; ?>
                <div class="application-form-input-field">
                    <label>
                        <?= esc($f['label']) ?>
                        <?php if ($f['is_required']): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <?php if ($f['field_type'] === 'textarea'): ?>
                    <textarea name="<?= esc($key) ?>"
                              placeholder="<?= esc($f['placeholder']) ?>"
                              <?= $f['is_required'] ? 'required' : '' ?>
                              style="height:100px;padding:10px 16px;border-radius:var(--radius);border:0;background:rgba(217,217,217,.4);font-family:var(--font),sans-serif;font-size:14px;resize:vertical;"
                    ><?= esc($data[$key] ?? '') ?></textarea>
                    <?php elseif ($f['field_type'] === 'select'): ?>
                    <select name="<?= esc($key) ?>" <?= $f['is_required'] ? 'required' : '' ?>>
                        <option value="" disabled selected><?= esc($f['placeholder'] ?: 'Select an option') ?></option>
                    </select>
                    <?php else: ?>
                    <input type="<?= esc($f['field_type']) ?>"
                           name="<?= esc($key) ?>"
                           placeholder="<?= esc($f['placeholder']) ?>"
                           value="<?= esc($data[$key] ?? '') ?>"
                           <?= $f['is_required'] ? 'required' : '' ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</form>