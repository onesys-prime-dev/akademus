<?php
/**
 * Custom field control: <select>. Expects $key, $f (field config), $value, $required,
 * and $options (option names from form_field_option_config, in display order).
 */
?>
<select name="<?= esc($key) ?>" <?= $required ? 'required' : '' ?>>
    <option value="" disabled <?= $value === '' ? 'selected' : '' ?>><?= esc(($f['placeholder'] ?? '') ?: 'Select an option') ?></option>
    <?php foreach ($options as $optionName): ?>
    <option value="<?= esc($optionName) ?>" <?= $value === $optionName ? 'selected' : '' ?>><?= esc($optionName) ?></option>
    <?php endforeach; ?>
</select>
