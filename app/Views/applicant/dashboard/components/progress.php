<div class="application-progress">

    <div class="app-progress-header">
        <h2>Overall Application Progress</h2>
        <span>40%</span>
    </div>

    <div class="steps-container">

        <?php 
        $steps = [
            'Account',
            'Form',
            'Documents',
            'Verified',
            'Enrolled'
        ];
        ?>

        <?php foreach ($steps as $index => $step): ?>

        <div class="step-box">

            <div class="step-bar-container">
                <div class="step-bar"></div>
            </div>

            <div class="step-bottom">

                <div class="step-ring">
                    <div class="step-line <?= $index === 0 ? 'left-line' : '' ?>"></div>

                    <div class="step-circle">
                        <?= $index + 1 ?>
                    </div>

                    <div class="step-line <?= $index === count($steps)-1 ? 'right-line' : '' ?>"></div>
                </div>

                <span><?= esc($step) ?></span>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>