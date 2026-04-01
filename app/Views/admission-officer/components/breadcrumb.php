<div class="breadcrumb">
    <?php foreach ($breadcrumb as $index => $item): ?>

        <?php if ($index !== array_key_last($breadcrumb)): ?>
            <div class="breadcrumb_label">
                <?= esc($item['label']) ?>
            </div>
            <img src="<?= base_url('assets/images/icons/greater-than.svg') ?>" alt="">
        <?php else: ?>
            <span class="breadcrumb_label-current">
                <?= esc($item['label']) ?>
            </span>
        <?php endif; ?>

    <?php endforeach; ?>
</div>