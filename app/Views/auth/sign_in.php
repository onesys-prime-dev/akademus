<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">

<div class="auth-container">
    <div class="auth-left">
        <div class="brand">
            <img src="<?= base_url('assets/images/logo_akademus.png') ?>">
            <h2>AKADEMUS</h2>
        </div>

        <h1>Your Future <br><span>Starts Here</span></h1>

        <p>Complete your admission entirely online — from document submission to enrollment.</p>

        <ul>
            <li>Create your applicant account</li>
            <li>Submit required documents</li>
            <li>Track your application status</li>
        </ul>
    </div>
    <div class="auth-right">

        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to continue your application.</p>

        <div class="toggle">
            <a href="<?= base_url('sign_in') ?>" class="active">Sign In</a>
            <a href="<?= base_url('sign_up') ?>">Sign Up</a>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('sign_in/login') ?>" method="post">

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Sign In</button>

        </form>
    </div>

</div>