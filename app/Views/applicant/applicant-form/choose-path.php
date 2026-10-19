<?php
// applicant/applicant-form/choose-path.php
$feeAmount = number_format((float) ($fast_track_fee_amount ?? 1000), 0);
?>

<div class="choose-path-page">

    <div class="choose-path-header">
        <span class="choose-path-header__eyebrow">Akademus Application</span>
        <h1>How would you like to apply?</h1>
        <p>Pick whichever fits you best — you can always finish anything you skip later.</p>
    </div>

    <div class="choose-path-options">

        <form method="post" action="<?= base_url('applicant-form/choose-path') ?>" class="choose-path-card">
            <?= csrf_field() ?>
            <input type="hidden" name="path" value="full">

            <h2 class="choose-path-card__title">Full Application</h2>
            <p class="choose-path-card__desc">
                Fill out everything now — Personal Information, Family Background, Educational Background,
                and all your required documents.
            </p>
            <ul class="choose-path-card__list">
                <li>No extra fee</li>
                <li>One sitting, start to finish</li>
            </ul>

            <button type="submit" class="choose-path-card__btn">Start Full Application</button>
        </form>

        <form method="post" action="<?= base_url('applicant-form/choose-path') ?>" class="choose-path-card choose-path-card--fast-track">
            <?= csrf_field() ?>
            <input type="hidden" name="path" value="fast_track">

            <span class="choose-path-card__badge">For a quick start</span>
            <h2 class="choose-path-card__title">Fast Track</h2>
            <p class="choose-path-card__desc">
                Submit just your Application Type and Personal Information now.
                Family Background, Educational Background, and most documents can wait until later.
            </p>
            <ul class="choose-path-card__list">
                <li>Additional fee: ₱<?= $feeAmount ?></li>
                <li>Only 1–2 documents needed upfront</li>
                <li>Finish the rest anytime from your Dashboard</li>
            </ul>

            <button type="submit" class="choose-path-card__btn choose-path-card__btn--accent">Start Fast Track</button>
        </form>

    </div>
</div>
