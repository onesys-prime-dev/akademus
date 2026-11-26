<?php
/** Custom field control: <input type="tel">. Expects $key, $f (field config), $value, $required. */
?>
<input type="tel"
       name="<?= esc($key) ?>"
       placeholder="<?= esc($f['placeholder'] ?? '') ?>"
       value="<?= esc($value) ?>"
       <?= $required ? 'required' : '' ?>>
