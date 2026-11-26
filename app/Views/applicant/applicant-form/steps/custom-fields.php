<?php
/**
 * Checks a page for admin-created (non-system) fields, e.g. a "Religion" field added
 * in the Form Field Manager, and renders each one with the template for its type
 * (applicant/applicant-form/field-types/{type}.php). Answers are stored in
 * applications.custom_fields.
 *
 * Expects: $fields (get_field_configs() for the page), $data (saved values by field_key).
 */
$customFields = array_filter(
    $fields ?? [],
    fn($f) => empty($f['is_system']) && !empty($f['is_visible'])
);

if ($customFields):
    // One query for every select field's options, instead of one per field.
    $selectIds = array_map('intval', array_column(
        array_filter($customFields, fn($f) => $f['field_type'] === 'select'),
        'id'
    ));
    $optionsByField = (new \App\Models\FormFieldOptionConfigModel())->getNamesForFields($selectIds);

    // Must match a file in field-types/ and the types offered in the admin Form Field Manager.
    $templates = ['text', 'number', 'email', 'tel', 'date', 'textarea', 'select'];
?>
<?php foreach ($customFields as $key => $f):
    $template = in_array($f['field_type'], $templates, true) ? $f['field_type'] : 'text';
    $required = !empty($f['is_required']);
?>
<div class="application-form-input-field-row">
    <div class="application-form-input-field large-field">
        <label>
            <?= esc($f['label']) ?>
            <?php if ($required): ?><span class="required-field">*</span><?php endif; ?>
        </label>
        <?= view('applicant/applicant-form/field-types/' . $template, [
            'key'      => $key,
            'f'        => $f,
            'value'    => (string) ($data[$key] ?? ''),
            'required' => $required,
            'options'  => $optionsByField[(int) $f['id']] ?? [],
        ]) ?>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
