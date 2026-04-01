<div class="calendar-overview">
    <?= $this->include('admission-officer/dashboard/components/calendar') ?>

    <div class="calendar-overview__exam-schedule">
        <span class="calendar-overview__exam-schedule--header">Upcoming Exam Schedules</span>

        <div class="exam-schedule__cards">
            <?php if (empty($upcomingSchedules)): ?>
                <p style="color:#718096;font-size:13px;padding:8px 0;">No upcoming exam schedules.</p>
            <?php else: ?>
                <?php foreach ($upcomingSchedules as $sched): ?>
                <?php
                    $dateObj  = new \DateTime($sched['exam_date']);
                    $dateLabel = $dateObj->format('F j');
                    $dayLabel  = $dateObj->format('l');
                    $startTime = date('g:i A', strtotime($sched['start_time']));
                    $endTime   = date('g:i A', strtotime($sched['end_time']));
                    $slots     = (int) $sched['booked_slots'] . ' / ' . (int) $sched['max_slots'] . ' booked';
                ?>
                <div class="exam-schedule__card">
                    <div class="exam-schedule__card--row">
                        <div class="exam-schedule__card--date"><?= esc($dateLabel) ?></div>
                        <span class="exam-schedule__card--time"><?= esc($startTime . ' - ' . $endTime . ', ' . $dayLabel) ?></span>
                    </div>

                    <span class="exam-schedule__card--location"><?= esc($sched['location']) ?></span>
                    <span style="font-size:11px;color:#718096;"><?= esc($slots) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
