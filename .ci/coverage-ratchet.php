<?php

/**
 * Coverage ratchet: line coverage may never drop below the recorded baseline.
 *
 *   php .ci/coverage-ratchet.php build/logs/clover.xml [.ci/coverage-baseline.txt]
 *
 * - Fails when coverage < baseline - 0.10 (tolerance for rounding / tiny refactors).
 * - When coverage rose by >= 0.5 points, says so: raise the baseline in the same PR
 *   (`php .ci/coverage-ratchet.php build/logs/clover.xml --update`) to lock the gain in.
 * - Writes a Markdown summary to $GITHUB_STEP_SUMMARY when available.
 */
$clover   = $argv[1] ?? 'build/logs/clover.xml';
$baseline = __DIR__ . '/coverage-baseline.txt';
$update   = in_array('--update', $argv, true);

if (! is_file($clover)) {
    fwrite(STDERR, "Clover report not found: {$clover}\n");

    exit(2);
}

$xml     = simplexml_load_file($clover);
$metrics = $xml->project->metrics;
$total   = (int) $metrics['statements'];
$covered = (int) $metrics['coveredstatements'];
$current = $total > 0 ? round($covered / $total * 100, 2) : 0.0;

if ($update) {
    file_put_contents($baseline, number_format($current, 2, '.', '') . "\n");
    echo "Coverage baseline set to {$current}%.\n";

    exit(0);
}

$base  = is_file($baseline) ? (float) trim((string) file_get_contents($baseline)) : 0.0;
$delta = round($current - $base, 2);
$ok    = $current >= $base - 0.10;

$line = sprintf('Line coverage: **%.2f%%** (%d/%d statements) — baseline %.2f%% (%+.2f)', $current, $covered, $total, $base, $delta);
echo strip_tags(str_replace('**', '', $line)) . "\n";

if ($summary = getenv('GITHUB_STEP_SUMMARY')) {
    file_put_contents($summary, "### PHPUnit coverage\n\n{$line}\n\n", FILE_APPEND);
}

if (! $ok) {
    echo "::error title=Coverage dropped::Line coverage {$current}% is below the baseline {$base}%. Add tests for the new/changed code.\n";

    exit(1);
}

if ($delta >= 0.5) {
    echo "::notice title=Coverage improved::Coverage rose to {$current}%. Raise .ci/coverage-baseline.txt in this PR: php .ci/coverage-ratchet.php {$clover} --update\n";
}

exit(0);
