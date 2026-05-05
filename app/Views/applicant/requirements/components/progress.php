<!-- dashboard/components/applicant-form-progress.php  -->

<section class="form-progress card">

    <h4 id="form-progress-text" class="form-progress__title"></h4>

    <div class="form-progress__timeline">

        <?php foreach ($categories as $index => $category): ?>

            <div class="form-progress__step">
                <div class="form-progress__circle" data-step="<?= $index + 1 ?>">
                    <?= $index + 1 ?>
                </div>

                <span><?= esc(mb_strimwidth($category['category_name'], 0, 10, '…')) ?></span>
            </div>

            <?php if ($index < count($categories) - 1): ?>
                <div class="form-progress__line" data-step="<?= $index + 1 ?>"></div>
            <?php endif; ?>

        <?php endforeach; ?>

        <!-- Optional Review Step -->
        <?php $lastStep = count($categories); ?>

        <div class="form-progress__line" data-step="<?= $lastStep ?>"></div>

        <div class="form-progress__step">
            <div class="form-progress__circle" data-step="<?= $lastStep + 1 ?>">
                <?= $lastStep + 1 ?>
            </div>
            <span>Review</span>
        </div>

    </div>

</section>