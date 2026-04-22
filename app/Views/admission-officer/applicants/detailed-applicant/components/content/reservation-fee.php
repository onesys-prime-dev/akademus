<?php
$payment = $payment ?? null;

$statusConfig = [
    'PENDING'  => ['label' => 'Pending',  'cls' => 'pr-status--pending',  'icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
    'PAID'     => ['label' => 'Approved', 'cls' => 'pr-status--approved', 'icon' => '<polyline points="20 6 9 17 4 12"/>'],
    'REJECTED' => ['label' => 'Rejected', 'cls' => 'pr-status--rejected', 'icon' => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>'],
];
?>
<?php echo view('admission-officer/applicants/detailed-applicant/components/content/_payment_styles') ?>

<div class="application-content__panel">

<?php if (! $payment): ?>

    <div class="pr-empty">
        <div class="pr-empty__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <div class="pr-empty__title">No Payment Submitted</div>
        <div class="pr-empty__desc">The applicant has not submitted a reservation fee payment yet.</div>
    </div>

<?php else:
    $st         = $payment['payment_status'];
    $cfg        = $statusConfig[$st] ?? ['label' => $st, 'cls' => 'pr-status--pending', 'icon' => ''];
    $isPending  = $st === 'PENDING';
    $ext        = strtolower(pathinfo($payment['proof_of_payment'] ?? '', PATHINFO_EXTENSION));
    $isImage    = in_array($ext, ['jpg','jpeg','png','gif','webp']);
    $proofUrl   = $payment['proof_of_payment']
        ? base_url('admission-officer/payments/' . $payment['payment_id'] . '/proof')
        : null;
    $pid = (int) $payment['payment_id'];
?>

    <!-- ── Status Banner ── -->
    <div class="pr-status-banner pr-status-banner--<?= $st === 'PAID' ? 'approved' : ($st === 'REJECTED' ? 'rejected' : 'pending') ?>">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><?= $cfg['icon'] ?></svg>
        <span><?= esc($cfg['label']) ?> &mdash; Reservation Fee</span>
        <?php if (! empty($payment['reviewed_at'])): ?>
        <span class="pr-status-banner__by">
            Reviewed by <?= esc(trim(($payment['reviewer_firstname'] ?? '') . ' ' . ($payment['reviewer_lastname'] ?? ''))) ?>
            &middot; <?= esc(date('M j, Y g:i A', strtotime($payment['reviewed_at']))) ?>
        </span>
        <?php endif; ?>
    </div>

    <!-- ── Main layout ── -->
    <div class="pr-layout">

        <!-- Left: Details -->
        <div class="pr-details">
            <div class="pr-section-label">Payment Information</div>

            <div class="pr-info-grid">
                <div class="pr-info-item">
                    <div class="pr-info-label">Amount Paid</div>
                    <div class="pr-info-value pr-info-value--amount">₱<?= number_format((float)$payment['amount_paid'], 2) ?></div>
                </div>
                <div class="pr-info-item">
                    <div class="pr-info-label">Date Submitted</div>
                    <div class="pr-info-value"><?= $payment['created_at'] ? esc(date('M j, Y', strtotime($payment['created_at']))) : '—' ?></div>
                    <?php if ($payment['created_at']): ?>
                    <div class="pr-info-sub"><?= esc(date('g:i A', strtotime($payment['created_at']))) ?></div>
                    <?php endif; ?>
                </div>
                <div class="pr-info-item">
                    <div class="pr-info-label">Reference Number</div>
                    <div class="pr-info-value pr-info-value--mono"><?= esc($payment['reference_num'] ?? '—') ?></div>
                </div>
                <div class="pr-info-item">
                    <div class="pr-info-label">Payment Method</div>
                    <div class="pr-info-value"><?= esc($payment['method_name'] ?? '—') ?></div>
                </div>
                <?php if (! empty($payment['account_name'])): ?>
                <div class="pr-info-item">
                    <div class="pr-info-label">Account Name</div>
                    <div class="pr-info-value"><?= esc($payment['account_name']) ?></div>
                </div>
                <div class="pr-info-item">
                    <div class="pr-info-label">Account Number</div>
                    <div class="pr-info-value pr-info-value--mono"><?= esc($payment['account_number'] ?? '—') ?></div>
                </div>
                <?php endif; ?>
                <?php if (! empty($payment['payment_date'])): ?>
                <div class="pr-info-item" style="grid-column:1/-1;">
                    <div class="pr-info-label">Approved On</div>
                    <div class="pr-info-value"><?= esc(date('M j, Y g:i A', strtotime($payment['payment_date']))) ?></div>
                </div>
                <?php endif; ?>
                <?php if (! empty($payment['remarks'])): ?>
                <div class="pr-info-item pr-info-item--full">
                    <div class="pr-info-label">Remarks</div>
                    <div class="pr-info-value"><?= esc($payment['remarks']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Proof -->
        <div class="pr-proof-col">
            <div class="pr-section-label">Proof of Payment</div>

            <?php if ($proofUrl): ?>
                <?php if ($isImage): ?>
                <div class="pr-proof-thumb" data-lightbox data-src="<?= esc($proofUrl) ?>">
                    <img src="<?= esc($proofUrl) ?>" alt="Proof of Payment">
                    <div class="pr-proof-thumb__overlay">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        View Full
                    </div>
                </div>
                <a href="<?= esc($proofUrl) ?>" target="_blank" class="pr-proof-open-link">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Open in new tab
                </a>
                <?php else: ?>
                <a href="<?= esc($proofUrl) ?>" target="_blank" class="pr-proof-file-btn">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <div>
                        <div style="font-weight:700;font-size:13px;">View PDF</div>
                        <div style="font-size:11px;opacity:.7;margin-top:1px;">Click to open</div>
                    </div>
                </a>
                <?php endif; ?>
            <?php else: ?>
                <div class="pr-proof-none">No file attached</div>
            <?php endif; ?>
        </div>

    </div><!-- /.pr-layout -->

    <!-- ── Action Area (pending only) ── -->
    <?php if ($isPending): ?>
    <div class="pr-action-area" id="pr-action-<?= $pid ?>">
        <div class="pr-action-header">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Review Payment
        </div>
        <div class="pr-action-body">
            <div class="pr-remarks-label">Remarks <span>(required on reject, optional on approve)</span></div>
            <textarea class="pr-remarks-input" id="pr-remarks-<?= $pid ?>" placeholder="Add remarks for the applicant…" rows="3"></textarea>
            <p class="pr-action-err" id="pr-err-<?= $pid ?>"></p>
            <div class="pr-action-btns">
                <button class="pr-btn pr-btn--approve"
                    data-payment-action
                    data-payment-id="<?= $pid ?>"
                    data-action="approve"
                    data-panel="admission">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Approve Payment
                </button>
                <button class="pr-btn pr-btn--reject"
                    data-payment-action
                    data-payment-id="<?= $pid ?>"
                    data-action="reject"
                    data-panel="admission">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Reject Payment
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

</div><!-- .application-content__panel -->

<!-- ── Lightbox ── -->
<div class="pr-lightbox" id="pr-lightbox" data-lightbox-overlay>
    <button class="pr-lightbox__close" data-lightbox-close>&times;</button>
    <img id="pr-lightbox-img" src="" alt="Proof of Payment">
</div>