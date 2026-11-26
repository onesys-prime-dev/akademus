<?php
/** Custom field control: <input type="date">. Expects $key, $f (field config), $value, $required. */
?>
<input type="date"
       name="<?= esc($key) ?>"
       placeholder="<?= esc($f['placeholder'] ?? '') ?>"
       value="<?= esc($value) ?>"
       <?= $required ? 'required' : '' ?>>
