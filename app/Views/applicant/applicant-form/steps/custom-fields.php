<?php
/**
 * Renders the admin-created (non-system) fields of a page, e.g. a "Religion" field
 * added in the Form Field Manager. Answers are stored in applications.custom_fields.
 *
 * Expects: $fields (get_field_configs() for the page), $data (saved values by field_key).
 */
$customFields = array_filter(
    $fields ?? [],
    fn($f) => empty($f['is_system']) && !empty($f['is_visible'])
);

if ($customFields):
    $selectIds = array_map('intval', array_column(
        array_filter($customFields, fn($f) => $f['field_type'] === 'select'),
        'id'
    ));
    $optionsByField = (new \App\Models\FormFieldOptionConfigModel())->getNamesForFields($selectIds);
    $inputTypes     = ['text', 'number', 'email', 'tel', 'date'];
?>
<?php foreach ($customFields as $key => $f):
    $value    = (string) ($data[$key] ?? '');
    $required = !empty($f['is_required']);
?>
<div class="application-form-input-field-row">
    <div class="application-form-input-field large-field">
        <label>
            <?= esc($f['label']) ?>
            <?php if ($required): ?><span class="required-field">*</span><?php endif; ?>
        </label>

        <?php if ($f['field_type'] === 'textarea'): ?>
        <textarea name="<?= esc($key) ?>"
                  placeholder="<?= esc($f['placeholder'] ?? '') ?>"
                  <?= $required ? 'required' : '' ?>
                  style="height:100px;padding:10px 16px;border-radius:var(--radius);border:0;background:rgba(217,217,217,.4);font-family:var(--font),sans-serif;font-size:14px;resize:vertical;"
        ><?= esc($value) ?></textarea>

        <?php elseif ($f['field_type'] === 'select'): ?>
        <select name="<?= esc($key) ?>" <?= $required ? 'required' : '' ?>>
            <option value="" disabled <?= $value === '' ? 'selected' : '' ?>><?= esc(($f['placeholder'] ?? '') ?: 'Select an option') ?></option>
            <?php foreach ($optionsByField[(int) $f['id']] ?? [] as $optionName): ?>
            <option value="<?= esc($optionName) ?>" <?= $value === $optionName ? 'selected' : '' ?>><?= esc($optionName) ?></option>
            <?php endforeach; ?>
        </select>

        <?php else: ?>
        <input type="<?= in_array($f['field_type'], $inputTypes, true) ? esc($f['field_type']) : 'text' ?>"
               name="<?= esc($key) ?>"
               placeholder="<?= esc($f['placeholder'] ?? '') ?>"
               value="<?= esc($value) ?>"
               <?= $required ? 'required' : '' ?>>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
