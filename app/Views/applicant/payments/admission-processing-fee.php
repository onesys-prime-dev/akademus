<?php
// app/Views/applicant/payments/admission_processing_fee.php
// Loaded inside applicant/layouts/main.php via $page_content.
//
// Expected variables from controller:
//   $fee_type         – row from payment_fee_type (or null)
//   $payment_methods  – array of active payment method rows
//   $existing_payment – latest payment row for this applicant + fee type (or null)
//   $applicant_id     – int
//   $applicant_number – string  e.g. "APP-2025-08741"

$existingPayment = $existing_payment ?? null;
$isPaid          = $existingPayment && $existingPayment['payment_status'] === 'PAID';
$isPending       = $existingPayment && $existingPayment['payment_status'] === 'PENDING';
$isProcessing    = $existingPayment && $existingPayment['payment_status'] === 'PROCESSING';
$isRejected      = $existingPayment && $existingPayment['payment_status'] === 'REJECTED';

$feeAmount       = $fee_type['amount'] ?? 500.00;
$referenceCode   = 'ADMFEE-#' . ($applicant_number ?? 'APP-0000-00000');

// Account details per method code.
// NOTE: Move these to a `payment_method_accounts` DB table so finance
//       staff can update them from an admin panel without code changes.
$methodDetails = [
    'GCASH' => [
        'label' => 'Send payment via GCash',
        'rows'  => [
            ['label' => 'Account / Number', 'value' => '0917 000 0000',            'copy' => true],
            ['label' => 'Account Name',     'value' => 'University Finance Office', 'copy' => false],
            ['label' => 'Amount',           'value' => '₱' . number_format($feeAmount, 2), 'copy' => false],
            ['label' => 'Reference / Note', 'value' => $referenceCode,             'copy' => true],
        ],
    ],
    'MAYA' => [
        'label' => 'Send payment via Maya',
        'rows'  => [
            ['label' => 'Account / Number', 'value' => '0998 111 2222',            'copy' => true],
            ['label' => 'Account Name',     'value' => 'University Finance Office', 'copy' => false],
            ['label' => 'Amount',           'value' => '₱' . number_format($feeAmount, 2), 'copy' => false],
            ['label' => 'Reference / Note', 'value' => $referenceCode,             'copy' => true],
        ],
    ],
    'BANK_TRANSFER' => [
        'label' => 'Send payment via Bank Transfer (BDO)',
        'rows'  => [
            ['label' => 'Account / Number', 'value' => '0012 3456 7890',           'copy' => true],
            ['label' => 'Account Name',     'value' => 'University Finance Office', 'copy' => false],
            ['label' => 'Amount',           'value' => '₱' . number_format($feeAmount, 2), 'copy' => false],
            ['label' => 'Reference / Note', 'value' => $referenceCode,             'copy' => true],
        ],
    ],
    'BPI' => [
        'label' => 'Send payment via BPI',
        'rows'  => [
            ['label' => 'Account / Number', 'value' => '9876 5432 1000',           'copy' => true],
            ['label' => 'Account Name',     'value' => 'University Finance Office', 'copy' => false],
            ['label' => 'Amount',           'value' => '₱' . number_format($feeAmount, 2), 'copy' => false],
            ['label' => 'Reference / Note', 'value' => $referenceCode,             'copy' => true],
        ],
    ],
];

// Filter to only methods that have account details defined
$displayMethods = array_filter(
    $payment_methods ?? [],
    fn($m) => isset($methodDetails[$m['code']])
);
$displayMethods = array_values($displayMethods);

// Fallback if nothing passed from controller
if (empty($displayMethods)) {
    $displayMethods = [
        ['payment_method_id' => 3, 'code' => 'GCASH',        'name' => 'GCash'],
        ['payment_method_id' => 4, 'code' => 'MAYA',         'name' => 'Maya'],
        ['payment_method_id' => 1, 'code' => 'BANK_TRANSFER','name' => 'BDO'],
    ];
}

$firstMethod = $displayMethods[0] ?? null;
?>

