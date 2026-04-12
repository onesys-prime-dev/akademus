<?php
$colors  = ['#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#10b981', '#f97316', '#06b6d4', '#ec4899'];

$data = $perProgram ?? [];
$count = count($data);

$maxVal = max(1, ...array_column($data ?: [['total' => 0]], 'total'));

$barH    = 28;
$barGap  = 46;
$labelX  = 100;   // reduced for better responsiveness
$barX    = 120;
$maxBarW = 700;

$svgH = max(80, $count * $barGap + 20);
?>

<div class="applicants-per-program card">
    <span class="applicants-per-program__header">Applicants Per Program</span>

    <div class="applicants-per-program__body">
        <?php if (empty($perProgram)): ?>
            <p style="text-align:center;color:#718096;padding:24px 0;">No applicant data yet.</p>
        <?php else: ?>
        <svg
            class="applicants-per-program__svg"
            viewBox="0 0 900 <?= $svgH ?>"
            preserveAspectRatio="xMinYMin meet"
        >
            <?php foreach ($data as $i => $prog): ?>
            <?php
                $y      = 10 + $i * $barGap;
                $value  = (int) $prog['total'];
                $barW   = ($value > 0) ? max(6, round(($value / $maxVal) * $maxBarW)) : 4;
                $color  = $colors[$i % count($colors)];

                $labelRaw = $prog['program_code'] ?? $prog['program_name'] ?? 'Program ' . ($i + 1);
                $label = esc(mb_strimwidth($labelRaw, 0, 18, '...'));

                $midY = $y + $barH / 2 + 4;
            ?>

            <!-- Label -->
            <text x="<?= $labelX ?>" y="<?= $midY ?>"
                text-anchor="end"
                class="bar-label"
                title="<?= esc($labelRaw) ?>">
                <?= $label ?>
            </text>

            <!-- Background bar -->
            <rect x="<?= $barX ?>" y="<?= $y ?>"
                width="<?= $maxBarW ?>" height="<?= $barH ?>"
                rx="6" class="bar-bg"/>

            <!-- Actual bar -->
            <rect x="<?= $barX ?>" y="<?= $y ?>"
                width="<?= $barW ?>" height="<?= $barH ?>"
                rx="6"
                fill="<?= $color ?>"
                class="bar-fill">
                <title><?= esc($labelRaw) ?>: <?= number_format($value) ?></title>
            </rect>

            <!-- Value -->
            <text x="<?= $barX + $barW + 8 ?>" y="<?= $midY ?>"
                class="bar-value">
                <?= number_format($value) ?>
            </text>

            <?php endforeach; ?>
        </svg>

        <?php endif; ?>
    </div>
</div>
