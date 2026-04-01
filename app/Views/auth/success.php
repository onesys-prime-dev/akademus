<!-- auth/success.php -->
<div class="success-container">
    <div class="applicant-id-box">
        <span>Applicant ID</span>
        <p>#<?= $applicant_number ?></p>
    </div>

    <div class="success-info">
        <span>A welcome email with your ID has been sent to your registered email address. Keep this for your records.</span>
    </div>

    <a href="<?= base_url('/sign-in') ?>" class="go-back-link-btn">
        GO BACK TO LOGIN
    </a>
</div>