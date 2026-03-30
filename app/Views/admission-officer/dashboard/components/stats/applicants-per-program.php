<?php
$colors  = ['#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#10b981', '#f97316', '#06b6d4', '#ec4899'];
$maxVal  = max(1, ...array_column($perProgram ?: [['total' => 0]], 'total'));
$barH    = 36;
$barGap  = 58;
$labelX  = 140;
$barX    = 150;
$maxBarW = 820; // 150 to 970
$count   = count($perProgram);
$svgH    = max(80, $count * $barGap + 30);
?>
<div class="applicants-per-program card">
    <span class="applicants-per-program__header">Applicants Per Program</span>

    <div class="applicants-per-program__body">
        <?php if (empty($perProgram)): ?>
            <p style="text-align:center;color:#718096;padding:24px 0;">No applicant data yet.</p>
        <?php else: ?>
        <svg
            class="applicants-per-program__svg"
            viewBox="0 0 1000 <?= $svgH ?>"
            preserveAspectRatio="none"
        >
            <!-- PROGRAM LABELS & BARS -->
            <?php foreach ($perProgram as $i => $prog): ?>
            <?php
                $y      = 20 + $i * $barGap;
                $barW   = max(4, round(($prog['total'] / $maxVal) * $maxBarW));
                $color  = $colors[$i % count($colors)];
                $label  = esc($prog['program_name'] ?? $prog['program_code'] ?? 'Program ' . ($i + 1));
                $midY   = $y + $barH / 2 + 4;
            ?>
            <text x="<?= $labelX ?>" y="<?= $midY ?>" text-anchor="end" fill="#333" font-size="12"><?= $label ?></text>
            <rect x="<?= $barX ?>" y="<?= $y ?>" width="<?= $barW ?>" height="<?= $barH ?>" rx="6" fill="<?= $color ?>"/>
            <text x="<?= $barX + $barW + 8 ?>" y="<?= $midY ?>" fill="#555" font-size="11"><?= number_format($prog['total']) ?></text>
            <?php endforeach; ?>
        </svg>

        <!-- LEGENDS -->
        <div class="applicants-per-program__legends">
            <?php foreach ($perProgram as $i => $prog): ?>
            <div class="applicants-per-program__legend">
                <div class="legend-box" style="background:<?= $colors[$i % count($colors)] ?>"></div>
                <span><?= esc($prog['program_name'] ?? $prog['program_code']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
