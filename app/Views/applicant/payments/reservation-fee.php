<?php

$applicantNumber = session()->get('applicant_number');
$referenceCode   = 'RESFEE-' . esc($applicantNumber);
?>

<div class="payment-page">

    <!-- HERO CARD -->
    <div class="payment-hero-card">
        <span class="payment-label">Reservation Fee</span>
        <h2 class="payment-title">Pay to Reserve Your Slot</h2>
        <p class="payment-subtext">
            A one-time reservation fee is required to reserve your slot in the application process. Non-refundable.
        </p>
        <h1 class="payment-amount">₱2000.00</h1>
        <span class="payment-note">One-time · Non-refundable reservation fee</span>

        <div class="payment-methods">
            <button class="method-btn active" data-method="GCASH">GCash</button>
            <button class="method-btn"        data-method="MAYA">Maya</button>
            <button class="method-btn"        data-method="BDO">BDO</button>
            <button class="method-btn"        data-method="BPI">BPI</button>
            <button class="method-btn"        data-method="PAYMAYA">PayMaya</button>
        </div>
    </div>

    <form action="<?= base_url('payment/reservation-fee/store') ?>" method="post" enctype="multipart/form-data">
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
                    <p>₱1500.00</p>
                </div>

                <div class="account-row last">
                    <span>Reference / Note</span>

</div>