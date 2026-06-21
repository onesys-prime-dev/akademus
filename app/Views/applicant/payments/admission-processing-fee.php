<!-- app/Views/applicant/payments/admission-processing-fee.php -->

<?php
// FIX: $amount now comes from $fee['amount'] (from payment_fee_type DB row)
// rather than a hardcoded fallback. The controller passes $fee and $amount.
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

$paymentSource = strtoupper($existing_payment['payment_source'] ?? '');
$activeTab = $paymentSource === 'COUNTER'
    ? 'upload-proof'
    : 'online-payment';

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

        <div class="apf-banner__label">Admission Processing Fee</div>
        <h2 class="apf-banner__title">Pay to Submit Your Application</h2>
        <p class="apf-banner__subtitle">
            A one-time processing fee is required to officially submit your admission application. Non-refundable.
        </p>

        <div class="apf-banner__amount">
            <span class="apf-banner__currency">₱</span>
            <span class="apf-banner__number">
                <?= number_format((float)($fee['amount'] ?? 0), 2) ?>
            </span>
        </div>
        <p class="apf-banner__note">One-time · Non-refundable processing fee</p>

        <div class="apf-banner__tabs <?= $hasExistingPayment ? 'apf-banner__tabs--locked' : '' ?>">
            <button
                type="button"
                class="apf-banner__tab <?= $activeTab === 'online-payment' ? 'apf-banner__tab--active' : '' ?>"
                data-apf-tab="online-payment"
                <?= $hasExistingPayment && $activeTab !== 'online-payment' ? 'disabled' : '' ?>>
                Online Payment
            </button>

            <button
                type="button"
                class="apf-banner__tab <?= $activeTab === 'upload-proof' ? 'apf-banner__tab--active' : '' ?>"
                data-apf-tab="upload-proof"
                <?= $hasExistingPayment && $activeTab !== 'upload-proof' ? 'disabled' : '' ?>>
                By the Counter
            </button>
        </div>
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
                    Your admission fee payment has been <strong>approved</strong>. No further action is needed.
                <?php elseif ($status === 'PROCESSING'): ?>
                    Your payment is currently <strong>under review</strong>. You will be notified once verified.
                <?php elseif ($status === 'PENDING'): ?>
                    Your payment has been <strong>submitted</strong> and is awaiting review by the finance office.
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if (! $isLocked): ?>
        <!-- ── Step 1: Send Payment ───────────────────────────────────── -->
        <div
            class="apf-section <?= $activeTab === 'online-payment' ? 'apf-section--active' : '' ?> card"
            data-apf-section="online-payment">
            <div class="apf-section__header">
                <div class="apf-step-badge">Online</div>
                <h3 class="apf-section__title">Select Payment Methods</h3>
            </div>

            <!-- Payment Method Tabs — rendered from DB via controller -->
            <div class="apf-methods" id="paymentMethods">
                <?php foreach ($payment_methods as $i => $method): ?>
                    <button
                        class="apf-method-btn <?= $i === 0 ? 'apf-method-btn--active' : '' ?>"
                        data-method-id="<?= $method['payment_method_id'] ?>"
                        data-method-code="<?= strtolower($method['code']) ?>"
                        data-account-id="<?= $method['payment_method_account_id'] ?? '' ?>"
                        onclick="selectMethod(this)"
                        <?= $isLocked ? 'disabled' : '' ?>
                    >
                        <img
                            src="<?= base_url('assets/images/payment-methods/' . $method['code'] . '.png') ?>"
                            alt="<?= esc($method['name']) ?>"
                            class="apf-method-btn__icon">

                        <?= esc($method['name']) ?>

                        <input type="radio">
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="apf-payment-info">
                <div class="apf-payment-info__method-label" id="methodLabel"></div>
                <div class="apf-info-grid">
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">Account / Number</span>
                        <span class="apf-info-row__value" id="infoAccountNumber">–</span>
                    </div>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">Account Name</span>
                        <span class="apf-info-row__value" id="infoAccountName">–</span>
                    </div>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">Amount</span>
                        <span class="apf-info-row__value">₱<?= number_format((float)($fee['amount'] ?? 0), 2) ?></span>
                    </div>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">Reference / Note</span>
                        <!-- FIX: was missing the ADMFEE- prefix in the dynamic version -->
                        <span class="apf-info-row__value">ADMFEE-#<?= esc($applicant_number) ?></span>
                    </div>
                </div>
            </div>

            <!-- ADMISSION PROCESSING FEE - PAY NOW button -->
            <div class="apf-paynow-row">
                <button
                    type="button"
                    class="apf-paynow-btn"
                    onclick="openCheckoutModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>

                    Pay Now
                </button>

                <p class="apf-paynow-note">
                    Secure online checkout powered by SmartPay.
                </p>
            </div>

            <!-- SMARTPAY CHECKOUT -->
            <div class="smartpay-modal" id="smartpayModal">

                <div class="smartpay-modal-content">

                    <div class="smartpay-header">
                        <h3>SmartPay Checkout</h3>

                        <button
                            type="button"
                            class="smartpay-close"
                            onclick="closeCheckoutModal()">
                            &times;
                        </button>
                    </div>

                    <div class="smartpay-body">

                        <div class="smartpay-amount">
                            ₱<?= number_format((float)($fee['amount'] ?? 0), 2) ?>
                        </div>

                        <p class="smartpay-subtitle">
                            Continue with SmartPay.
                        </p>

                        <div class="smartpay-method-preview">
                            <span id="checkoutMethodName">GCash</span>
                        </div>

                        <button
                            type="button"
                            class="smartpay-proceed-btn"
                            onclick="processSmartPayCheckout(this)">

                            Proceed Payment
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
                    Important: Include <strong>ADMFEE-#<?= esc($applicant_number) ?></strong> as your
                    payment reference or remarks so the finance office can match your payment.
                </span>
            </div>
        </div>
    <?php endif; ?>

    <!-- ── Option 2: By the counter ───────────────────────────────────── -->
    <?php if (! $isLocked): ?>
        <div
            class="apf-section <?= $activeTab === 'upload-proof' ? 'apf-section--active' : '' ?> card"
            data-apf-section="upload-proof">
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

            <!-- FIX: inline error box replaces alert() calls in the JS -->
            <div class="apf-alert apf-alert--danger" id="clientError" style="display:none;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                <span id="clientErrorMsg"></span>
            </div>

            <?php if (! $isLocked): ?>
                <!-- transaction ref, upload zone, submit button here -->
                
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
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <!-- ── Existing submission summary (read-only) ───────────────── -->
    <?php if ($existing_payment): ?>

        <?php
            $paymentSource =
                strtoupper(
                    $existing_payment['payment_source']
                    ?? ''
                );

            $isCounter =
                $paymentSource === 'COUNTER';
        ?>

        <div class="apf-section card">

            <div class="apf-section__header">
                <div
                    class="apf-step-badge"
                    style="background:#6b7280;">
                    Submitted
                </div>

                <h3 class="apf-section__title">
                    Your Payment Submission
                </h3>
            </div>

            <div class="apf-info-grid">

                <div class="apf-info-row">
                    <span class="apf-info-row__key">
                        Payment Source
                    </span>

                    <span class="apf-info-row__value">
                        <?= $isCounter
                            ? 'University Cashier Office'
                            : 'Online Payment' ?>
                    </span>
                </div>

                <?php if (! $isCounter): ?>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">
                            Payment Method
                        </span>

                        <span class="apf-info-row__value">
                            <?= esc(
                                $existing_payment['method_name']
                            ) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">
                        Official Receipt Number
                    </span>

                    <span class="apf-info-row__value">
                        <?= esc(
                            $existing_payment['reference_num']
                        ) ?>
                    </span>
                </div>

                <?php if (
                    $isCounter &&
                    ! empty($existing_payment['proof_of_payment'])
                ): ?>

                    <?php
                        $proofUrl = site_url(
                            'admission-processing-fee/proof/' . $existing_payment['payment_id']
                        );

                        $proofExt = strtolower(
                            pathinfo(
                                $existing_payment['proof_of_payment'],
                                PATHINFO_EXTENSION
                            )
                        );

                        $isImageProof = in_array(
                            $proofExt,
                            ['jpg', 'jpeg', 'png', 'gif', 'webp']
                        );
                    ?>

                    <div class="apf-info-row">
                        <span class="apf-info-row__key">
                            Proof of Payment
                        </span>

                        <span class="apf-info-row__value">

                            <button
                                type="button"
                                class="apf-proof-btn"
                                data-proof-url="<?= esc($proofUrl) ?>"
                                data-proof-type="<?= $isImageProof ? 'image' : 'pdf' ?>">

                                <svg width="16" height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                View Uploaded Proof

                            </button>

                        </span>
                    </div>

                <?php endif; ?>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">
                        Amount Paid
                    </span>

                    <span class="apf-info-row__value">
                        ₱<?= number_format(
                            (float)
                            $existing_payment['amount_paid'],
                            2
                        ) ?>
                    </span>
                </div>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">
                        Payment Status
                    </span>

                    <span class="apf-info-row__value">
                        <?= esc(
                            ucfirst(
                                strtolower(
                                    $existing_payment['payment_status']
                                )
                            )
                        ) ?>
                    </span>
                </div>

                <div class="apf-info-row">
                    <span class="apf-info-row__key">
                        Submitted On
                    </span>

                    <span class="apf-info-row__value">
                        <?= date(
                            'F j, Y · g:i A',
                            strtotime(
                                $existing_payment['created_at']
                            )
                        ) ?>
                    </span>
                </div>

                <?php if (
                    ! empty(
                        $existing_payment['payment_date']
                    )
                ): ?>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">
                            Payment Confirmed
                        </span>

                        <span class="apf-info-row__value">
                            <?= date(
                                'F j, Y · g:i A',
                                strtotime(
                                    $existing_payment['payment_date']
                                )
                            ) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (
                    ! empty(
                        $existing_payment['remarks']
                    )
                ): ?>
                    <div class="apf-info-row">
                        <span class="apf-info-row__key">
                            Remarks
                        </span>

                        <span class="apf-info-row__value">
                            <?= esc(
                                $existing_payment['remarks']
                            ) ?>
                        </span>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    <?php endif; ?>


    <div class="apf-proof-modal" id="proofModal">

        <div class="apf-proof-modal__backdrop"></div>

        <div class="apf-proof-modal__content">

            <button
                type="button"
                class="apf-proof-modal__close"
                id="closeProofModal">
                &times;
            </button>

            <img
                id="proofImageViewer"
                class="apf-proof-modal__image"
                style="display:none;"
                alt="Proof of Payment">

            <iframe
                id="proofPdfViewer"
                class="apf-proof-modal__pdf"
                style="display:none;">
            </iframe>

        </div>

    </div>

