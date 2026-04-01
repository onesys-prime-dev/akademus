<?php
// Status bar data: label, count, CSS class
$statusBars = [
    ['label' => 'In Progress', 'count' => $statusCounts['in_progress'], 'class' => 'applicants-per-status__legend--box-submitted'],
    ['label' => 'Pending',     'count' => $statusCounts['pending'],     'class' => 'applicants-per-status__legend--box-pending'],
    ['label' => 'Approved',    'count' => $statusCounts['approved'] + $statusCounts['completed'], 'class' => 'applicants-per-status__legend--box-approved'],
    ['label' => 'Rejected',    'count' => $statusCounts['rejected'],    'class' => 'applicants-per-status__legend--box-rejected'],
];

$maxCount = max(1, ...array_column($statusBars, 'count'));

// Chart geometry
$chartTop    = 20;
$chartBottom = 260;
$chartH      = $chartBottom - $chartTop; // 240
$barWidth    = 140;
$barXPositions = [150, 365, 580, 795];

// Y-axis: round maxCount up to a nice multiple
$step = 1;
foreach ([1000, 500, 200, 100, 50, 20, 10, 5, 2, 1] as $s) {
    if ($maxCount / $s >= 3) { $step = $s; break; }
}
$axisMax  = ceil($maxCount / $step) * $step;
$axisMax  = max($axisMax, 1);
$gridSteps = 5;
$gridStep  = $axisMax / $gridSteps;

// Grid y positions (top to bottom = max to 0)
$gridYs = [];
for ($g = 0; $g <= $gridSteps; $g++) {
    $gridYs[] = $chartTop + ($g / $gridSteps) * $chartH;
}
?>
<div class="applicants-per-status card">
    <span class="applicants-per-status__header">Applicants Per Status</span>

    <div class="applicants-per-status__body">
        <svg
            class="applicants-per-status__svg"
            viewBox="0 0 1000 280"
            preserveAspectRatio="none"
            role="img"
            aria-label="Applicants per status bar chart"
        >
            <!-- grid lines -->
            <g class="applicants-per-status__grid">
                <?php foreach ($gridYs as $gy): ?>
                <line x1="90" y1="<?= $gy ?>" x2="980" y2="<?= $gy ?>" />
                <?php endforeach; ?>
            </g>

            <!-- y-axis labels -->
            <g class="applicants-per-status__y-axis">
                <?php foreach ($gridYs as $gi => $gy): ?>
                <text x="80" y="<?= $gy + 4 ?>"><?= number_format($axisMax - $gi * $gridStep) ?></text>
                <?php endforeach; ?>
            </g>

            <!-- bars -->
            <g class="applicants-per-status__bars">
                <?php foreach ($statusBars as $i => $bar): ?>
                <?php
                    $barH = $bar['count'] > 0
                        ? round(($bar['count'] / $axisMax) * $chartH)
                        : 0;
                    $barY = $chartBottom - $barH;
                    $bx   = $barXPositions[$i];
                ?>
                <rect x="<?= $bx ?>" y="<?= $barY ?>" width="<?= $barWidth ?>" height="<?= max(0, $barH) ?>" rx="8" />
                <?php if ($bar['count'] > 0): ?>
                <text x="<?= $bx + $barWidth / 2 ?>" y="<?= $barY - 6 ?>"
                      text-anchor="middle" fill="#555" font-size="13">
                    <?= number_format($bar['count']) ?>
                </text>
                <?php endif; ?>
                <?php endforeach; ?>
            </g>

            <!-- x-axis labels -->
            <g fill="#555" font-size="13">
                <?php foreach ($statusBars as $i => $bar): ?>
                <text x="<?= $barXPositions[$i] + $barWidth / 2 ?>" y="278"
                      text-anchor="middle"><?= esc($bar['label']) ?></text>
                <?php endforeach; ?>
            </g>
        </svg>
    </div>

    <div class="applicants-per-status__legends">
        <?php foreach ($statusBars as $bar): ?>
        <div class="applicants-per-status__legend">
            <div class="applicants-per-status__legend--box <?= $bar['class'] ?>"></div>
            <div class="applicants-per-status__legend--title"><?= esc($bar['label']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
