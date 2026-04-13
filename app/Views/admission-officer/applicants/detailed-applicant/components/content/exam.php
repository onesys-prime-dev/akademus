<?php
$booking = $booking ?? null;

$statusMap = [
    'booked'    => ['label' => 'Scheduled',  'cls' => 'exam-badge--scheduled'],
    'attended'  => ['label' => 'Attended',   'cls' => 'exam-badge--attended'],
    'cancelled' => ['label' => 'Cancelled',  'cls' => 'exam-badge--cancelled'],
];
?>

<div class="application-content__panel">
<style>
.exam-review-card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;max-width:680px;}
.exam-review-card__header{padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.exam-review-card__title{font-size:14px;font-weight:700;color:#0d1e38;}
.exam-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px 28px;padding:20px;}
.exam-detail-item label{display:block;font-size:11px;font-weight:700;color:#7a8aaa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;}
.exam-detail-item span{font-size:13px;font-weight:600;color:#1e293b;}
.exam-badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;}
.exam-badge--scheduled{background:#dbeafe;color:#1d4ed8;}
.exam-badge--attended{background:#dcfce7;color:#15803d;}
.exam-badge--cancelled{background:#fee2e2;color:#b91c1c;}
.exam-empty{padding:48px 24px;text-align:center;color:#888;}
.exam-empty p:first-child{font-size:15px;font-weight:500;color:#2d3a4f;}
.exam-empty p:last-child{font-size:13px;margin-top:6px;color:#94a3b8;line-height:1.6;}
.exam-divider{border:none;border-top:1px solid #f1f5f9;margin:0 20px;}
</style>

<?php if (! $booking): ?>

    <div class="exam-empty">
        <p>No Exam Booking Found</p>
        <p>This applicant has not booked an entrance exam slot yet.<br>
           They will be able to choose one from the Exam Schedule page.</p>
    </div>

<?php else:
    $badge      = $statusMap[$booking['booking_status']] ?? ['label' => $booking['booking_status'], 'cls' => ''];
    $examDate   = $booking['exam_date']  ? date('l, F j, Y', strtotime($booking['exam_date']))  : '—';
    $startTime  = $booking['start_time'] ? date('g:i A', strtotime($booking['start_time'])) : '—';
    $endTime    = $booking['end_time']   ? date('g:i A', strtotime($booking['end_time']))   : '—';
    $timeRange  = ($booking['start_time'] && $booking['end_time']) ? $startTime . ' – ' . $endTime : $startTime;
    $bookedAt   = $booking['booked_at']  ? date('M j, Y g:i A', strtotime($booking['booked_at']))  : '—';
    $checkedIn  = $booking['checked_in_at'] ? date('M j, Y g:i A', strtotime($booking['checked_in_at'])) : null;
    $slotsLeft  = (int)$booking['max_slots'] - (int)$booking['booked_slots'];
?>

    <div class="exam-review-card">
        <div class="exam-review-card__header">
            <span class="exam-review-card__title">Entrance Exam Details</span>
            <span class="exam-badge <?= $badge['cls'] ?>"><?= esc($badge['label']) ?></span>
        </div>

        <div class="exam-detail-grid">
            <div class="exam-detail-item">
                <label>Exam Date</label>
                <span><?= esc($examDate) ?></span>
            </div>
            <div class="exam-detail-item">
                <label>Time</label>
                <span><?= esc($timeRange) ?></span>
            </div>
            <div class="exam-detail-item" style="grid-column:1/-1;">
                <label>Venue / Location</label>
                <span><?= esc($booking['location'] ?? '—') ?></span>
            </div>
            <div class="exam-detail-item">
                <label>Booked On</label>
                <span><?= esc($bookedAt) ?></span>
            </div>
            <div class="exam-detail-item">
                <label>Slot Capacity</label>
                <span><?= (int)$booking['booked_slots'] ?> / <?= (int)$booking['max_slots'] ?> booked</span>
            </div>
            <?php if ($checkedIn): ?>
            <div class="exam-detail-item" style="grid-column:1/-1;">
                <label>Checked In At</label>
                <span><?= esc($checkedIn) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

</div>