<div class="apf-wrap">

    <?php if (session()->getFlashdata('success')): ?>
    <div class="apf-flash apf-flash-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="apf-flash apf-flash-error">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
    <?php endif; ?>

    <?php if ($isPaid): ?>
    <div class="apf-status-banner apf-status-paid">
        <div class="apf-status-icon">✓</div>
        <div>
            <strong>Payment Confirmed</strong>
            <p>Your admission processing fee has been verified. Reference: <code><?= esc($existingPayment['reference_num']) ?></code></p>
        </div>
    </div>

    <?php elseif ($isPending || $isProcessing): ?>
    <div class="apf-status-banner apf-status-pending">
        <div class="apf-status-icon">⏳</div>
        <div>
            <strong>Payment Under Review</strong>
            <p>We received your submission and are verifying your payment. This usually takes 1–2 business days.</p>
        </div>
    </div>

    <?php elseif ($isRejected): ?>
    <div class="apf-status-banner apf-status-rejected">
        <div class="apf-status-icon">✕</div>
        <div>
            <strong>Payment Rejected</strong>
            <p>Your payment submission was not verified. Please re-submit with the correct proof of payment below.</p>
        </div>
    </div>
    <?php endif; ?>


    <!-- ══════════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════════ -->
    <div class="apf-hero">
        <div class="apf-hero-content">
            <p class="apf-eyebrow">Admission Processing Fee</p>
            <h2 class="apf-title">Pay to Submit Your Application</h2>
            <p class="apf-subtitle">A one-time processing fee is required to officially submit your admission application. Non-refundable.</p>

            <div class="apf-price-row">
                <span class="apf-currency">₱</span>
                <span class="apf-amount"><?= number_format($feeAmount, 2) ?></span>
            </div>
            <p class="apf-price-note">One-time · Non-refundable processing fee</p>
        </div>

        <div class="apf-method-tabs" id="methodTabs">
            <?php foreach ($displayMethods as $i => $method): ?>
            <button
                class="apf-method-btn <?= $i === 0 ? 'active' : '' ?>"
                data-code="<?= esc($method['code']) ?>"
                data-id="<?= esc($method['payment_method_id']) ?>"
                onclick="apfSelectMethod(this)"
                type="button"
            >
                <?= esc($method['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- ══════════════════════════════════════════════════
         BODY
    ══════════════════════════════════════════════════ -->
    <div class="apf-body">

        <?php if (! $isPaid && ! $isPending && ! $isProcessing): ?>

            <!-- ── STEP 1 ── -->
            <div class="apf-step">
                <div class="apf-step-header">
                    <div class="apf-step-num">1</div>
                    <h3 class="apf-step-title">Send Your Payment</h3>
                </div>

                <div class="apf-info-box" id="apfPaymentBox">
                    <div class="apf-info-box-header">
                        <span id="apfMethodLabel">
                            <?= esc($methodDetails[$firstMethod['code']]['label'] ?? 'Send payment') ?>
                        </span>
                    </div>
                    <div class="apf-info-rows" id="apfInfoRows">
                        <!-- Populated by apfRenderRows() on DOMContentLoaded -->
                    </div>
                </div>

                <div class="apf-alert apf-alert-warning">
                    <span class="apf-alert-icon">⚠️</span>
                    <div>
                        <strong>Important:</strong> Include <strong><?= esc($referenceCode) ?></strong>
                        as your payment reference or remarks so the finance office can match your payment.
                    </div>
                </div>
            </div>

            <div class="apf-divider"></div>

            <!-- ── STEP 2 ── -->
            <div class="apf-step">
                <div class="apf-step-header">
                    <div class="apf-step-num">2</div>
                    <h3 class="apf-step-title">Upload Proof of Payment</h3>
                </div>

                <?= form_open_multipart(base_url('payments/admission-fee/submit'), ['id' => 'apfForm', 'novalidate' => true]) ?>

                    <?= csrf_field() ?>
                    <input type="hidden" name="applicant_id"        value="<?= esc($applicant_id ?? '') ?>">
                    <input type="hidden" name="payment_fee_type_id" value="<?= esc($fee_type['payment_fee_type_id'] ?? 1) ?>">
                    <input type="hidden" name="payment_method_id"   id="apfMethodId" value="<?= esc($firstMethod['payment_method_id'] ?? '') ?>">
                    <input type="hidden" name="amount"              value="<?= esc($feeAmount) ?>">

                    <!-- Transaction / Reference Number -->
                    <div class="apf-field">
                        <label class="apf-field-label" for="transaction_id">
                            Transaction / Reference Number <span class="apf-req">*</span>
                        </label>
                        <input
                            type="text"
                            id="transaction_id"
                            name="transaction_id"
                            class="apf-input <?= isset($errors['transaction_id']) ? 'apf-input-error' : '' ?>"
                            placeholder="e.g. GCash ref 1234567890"
                            value="<?= esc(old('transaction_id')) ?>"
                            autocomplete="off"
                            required
                        >
                        <?php if (isset($errors['transaction_id'])): ?>
                            <span class="apf-field-error"><?= esc($errors['transaction_id']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Proof of Payment Upload -->
                    <div class="apf-field">
                        <label class="apf-field-label">
                            Proof of Payment <span class="apf-req">*</span>
                        </label>

                        <div class="apf-dropzone <?= isset($errors['proof_of_payment']) ? 'apf-dropzone-error' : '' ?>" id="apfDropzone">
                            <input
                                type="file"
                                name="proof_of_payment"
                                id="apfFileInput"
                                accept="image/png,image/jpeg,image/jpg,application/pdf"
                                onchange="apfHandleFile(this.files[0])"
                            >
                            <div class="apf-dropzone-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="16 16 12 12 8 16"/>
                                    <line x1="12" y1="12" x2="12" y2="21"/>
                                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                                </svg>
                            </div>
                            <p class="apf-dropzone-title">Click or drag file to this area to upload</p>
                            <p class="apf-dropzone-sub">Supports <span>JPG, PNG, PDF</span> · Max 2 MB</p>
                        </div>

                        <?php if (isset($errors['proof_of_payment'])): ?>
                            <span class="apf-field-error"><?= esc($errors['proof_of_payment']) ?></span>
                        <?php endif; ?>

                        <!-- File preview (shown after selection) -->
                        <div class="apf-file-preview" id="apfFilePreview">
                            <div class="apf-file-preview-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <div class="apf-file-preview-info">
                                <span class="apf-file-name" id="apfFileName"></span>
                                <span class="apf-file-size" id="apfFileSize"></span>
                            </div>
                            <button type="button" class="apf-file-remove" onclick="apfRemoveFile()" title="Remove file">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="apf-submit-area">
                        <button type="submit" class="apf-submit-btn">
                            Submit Payment Confirmation
                        </button>
                        <p class="apf-submit-note">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            Your submission is secure. Processing may take 1–2 business days.
                        </p>
                    </div>

                <?= form_close() ?>
            </div>

        <?php else: ?>

            <!-- ── READ-ONLY SUMMARY (already submitted) ── -->
            <div class="apf-summary">
                <h3 class="apf-step-title">Your Submission</h3>
                <div class="apf-info-box" style="margin-top: 16px;">
                    <div class="apf-info-rows">
                        <div class="apf-info-row">
                            <span class="apf-info-label">Reference No.</span>
                            <span class="apf-info-value"><?= esc($existingPayment['reference_num']) ?></span>
                        </div>
                        <div class="apf-info-row">
                            <span class="apf-info-label">Transaction ID</span>
                            <span class="apf-info-value"><?= esc($existingPayment['transaction_id'] ?? '—') ?></span>
                        </div>
                        <div class="apf-info-row">
                            <span class="apf-info-label">Amount</span>
                            <span class="apf-info-value">₱<?= number_format($existingPayment['amount'], 2) ?></span>
                        </div>
                        <div class="apf-info-row">
                            <span class="apf-info-label">Status</span>
                            <span class="apf-badge apf-badge-<?= strtolower(esc($existingPayment['payment_status'])) ?>">
                                <?= esc($existingPayment['payment_status']) ?>
                            </span>
                        </div>
                        <div class="apf-info-row">
                            <span class="apf-info-label">Submitted</span>
                            <span class="apf-info-value">
                                <?= esc(date('F j, Y g:i A', strtotime($existingPayment['created_at']))) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div><!-- /.apf-body -->
</div><!-- /.apf-wrap -->


<!-- ══════════════════════════════════════════════════
     INLINE SCRIPT
     (keep here so $methodDetails is available via PHP → JS)
══════════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    // ── Data from PHP ────────────────────────────────────────────────
    const METHOD_DETAILS = <?= json_encode($methodDetails) ?>;

    const PLACEHOLDERS = {
        GCASH:        'e.g. GCash ref 1234567890',
        MAYA:         'e.g. Maya ref 9876543210',
        BANK_TRANSFER:'e.g. BDO ref 1122334455',
        BPI:          'e.g. BPI ref 5544332211',
    };

    // ── Render info rows ─────────────────────────────────────────────
    function apfRenderRows(code) {
        const details   = METHOD_DETAILS[code];
        const container = document.getElementById('apfInfoRows');
        if (!details || !container) return;

        container.innerHTML = details.rows.map((r, i) => `
            <div class="apf-info-row">
                <span class="apf-info-label">${r.label}</span>
                <span class="apf-info-value" id="apfVal${i}">${r.value}</span>
                ${r.copy
                    ? `<button type="button" class="apf-copy-btn" id="apfCopy${i}"
                               onclick="apfCopy(${i}, '${r.value.replace(/'/g, "\\'")}')">Copy</button>`
                    : ''}
            </div>
        `).join('');
    }

    // ── Select payment method ────────────────────────────────────────
    window.apfSelectMethod = function (btn) {
        document.querySelectorAll('.apf-method-btn')
                .forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const code = btn.dataset.code;
        const id   = btn.dataset.id;

        // Update hidden field
        const hiddenId = document.getElementById('apfMethodId');
        if (hiddenId) hiddenId.value = id;

        // Update label
        const label = document.getElementById('apfMethodLabel');
        if (label && METHOD_DETAILS[code]) {
            label.textContent = METHOD_DETAILS[code].label;
        }

        // Animate box
        const box = document.getElementById('apfPaymentBox');
        if (box) {
            box.style.animation = 'none';
            requestAnimationFrame(() => {
                box.style.animation = 'apfFadeIn .25s ease both';
            });
        }

        // Re-render rows
        apfRenderRows(code);

        // Update placeholder
        const inp = document.getElementById('transaction_id');
        if (inp) inp.placeholder = PLACEHOLDERS[code] ?? 'e.g. ref 1234567890';
    };

    // ── Copy to clipboard ────────────────────────────────────────────
    window.apfCopy = function (idx, val) {
        navigator.clipboard.writeText(val).then(() => {
            const btn = document.getElementById(`apfCopy${idx}`);
            if (!btn) return;
            btn.textContent = '✓ Copied';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = 'Copy';
                btn.classList.remove('copied');
            }, 1800);
        });
    };

    // ── File handling ────────────────────────────────────────────────
    window.apfHandleFile = function (file) {
        if (!file) return;
        document.getElementById('apfFileName').textContent = file.name;
        document.getElementById('apfFileSize').textContent = apfFormatSize(file.size);
        document.getElementById('apfFilePreview').classList.add('visible');
    };

    window.apfRemoveFile = function () {
        document.getElementById('apfFileInput').value = '';
        document.getElementById('apfFilePreview').classList.remove('visible');
    };

    function apfFormatSize(bytes) {
        if (bytes < 1024)    return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    // ── Drag & drop ──────────────────────────────────────────────────
    const dz = document.getElementById('apfDropzone');
    if (dz) {
        dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('drag-over'); });
        dz.addEventListener('dragleave', ()  => dz.classList.remove('drag-over'));
        dz.addEventListener('drop', e => {
            e.preventDefault();
            dz.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file) {
                document.getElementById('apfFileInput').files = e.dataTransfer.files;
                apfHandleFile(file);
            }
        });
    }

    // ── Init on load ─────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const firstBtn = document.querySelector('.apf-method-btn');
        if (firstBtn) apfSelectMethod(firstBtn);
    });

}());
</script>