</div>

<script>
    const PAYMENT_DETAILS = <?= json_encode($payment_methods, JSON_UNESCAPED_UNICODE) ?>;
</script>

<script>
    const SUBMIT_URL = "<?= base_url('admission-processing-fee/submit') ?>";
</script>

<!-- SMARTPAY modal -->
<script>
    function openCheckoutModal() {
        // get currently selected method for display in modal
        const activeMethod = document.querySelector('.apf-method-btn--active');

        // update checkout modal text
        if (activeMethod) {
            document.getElementById('checkoutMethodName').innerText = activeMethod.innerText.trim();
        }

        document
            .getElementById('smartpayModal')
            .classList
            .add('active');
    }

    function closeCheckoutModal() {
        document
            .getElementById('smartpayModal')
            .classList
            .remove('active');
    }

    async function processSmartPayCheckout(button) {
        button.disabled = true;
        button.innerText = 'Creating Checkout...';

        try {
            const activeMethod = document.querySelector('.apf-method-btn--active');

            if (!activeMethod) {
                throw new Error('No payment method selected.');
            }

            const paymentMethod = activeMethod.dataset.methodCode;

            const response = await fetch(
                "<?= base_url('payment/admission-fee') ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    body: JSON.stringify({
                        payment_method: paymentMethod
                    })
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                if (data.data.redirect_url) {

                    window.location.href = data.data.redirect_url;

                } else {
                    alert(
                        'Checkout URL not returned by SmartPay.'
                    );
                }
            } else {
                alert(data.message || 'Failed to create SmartPay checkout.');
            }
        } catch (error) {

            console.error('Checkout error:', error);

            alert('An error occurred while creating checkout.');

        } finally {

            button.disabled = false;
            button.innerText = 'Proceed Payment';

        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.apf-banner__tab');
        const sections = document.querySelectorAll('.apf-section[data-apf-section]');

        if (!tabs.length || !sections.length) return;

        function activateTab(target) {
            tabs.forEach(tab => {
                tab.classList.toggle(
                    'apf-banner__tab--active',
                    tab.dataset.apfTab === target
                );
            });

            sections.forEach(section => {
                section.classList.toggle(
                    'apf-section--active',
                    section.dataset.apfSection === target
                );
            });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                if (tab.disabled) return;

                activateTab(tab.dataset.apfTab);
            });
        });

        const activeTab = document.querySelector('.apf-banner__tab--active');
        if (activeTab) {
            activateTab(activeTab.dataset.apfTab);
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const modal =
        document.getElementById('proofModal');

    if (!modal) return;

    const imageViewer =
        document.getElementById('proofImageViewer');

    const pdfViewer =
        document.getElementById('proofPdfViewer');

    const closeBtn =
        document.getElementById('closeProofModal');

    document
        .querySelectorAll('.apf-proof-btn')
        .forEach(btn => {

            btn.addEventListener('click', () => {

                const url =
                    btn.dataset.proofUrl;

                const type =
                    btn.dataset.proofType;

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

    modal
        .querySelector('.apf-proof-modal__backdrop')
        .addEventListener('click', closeModal);
});
</script>

<script src="<?= base_url('assets/js/payment-fees.js') ?>" defer></script>