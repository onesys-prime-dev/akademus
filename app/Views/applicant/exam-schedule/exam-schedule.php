<?php
$booking   = $booking   ?? null;
$available = $available ?? [];

$bookingStatusLabels = [
    'booked'    => ['label' => 'Scheduled',  'cls' => 'es-badge--scheduled'],
    'attended'  => ['label' => 'Attended',   'cls' => 'es-badge--attended'],
    'cancelled' => ['label' => 'Cancelled',  'cls' => 'es-badge--cancelled'],
];
?>

<style>
.es-page { max-width: 680px; margin: 0 auto; padding: 0 0 48px; }

/* ── Card shell ── */
.es-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 20px; }
.es-card__header { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.es-card__title  { font-size: 15px; font-weight: 700; color: #0d1e38; margin: 0; }
.es-card__sub    { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.es-card__body   { padding: 24px; }

/* ── Badge ── */
.es-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.es-badge--scheduled { background: #dbeafe; color: #1d4ed8; }
.es-badge--attended  { background: #dcfce7; color: #15803d; }
.es-badge--cancelled { background: #fee2e2; color: #b91c1c; }
.es-badge--open      { background: #dcfce7; color: #15803d; }
.es-badge--full      { background: #f1f5f9; color: #64748b; }

/* ── Detail grid ── */
.es-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 28px; }
.es-detail-item label { display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
.es-detail-item span  { font-size: 14px; font-weight: 600; color: #1e293b; }

/* ── Reminder ── */
.es-reminder { margin-top: 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; display: flex; gap: 10px; align-items: flex-start; }
.es-reminder__icon { flex-shrink: 0; color: #d97706; margin-top: 1px; }
.es-reminder__text { font-size: 12px; color: #92400e; line-height: 1.55; }
.es-reminder__text strong { font-weight: 700; }

/* ── Divider ── */
.es-divider { border: none; border-top: 1px solid #f1f5f9; margin: 20px 0; }

/* ── Available slots table ── */
.es-slots-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.es-slots-table thead tr { background: #f8fafc; }
.es-slots-table th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; color: #7a8aaa; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid #e2e8f0; }
.es-slots-table td { padding: 13px 14px; border-bottom: 1px solid #f1f5f9; color: #2d3a4f; vertical-align: middle; }
.es-slots-table tbody tr:last-child td { border-bottom: none; }
.es-slots-table tbody tr:hover { background: #fafbfc; }

/* ── Buttons ── */
.es-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 7px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; transition: opacity .15s; }
.es-btn:disabled { opacity: .5; cursor: not-allowed; }
.es-btn--book   { background: var(--primary, #0040a6); color: #fff; }
.es-btn--cancel { background: #fee2e2; color: #b91c1c; }
.es-btn:hover:not(:disabled) { opacity: .87; }

/* ── Empty state ── */
.es-empty { text-align: center; padding: 40px 20px; }
.es-empty__icon  { width: 52px; height: 52px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
.es-empty__title { font-size: 14px; font-weight: 700; color: #2d3a4f; margin-bottom: 5px; }
.es-empty__desc  { font-size: 13px; color: #94a3b8; line-height: 1.6; }

/* ── Toast ── */
#es-toast { position:fixed;bottom:24px;right:24px;padding:12px 20px;border-radius:8px;font-size:13px;font-weight:600;z-index:99999;box-shadow:0 8px 24px rgba(0,0,0,.15);transition:opacity .25s;opacity:0;pointer-events:none; }
#es-toast.success { background:#15803d;color:#fff; }
#es-toast.error   { background:#b91c1c;color:#fff; }

.es-badge--result-pending { background:#f1f5f9; color:#64748b; }
.es-badge--result-passed  { background:#dcfce7; color:#15803d; }
.es-badge--result-failed  { background:#fee2e2; color:#b91c1c; }
</style>

<div class="es-page">

<?php if ($booking): ?>

    <?php
        $badge     = $bookingStatusLabels[$booking['booking_status']] ?? ['label' => $booking['booking_status'], 'cls' => ''];
        $examDate  = $booking['exam_date']  ? date('l, F j, Y', strtotime($booking['exam_date'])) : '—';
        $startTime = $booking['start_time'] ? date('g:i A', strtotime($booking['start_time'])) : '—';
        $endTime   = $booking['end_time']   ? date('g:i A', strtotime($booking['end_time']))   : '—';
        $timeRange = ($booking['start_time'] && $booking['end_time']) ? $startTime . ' – ' . $endTime : $startTime;
        $bookedAt  = $booking['booked_at']  ? date('M j, Y g:i A', strtotime($booking['booked_at'])) : '—';
        $canCancel = $booking['booking_status'] === 'booked';
    ?>

    <!-- ── Active Booking Card ── -->
    <div class="es-card">
        <div class="es-card__header">
            <div>
                <div class="es-card__title">My Exam Schedule</div>
                <div class="es-card__sub">Your booked entrance examination slot</div>
            </div>
            <span class="es-badge <?= $badge['cls'] ?>"><?= esc($badge['label']) ?></span>
        </div>
        <div class="es-card__body">

            <div class="es-detail-grid">
                <div class="es-detail-item">
                    <label>Exam Date</label>
                    <span><?= esc($examDate) ?></span>
                </div>
                <div class="es-detail-item">
                    <label>Time</label>
                    <span><?= esc($timeRange) ?></span>
                </div>
                <div class="es-detail-item" style="grid-column:1/-1;">
                    <label>Venue / Location</label>
                    <span><?= esc($booking['location'] ?? '—') ?></span>
                </div>
                <div class="es-detail-item">
                    <label>Booked On</label>
                    <span><?= esc($bookedAt) ?></span>
                </div>
                <?php if ($booking['booking_status'] === 'attended' && $booking['checked_in_at']): ?>
                <div class="es-detail-item">
                    <label>Checked In At</label>
                    <span><?= esc(date('M j, Y g:i A', strtotime($booking['checked_in_at']))) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php
                $resultStatus = strtoupper($booking['result_status'] ?? 'PENDING');

                $resultMap = [
                    'PENDING' => ['label' => 'Pending Result', 'cls' => 'es-badge--result-pending'],
                    'PASSED'  => ['label' => 'Passed', 'cls' => 'es-badge--result-passed'],
                    'FAILED'  => ['label' => 'Failed', 'cls' => 'es-badge--result-failed'],
                ];

                $resultBadge = $resultMap[$resultStatus] ?? $resultMap['PENDING'];
            ?>

            <?php if ($booking['booking_status'] === 'attended'): ?>
                <hr class="es-divider">

                <div class="es-card__title" style="margin-bottom:14px;">
                    Exam Result
                </div>

                <div class="es-detail-grid">
                    <div class="es-detail-item">
                        <label>Result Status</label>
                        <span>
                            <span class="es-badge <?= $resultBadge['cls'] ?>">
                                <?= esc($resultBadge['label']) ?>
                            </span>
                        </span>
                    </div>

                    <?php if (! empty($booking['score'])): ?>
                        <div class="es-detail-item">
                            <label>Score</label>
                            <span><?= esc($booking['score']) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($booking['result_recorded_at'])): ?>
                        <div class="es-detail-item">
                            <label>Result Recorded</label>
                            <span>
                                <?= esc(date('M j, Y g:i A', strtotime($booking['result_recorded_at']))) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($booking['result_remarks'])): ?>
                        <div class="es-detail-item" style="grid-column:1/-1;">
                            <label>Remarks</label>
                            <span><?= esc($booking['result_remarks']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($canCancel): ?>
            <hr class="es-divider">
            <div class="es-reminder">
                <div class="es-reminder__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="es-reminder__text">
                    <strong>Reminder:</strong> Please arrive at least 15 minutes before your scheduled exam time.
                    Bring a valid school ID and your Applicant ID number.
                </div>
            </div>

            <div style="margin-top:18px;">
                <button class="es-btn es-btn--cancel" id="btn-cancel-booking">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Cancel Booking
                </button>
                <p style="font-size:11px;color:#94a3b8;margin-top:6px;">
                    Cancelling will free up this slot and allow you to pick a new one.
                </p>
            </div>
            <?php endif; ?>

        </div>
    </div>

<?php else: ?>

    <!-- ── Available Slots Card ── -->
    <div class="es-card">
        <div class="es-card__header">
            <div>
                <div class="es-card__title">Choose an Exam Slot</div>
                <div class="es-card__sub">Select an available schedule below to book your entrance examination</div>
            </div>
        </div>
        <div class="es-card__body" style="padding:0;">

        <?php if (empty($available)): ?>
            <div class="es-empty">
                <div class="es-empty__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="es-empty__title">No Available Slots</div>
                <div class="es-empty__desc">
                    There are no open exam schedules at this time.<br>
                    Please check back later or contact the admissions office.
                </div>
            </div>
        <?php else: ?>

            <table class="es-slots-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Slots</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($available as $slot):
                    $slotDate  = date('M j, Y (D)', strtotime($slot['exam_date']));
                    $slotStart = date('g:i A', strtotime($slot['start_time']));
                    $slotEnd   = date('g:i A', strtotime($slot['end_time']));
                    $slotTime  = $slotStart . ' – ' . $slotEnd;
                    $avail     = (int)$slot['available_slots'];
                ?>
                <tr>
                    <td><?= esc($slotDate) ?></td>
                    <td><?= esc($slotTime) ?></td>
                    <td><?= esc($slot['location']) ?></td>
                    <td>
                        <span style="font-size:12px;font-weight:600;color:<?= $avail <= 3 ? '#d97706' : '#15803d' ?>;">
                            <?= $avail ?> left
                        </span>
                    </td>
                    <td>
                        <button class="es-btn es-btn--book"
                            data-id="<?= (int)$slot['exam_schedule_id'] ?>"
                            onclick="esBook(this)">
                            Book
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

</div><!-- .es-page -->

<div id="es-toast"></div>

<script>
const ES_BOOK_URL   = '<?= base_url('exam-schedule/book') ?>';
const ES_CANCEL_URL = '<?= base_url('exam-schedule/cancel') ?>';

function esShowToast(msg, type) {
    const t = document.getElementById('es-toast');
    t.textContent = msg;
    t.className   = type;   // 'success' | 'error'
    t.style.opacity = '1';
    setTimeout(() => { t.style.opacity = '0'; }, 3000);
}

async function esBook(btn) {
    const scheduleId = btn.dataset.id;
    btn.disabled = true;
    btn.textContent = 'Booking…';

    const form = new FormData();
    form.append('schedule_id', scheduleId);

    try {
        const res  = await fetch(ES_BOOK_URL, { method: 'POST', body: form });
        const data = await res.json();
        if (data.success) {
            esShowToast(data.message, 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            esShowToast(data.message, 'error');
            btn.disabled = false;
            btn.textContent = 'Book';
        }
    } catch {
        esShowToast('Network error. Please try again.', 'error');
        btn.disabled = false;
        btn.textContent = 'Book';
    }
}

const cancelBtn = document.getElementById('btn-cancel-booking');
if (cancelBtn) {
    cancelBtn.addEventListener('click', async () => {
        if (! confirm('Cancel your current exam booking? You will be able to choose a new slot.')) return;

        cancelBtn.disabled = true;
        cancelBtn.textContent = 'Cancelling…';

        try {
            const res  = await fetch(ES_CANCEL_URL, { method: 'POST', body: new FormData() });
            const data = await res.json();
            if (data.success) {
                esShowToast(data.message, 'success');
                setTimeout(() => location.reload(), 1200);
            } else {
                esShowToast(data.message, 'error');
                cancelBtn.disabled = false;
                cancelBtn.textContent = 'Cancel Booking';
            }
        } catch {
            esShowToast('Network error. Please try again.', 'error');
            cancelBtn.disabled = false;
            cancelBtn.textContent = 'Cancel Booking';
        }
    });
}
</script>
