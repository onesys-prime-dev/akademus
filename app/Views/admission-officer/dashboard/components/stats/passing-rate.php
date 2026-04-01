<!-- PIE CHART -->
<?php
$rate    = (int) ($passingRate ?? 0);
$failRate = 100 - $rate;
?>
<div class="passing-rate card">
    <span class="passing-rate__header">Passing Rate</span>

    <div class="passing-rate__body">
        <div class="passing-rate__chart">
            <div class="passing-rate__pie"
                 style="background: conic-gradient(var(--primary, #0040a6) 0% <?= $rate ?>%, #e2e8f0 <?= $rate ?>% 100%)">
            </div>
            <div class="passing-rate__center">
                <span><?= $rate ?>%</span>
            </div>
        </div>
    </div>

    <div class="passing-rate__legends">
        <div class="passing-rate__legend">
            <div class="passing-rate__legend--box legend__box--passed"></div>
            <span class="passing-rate__legend--title">Passed (<?= $rate ?>%)</span>
        </div>

        <div class="passing-rate__legend">
            <div class="passing-rate__legend--box legend__box--failed"></div>
            <span class="passing-rate__legend--title">Failed (<?= $failRate ?>%)</span>
        </div>
    </div>
</div>
