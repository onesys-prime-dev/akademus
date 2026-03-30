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
        <h1 class="payment-amount">₱1500.00</h1>
        <span class="payment-note">One-time · Non-refundable reservation fee</span>

        <div class="payment-methods">
            <button class="method-btn active" data-method="GCASH">GCash</button>
            <button class="method-btn"        data-method="MAYA">Maya</button>
            <button class="method-btn"        data-method="BDO">BDO</button>
            <button class="method-btn"        data-method="BPI">BPI</button>
        </div>
    </div>

    <!-- PAYMENT INSTRUCTIONS -->
    <div class="payment-instructions">
        <h3>How to Pay:</h3>

        <ol class="instruction-list">
            <li>Choose your preferred payment method.</li>
            <li>Send the exact amount of ₱200.00 to the account details shown above.</li>
            <li>Use the reference code <strong><?= $referenceCode ?></strong> as your payment reference or note.</li>
            <li>After sending your payment, click the "I Have Paid" button below to submit your payment details for verification.</li>
        </ol>

        <a href="<?= base_url('applicant-form/step/1') ?>" class="paid-btn">I Have Paid</a>
    </div>