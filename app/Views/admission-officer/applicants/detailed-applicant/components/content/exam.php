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
.exam-review-card__actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.exam-result-btn {
    padding: 7px 12px;
    border: none;
    border-radius: 8px;
    background: #0d1e38;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}

.exam-result-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
}

.exam-result-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.exam-result-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
}

.exam-result-modal__content {
    position: relative;
    z-index: 2;
    width: min(460px, 92vw);
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
}

.exam-result-modal__header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.exam-result-modal__header h3 {
    margin: 0;
    font-size: 15px;
    color: #0d1e38;
}

.exam-result-modal__header button {
    border: none;
    background: transparent;
    font-size: 24px;
    cursor: pointer;
}

.exam-result-modal__body {
    padding: 20px;
}

.exam-result-field {
    margin-bottom: 14px;
}

.exam-result-field label {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
}

.exam-result-field label span {
    color: #dc2626;
}

.exam-result-field select,
.exam-result-field input,
.exam-result-field textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-family: inherit;
}

.exam-result-error {
    color: #dc2626;
    font-size: 12px;
    margin: 8px 0;
}

.exam-result-submit {
    width: 100%;
    padding: 11px 14px;
    border: none;
    border-radius: 8px;
    background: #2563eb;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
}
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

            <div class="exam-review-card__actions">
                <span class="exam-badge <?= $badge['cls'] ?>">
                    <?= esc($badge['label']) ?>
                </span>

                <button
                    type="button"
                    class="exam-result-btn"
                    onclick="openExamResultModal()">
                    Add Result
                </button>
            </div>
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
            <div class="exam-detail-item">
                <label>Result Status</label>
                <span><?= esc($booking['result_status'] ?? 'PENDING') ?></span>
            </div>

            <?php if (! empty($booking['score'])): ?>
                <div class="exam-detail-item">
                    <label>Score</label>
                    <span><?= esc($booking['score']) ?></span>
                </div>
            <?php endif; ?>

            <?php if (! empty($booking['result_remarks'])): ?>
                <div class="exam-detail-item" style="grid-column:1/-1;">
                    <label>Result Remarks</label>
                    <span><?= esc($booking['result_remarks']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="exam-result-modal" id="examResultModal">
        <div class="exam-result-modal__backdrop" onclick="closeExamResultModal()"></div>

        <div class="exam-result-modal__content">
            <div class="exam-result-modal__header">
                <h3>Add Exam Result</h3>
                <button type="button" onclick="closeExamResultModal()">&times;</button>
            </div>

            <div class="exam-result-modal__body">
                <input type="hidden" id="examBookingId" value="<?= (int) $booking['exam_booking_id'] ?>">

                <div class="exam-result-field">
                    <label>Result Status <span>*</span></label>
                    <select id="resultStatus">
                        <option value="">Select result</option>
                        <option value="PASSED">Passed</option>
                        <option value="FAILED">Failed</option>
                        <option value="PENDING">Pending</option>
                    </select>
                </div>

                <div class="exam-result-field">
                    <label>Score</label>
                    <input type="number" id="resultScore" min="0" max="100" step="0.01">
                </div>

                <div class="exam-result-field">
                    <label>Remarks</label>
                    <textarea id="resultRemarks" rows="4"></textarea>
                </div>

                <p class="exam-result-error" id="examResultError"></p>

                <button type="button" class="exam-result-submit" onclick="submitExamResult()">
                    Save Result
                </button>
            </div>
        </div>
    </div>

<?php endif; ?>

</div>


