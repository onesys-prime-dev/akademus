<?php
$applicantNumber = session()->get('applicant_number');
$referenceCode   = 'ADMFEE-' . esc($applicantNumber);
?>

<div class="payment-page">

    <!-- HERO CARD -->
    <div class="payment-hero-card">
        <span class="payment-label">Admission Processing Fee</span>
        <h2 class="payment-title">Pay to Submit Your Application</h2>
        <p class="payment-subtext">
            A one-time processing fee is required to officially submit your admission application. Non-refundable.
        </p>
        <h1 class="payment-amount">₱500.00</h1>
        <span class="payment-note">One-time · Non-refundable processing fee</span>

        <div class="payment-methods">
            <button class="method-btn active" data-method="GCASH">GCash</button>
            <button class="method-btn"        data-method="MAYA">Maya</button>
            <button class="method-btn"        data-method="BDO">BDO</button>
            <button class="method-btn"        data-method="BPI">BPI</button>
        </div>
    </div>

    <form action="<?= base_url('payment/admission-processing-fee/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <input type="hidden" name="payment_method_id" id="paymentMethodId" value="">

        <!-- ── STEP 1 ── -->
        <div class="payment-step-card">
            <div class="step-heading">
                <span class="step-num">1</span>
                <h3>Send Your Payment</h3>
            </div>

            <div class="account-box">
                <p class="account-box-label" id="accountBoxLabel">Send payment via GCash</p>

                <div class="account-row">
                    <span>Account / Number</span>
                    <div class="account-value-group">
                        <p id="accountNumber">—</p>
                        <button type="button" class="copy-btn" data-target="accountNumber">Copy</button>
                    </div>
                </div>

                <div class="account-row">
                    <span>Account Name</span>
                    <p id="accountName">—</p>
                </div>

                <div class="account-row">
                    <span>Amount</span>
                    <p>₱500.00</p>
                </div>

                <div class="account-row last">
                    <span>Reference / Note</span>
                    <div class="account-value-group">
                        <p id="accountReference"><?= $referenceCode ?></p>
                        <button type="button" class="copy-btn" data-target="accountReference">Copy</button>
                    </div>
                </div>
            </div>

            <div class="payment-warning">
                ⚠️ <strong>Important:</strong> Include <strong><?= $referenceCode ?></strong>
                as your payment reference or remarks so the finance office can match your payment.
            </div>
        </div>


        <!-- ── STEP 2 ── -->
        <div class="payment-step-card">
            <div class="step-heading">
                <span class="step-num">2</span>
                <h3>Upload Proof of Payment</h3>
            </div>

            <div class="form-group">
                <label class="form-label" for="transaction_id">
                    Transaction / Reference Number <span class="req">*</span>
                </label>
                <input
                    type="text"
                    id="transaction_id"
                    name="transaction_id"
                    class="form-input"
                    placeholder="e.g. GCash ref 1234567890"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">
                    Proof of Payment <span class="req">*</span>
                </label>
                <div class="upload-box" id="uploadBox">
                    <input type="file" id="proofUpload" name="proof_of_payment"
                           accept="image/png,image/jpeg,image/jpg,application/pdf" required>
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 16 12 12 8 16"/>
                            <line x1="12" y1="12" x2="12" y2="21"/>
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                        </svg>
                        <span>Click or drag file to upload</span>
                        <small>JPG, PNG, PDF · Max 2MB</small>
                    </div>
                    <div class="upload-preview" id="uploadPreview">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="#10b981" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span id="uploadedFileName"></span>
                        <button type="button" class="remove-file-btn" id="removeFile">✕</button>
                    </div>
                </div>
            </div>

            <div class="step2-footer">
                <button type="submit" class="submit-btn">Submit Payment</button>
                <p class="submit-note">
                    🔒 Your submission is secure. Processing may take 1–2 business days.
                </p>
            </div>
        </div>

    </form>

</div>

<script src="<?= base_url('assets/js/pages/admission-processing-fee.js') ?>?v=<?= time() ?>"></script>