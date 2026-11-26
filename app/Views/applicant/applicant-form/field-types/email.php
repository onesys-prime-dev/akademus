<?php
/** Custom field control: <input type="email">. Expects $key, $f (field config), $value, $required. */
?>
<input type="email"
       name="<?= esc($key) ?>"
       placeholder="<?= esc($f['placeholder'] ?? '') ?>"
       value="<?= esc($value) ?>"
       <?= $required ? 'required' : '' ?>>
