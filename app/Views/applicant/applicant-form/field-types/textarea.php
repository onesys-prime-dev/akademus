<?php
/** Custom field control: <textarea>. Expects $key, $f (field config), $value, $required. */
?>
<textarea name="<?= esc($key) ?>"
          placeholder="<?= esc($f['placeholder'] ?? '') ?>"
          <?= $required ? 'required' : '' ?>
          style="height:100px;padding:10px 16px;border-radius:var(--radius);border:0;background:rgba(217,217,217,.4);font-family:var(--font),sans-serif;font-size:14px;resize:vertical;"
><?= esc($value) ?></textarea>
