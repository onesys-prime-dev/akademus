<?php
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = (int) (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->format('w'); // 0 = Sunday
$totalCells = (int) ceil(($firstDay + $daysInMonth) / 7) * 7;
?>

<div class="exam-schedule__calendar" data-calendar>
    <div class="exam-schedule__label--days">
        <span class="exam-schedule__label--day">SUNDAY</span>
        <span class="exam-schedule__label--day">MONDAY</span>
        <span class="exam-schedule__label--day">TUESDAY</span>
        <span class="exam-schedule__label--day">WEDNESDAY</span>
        <span class="exam-schedule__label--day">THURSDAY</span>
        <span class="exam-schedule__label--day">FRIDAY</span>
        <span class="exam-schedule__label--day">SATURDAY</span>
    </div>

    <div class="exam-schedule__calendar--month" data-calendar-month>
        <?php for ($cell = 0, $day = 1; $cell < $totalCells; $cell++): ?>
            <?php if ($cell % 7 === 0): ?>
                <div class="exam-schedule__calendar--week">
            <?php endif; ?>

            <?php if ($cell < $firstDay || $day > $daysInMonth): ?>
                <div class="exam-schedule__calendar--day is-empty" aria-hidden="true">
                    &nbsp;
                </div>
            <?php else: ?>
                <?php $summary = $calendarSummary[$day] ?? null; ?>
                <button
                    type="button"
                    class="exam-schedule__calendar--day <?= $summary ? 'is-clickable' : '' ?>"
                    data-day="<?= esc($day) ?>"
                >
                    <div class="exam-schedule__calendar--day-number"><?= esc($day) ?></div>

                    <?php if ($summary): ?>
                        <div class="exam-schedule__calendar--tags">
                            <?php if ((int) $summary['open_count'] > 0): ?>
                                <span class="exam-schedule__tag exam-schedule__tag--open">
                                    <?= esc($summary['open_count']) ?> open
                                </span>
                            <?php endif; ?>

                            <?php if ((int) $summary['full_count'] > 0): ?>
                                <span class="exam-schedule__tag exam-schedule__tag--full">
                                    <?= esc($summary['full_count']) ?> full
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </button>
                <?php $day++; ?>
            <?php endif; ?>

            <?php if ($cell % 7 === 6): ?>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</div>