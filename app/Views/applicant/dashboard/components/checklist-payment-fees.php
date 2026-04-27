<?php

?>

<div class="dashboard-checklist-summary card">

    <div class="top-dashboard-container">

        <div class="top-dashboard-header">
            <h4>Payments</h4>
            <p>Track your payment progress</p>
        </div>

        <div class="dashboard-checklist-row-container">

            <?php
            $doneCount = 0;
            foreach ($paymentCheckList as $item):
                $done = $item['paid'];
                if ($done) $doneCount++;
            ?>

                <div class="dashboard-checklist-row <?= $done ? 'checklist-row-done' : '' ?>">

                    <div class="dashboard-checklist-box <?= $done ? 'checklist-box-done' : '' ?>">
                        <?= $done ? '✓' : '' ?>
                    </div>

                    <p><?= esc($item['title']) ?></p>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <div class="bottom-dashboard-container">
        <span class="checklist-completed-container">
            <?= $doneCount ?> of <?= count($paymentCheckList) ?> completed
        </span>

        <a href="<?= base_url('admission-processing-fee') ?>" class="continue-dashboard-btn">
            View
        </a>
    </div>

</div>