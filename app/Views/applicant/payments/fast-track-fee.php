<!-- app/Views/applicant/payments/fast-track-fee.php -->

<?php
$applicant_number = session()->get('applicant_number') ?? '';
$status           = $existing_payment['payment_status'] ?? null;

// Determines whether to lock the form (already submitted and not rejected)
$isLocked = in_array($status, ['PAID', 'PROCESSING', 'PENDING'], true);

// Status badge display config
$statusConfig = [
    'PENDING'    => ['label' => 'Pending Review',   'class' => 'apf-badge--warning'],
    'PROCESSING' => ['label' => 'Under Review',     'class' => 'apf-badge--info'],
    'PAID'       => ['label' => 'Payment Approved', 'class' => 'apf-badge--success'],
    'REJECTED'   => ['label' => 'Rejected',         'class' => 'apf-badge--danger'],
];
$badge = $status ? ($statusConfig[$status] ?? null) : null;

$hasExistingPayment = ! empty($existing_payment);
?>

<div class="apf-page">

    <!-- ── Hero Banner ──────────────────────────────────────────── -->
    <div class="apf-banner card">

        <?php if ($badge): ?>
            <div class="apf-banner__status-row">
                <span class="apf-status-badge <?= $badge['class'] ?>">
                    <?= $badge['label'] ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="apf-banner__label">Fast Track Fee</div>
        <h2 class="apf-banner__title">Additional Fee for Fast Track Applicants</h2>
        <p class="apf-banner__subtitle">
            This one-time fee covers the Fast Track option you selected on your application. Non-refundable.
        </p>

        <div class="apf-banner__amount">
            <span class="apf-banner__currency">₱</span>
            <span class="apf-banner__number">
                <?= number_format((float)($fee['amount'] ?? 0), 2) ?>
            </span>
        </div>
        <p class="apf-banner__note">One-time · Non-refundable Fast Track fee</p>
    </div>

    <!-- ── Status notice (shown when form is locked) ─────────────── -->
    <?php if ($isLocked): ?>
        <div class="apf-alert apf-alert--<?= $status === 'PAID' ? 'success' : ($status === 'REJECTED' ? 'danger' : 'warning') ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <?php if ($status === 'PAID'): ?>
                    <polyline points="20 6 9 17 4 12" />
                <?php else: ?>
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                <?php endif; ?>
            </svg>
            <span>
                <?php if ($status === 'PAID'): ?>
                    Your Fast Track fee payment has been <strong>approved</strong>. No further action is needed.
                <?php elseif ($status === 'PROCESSING'): ?>
                    Your payment is currently <strong>under review</strong>. You will be notified once verified.
                <?php elseif ($status === 'PENDING'): ?>
                    Your payment has been <strong>submitted</strong> and is awaiting review by the finance office.
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <!-- ── By the counter (manual proof of payment) ───────────────────────────────────── -->
    <?php if (! $isLocked): ?>
        <div class="apf-section apf-section--active card" data-apf-section="upload-proof">
            <div class="apf-section__header">
                <div class="apf-step-badge">Step 1</div>
                <h3 class="apf-section__title">By the Counter</h3>
            </div>

            <!-- Payment Instructions in paying by the university counter -->
            <div class="apf-counter-steps">
                <div class="apf-counter-step">
                    <div class="apf-counter-step__number">1</div>
                    <div class="apf-counter-step__content">
                        <h4 class="apf-counter-step__title">Visit the Cashier Office</h4>
                        <p class="apf-counter-step__text">
                            Go to the university cashier office during official business hours.
                        </p>
                    </div>
                </div>

                <div class="apf-counter-step">
                    <div class="apf-counter-step__number">2</div>
                    <div class="apf-counter-step__content">
                        <h4 class="apf-counter-step__title">Present Your Applicant Number</h4>
                        <p class="apf-counter-step__text">
                            Provide your applicant number from the admission portal for proper payment tagging.
                        </p>
                    </div>
                </div>

                <div class="apf-counter-step">
                    <div class="apf-counter-step__number">3</div>
                    <div class="apf-counter-step__content">
                        <h4 class="apf-counter-step__title">Pay and Keep Your Receipt</h4>
                        <p class="apf-counter-step__text">
                            Submit your payment at the counter and secure the official receipt for upload.
                        </p>
                    </div>
                </div>
            </div>

            <div class="apf-section__header">
                <div class="apf-step-badge">Step 2</div>
                <h3 class="apf-section__title">Upload Proof of Payment</h3>
            </div>

            <!-- inline error box replaces alert() calls in the JS -->
            <div class="apf-alert apf-alert--danger" id="clientError" style="display:none;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                <span id="clientErrorMsg"></span>
            </div>

            <div class="form-field">
                <label for="transactionRef">
                    Transaction / Reference Number <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="transactionRef"
                    name="transaction_reference"
                    placeholder="e.g. GCash ref 1234567890"
                    autocomplete="off"
                    maxlength="50">
            </div>

            <!-- File Upload Zone -->
            <div
                class="apf-upload-zone"
                id="uploadZone"
                ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave(event)"
                ondrop="handleDrop(event)"
                onclick="document.getElementById('proofFile').click()">
                <input
                    type="file"
                    id="proofFile"
                    name="proof_of_payment"
                    accept="image/jpeg,image/png,application/pdf"
                    style="display:none;"
                    onchange="handleFileSelect(event)">

                <div class="apf-upload-zone__idle" id="uploadIdle">
                    <div class="apf-upload-zone__icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 16 12 12 8 16" />
                            <line x1="12" y1="12" x2="12" y2="21" />
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                        </svg>
                    </div>
                    <p class="apf-upload-zone__primary">Click or drag file to this area to upload</p>
                    <p class="apf-upload-zone__secondary">Supports JPG, PNG, PDF · Max 10 MB</p>
                </div>

                <div class="apf-upload-zone__preview" id="uploadPreview" style="display:none;">
                    <div class="apf-file-preview">
                        <div class="apf-file-preview__icon" id="previewIcon">📄</div>
                        <div class="apf-file-preview__info">
                            <span class="apf-file-preview__name" id="previewFileName"></span>
                            <span class="apf-file-preview__size" id="previewFileSize"></span>
                        </div>
                        <button type="button" class="apf-file-preview__remove"
                            onclick="removeFile(event)" title="Remove file">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="apf-alert apf-alert--warning">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <span>
                    Important: Include <strong>FTFEE-#<?= esc($applicant_number) ?></strong> as your
                    payment reference or remarks so the finance office can match your payment.
                </span>
            </div>

            <!-- Submit -->
            <div class="apf-submit-row">
                <button class="apf-submit-btn" id="submitBtn" onclick="submitPayment()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                    Submit Payment
                </button>
                <p class="apf-submit-note">
                    Your submission will be reviewed by the finance office within 1–2 business days.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- ── Existing submission summary (read-only) ───────────────── -->
    <?php if ($existing_payment): ?>
        <div class="apf-section card">

            <div class="apf-section__header">
                <div class="apf-step-badge" style="background:#6b7280;">Submitted</div>
                <h3 class="apf-section__title">Your Payment Submission</h3>
            </div>

            <div class="apf-info-grid">

                <div class="apf-info-row">
                    <span class="apf-info-row__key">Payment Source</span>
                    <span class="apf-info-row__value">University Cashier Office</span>
                </div>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">Official Receipt Number</span>
                    <span class="apf-info-row__value"><?= esc($existing_payment['reference_num']) ?></span>
                </div>

                <?php if (! empty($existing_payment['proof_of_payment'])): ?>
                    <?php
                        $proofUrl = site_url('fast-track-fee/proof/' . $existing_payment['payment_id']);
                        $proofExt = strtolower(pathinfo($existing_payment['proof_of_payment'], PATHINFO_EXTENSION));
                        $isImageProof = in_array($proofExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    ?>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">Proof of Payment</span>
                        <span class="apf-info-row__value">
                            <button
                                type="button"
                                class="apf-proof-btn"
                                data-proof-url="<?= esc($proofUrl) ?>"
                                data-proof-type="<?= $isImageProof ? 'image' : 'pdf' ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                View Uploaded Proof
                            </button>
                        </span>
                    </div>
                <?php endif; ?>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">Amount Paid</span>
                    <span class="apf-info-row__value">₱<?= number_format((float) $existing_payment['amount_paid'], 2) ?></span>
                </div>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">Payment Status</span>
                    <span class="apf-info-row__value"><?= esc(ucfirst(strtolower($existing_payment['payment_status']))) ?></span>
                </div>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">Submitted On</span>
                    <span class="apf-info-row__value"><?= date('F j, Y · g:i A', strtotime($existing_payment['created_at'])) ?></span>
                </div>

                <?php if (! empty($existing_payment['payment_date'])): ?>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">Payment Confirmed</span>
                        <span class="apf-info-row__value"><?= date('F j, Y · g:i A', strtotime($existing_payment['payment_date'])) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (! empty($existing_payment['remarks'])): ?>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">Remarks</span>
                        <span class="apf-info-row__value"><?= esc($existing_payment['remarks']) ?></span>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>

    <div class="apf-proof-modal" id="proofModal">
        <div class="apf-proof-modal__backdrop"></div>
        <div class="apf-proof-modal__content">
            <button type="button" class="apf-proof-modal__close" id="closeProofModal">&times;</button>
            <img id="proofImageViewer" class="apf-proof-modal__image" style="display:none;" alt="Proof of Payment">
            <iframe id="proofPdfViewer" class="apf-proof-modal__pdf" style="display:none;"></iframe>
        </div>
    </div>

</div>

<script>
    const SUBMIT_URL = "<?= base_url('fast-track-fee/submit') ?>";
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('proofModal');
    if (!modal) return;

    const imageViewer = document.getElementById('proofImageViewer');
    const pdfViewer    = document.getElementById('proofPdfViewer');
    const closeBtn     = document.getElementById('closeProofModal');

    document.querySelectorAll('.apf-proof-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const url  = btn.dataset.proofUrl;
            const type = btn.dataset.proofType;

            imageViewer.style.display = 'none';
            pdfViewer.style.display = 'none';

            if (type === 'image') {
                imageViewer.src = url;
                imageViewer.style.display = 'block';
            } else {
                pdfViewer.src = url;
                pdfViewer.style.display = 'block';
            }

            modal.classList.add('active');
        });
    });

    function closeModal() {
        modal.classList.remove('active');
        imageViewer.src = '';
        pdfViewer.src = '';
    }

    closeBtn.addEventListener('click', closeModal);
    modal.querySelector('.apf-proof-modal__backdrop').addEventListener('click', closeModal);
});
</script>

<script src="<?= base_url('assets/js/payment-fees.js') ?>" defer></script>
