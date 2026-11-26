<?php
/** Custom field control: <input type="number">. Expects $key, $f (field config), $value, $required. */
?>
<input type="number"
       name="<?= esc($key) ?>"
       placeholder="<?= esc($f['placeholder'] ?? '') ?>"
       value="<?= esc($value) ?>"
       <?= $required ? 'required' : '' ?>>
