<?php
/** Custom field control: <input type="text">. Expects $key, $f (field config), $value, $required. */
?>
<input type="text"
       name="<?= esc($key) ?>"
       placeholder="<?= esc($f['placeholder'] ?? '') ?>"
       value="<?= esc($value) ?>"
       <?= $required ? 'required' : '' ?>>
