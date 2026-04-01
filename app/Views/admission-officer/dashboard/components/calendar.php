<?php
$monthName     = date('F', mktime(0, 0, 0, $calMonth, 1, $calYear));
$daysInMonth   = cal_days_in_month(CAL_GREGORIAN, $calMonth, $calYear);
$firstDayOfWeek = (int) date('w', mktime(0, 0, 0, $calMonth, 1, $calYear)); // 0=Sun
$today         = (int) date('j');
$isCurrentMY   = ((int) date('n') === $calMonth && (int) date('Y') === $calYear);

// Build grid cells: null = empty, int = day number
$cells = array_fill(0, $firstDayOfWeek, null);
for ($d = 1; $d <= $daysInMonth; $d++) {
    $cells[] = $d;
}
while (count($cells) % 7 !== 0) {
    $cells[] = null;
}
$weeks = array_chunk($cells, 7);
?>
<div class="calendar card">
    <div class="calendar__months">
        <div class="calendar__months--prev">
            <img class="calendar__months--prev--img" src="<?= base_url('assets/images/icons/less-than.svg') ?>" alt="">
        </div>

        <div class="calendar__months--current">
            <span class="calendar__current--month"><?= $monthName ?></span>
            <span class="calendar__current--year"><?= $calYear ?></span>
        </div>

        <div class="calendar__months--next">
            <img class="calendar__months--next--img" src="<?= base_url('assets/images/icons/less-than.svg') ?>" alt="">
        </div>
    </div>

    <div class="calendar__grid">
        <div class="calendar__row">
            <div class="calendar__day calendar__day--label">Sun</div>
            <div class="calendar__day calendar__day--label">Mon</div>
            <div class="calendar__day calendar__day--label">Tue</div>
            <div class="calendar__day calendar__day--label">Wed</div>
            <div class="calendar__day calendar__day--label">Thu</div>
            <div class="calendar__day calendar__day--label">Fri</div>
            <div class="calendar__day calendar__day--label">Sat</div>
        </div>

        <?php foreach ($weeks as $week): ?>
        <div class="calendar__row">
            <?php foreach ($week as $day): ?>
                <?php if ($day === null): ?>
                    <div class="calendar__day calendar__day--inactive"></div>
                <?php else: ?>
                    <?php
                    $hasExam = isset($calendarSummary[$day]);
                    $isToday = $isCurrentMY && ($day === $today);
                    $cls = 'calendar__day';
                    if ($isToday)  $cls .= ' calendar__day--active';
                    if ($hasExam)  $cls .= ' calendar__day--has-exam';
                    ?>
                    <div class="<?= $cls ?>" title="<?= $hasExam ? $calendarSummary[$day]['total_schedules'] . ' exam(s)' : '' ?>">
                        <?= $day ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.calendar__day--has-exam {
    position: relative;
}
.calendar__day--has-exam::after {
    content: '';
    display: block;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--accent, #ffb200);
    position: absolute;
    bottom: 3px;
    left: 50%;
    transform: translateX(-50%);
}
</style>
