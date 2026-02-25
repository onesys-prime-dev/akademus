<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">

<div class="auth-container">

    <div class="auth-left">
        <a href="<?= base_url('/') ?>" class="brand">
            <img src="<?= base_url('assets/images/logo_akademus.png') ?>">
            <h2>AKADEMUS</h2>
        </a>

        <h1>Your Future <br><span>Starts Here</span></h1>

        <p>Complete your admission entirely online — from document submission to enrollment.</p>

        <ul>
            <li>Create your applicant account</li>
            <li>Submit required documents</li>
            <li>Track your application status</li>
        </ul>
    </div>

    <div class="auth-right">

        <h2>Create account</h2>
        <p class="subtitle">Start your admission journey today.</p>

        <div class="toggle">
            <a href="<?= base_url('sign_in') ?>">Sign In</a>
            <a href="<?= base_url('sign_up') ?>" class="active">Sign Up</a>
        </div>

        <?php if(isset($validation)): ?>
            <div class="alert">
                <?= $validation->listErrors(); ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('sign_up/register') ?>" method="post">

            <div class="row">
                <input type="text" name="firstname" placeholder="First Name" required>
                <input type="text" name="lastname" placeholder="Last Name" required>
            </div>

            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="mobile" placeholder="Mobile" required>

            <select name="program" required>
                <option value="">Select a Program</option>
                <option>Computer Engineering</option>
                <option>Computer Science</option>
                <option>Information System</option>
                <option>Information Technology</option>
            </select>

            <button type="submit">Create Account</button>

        </form>
    </div>
    <script>
    document.querySelectorAll('.toggle a').forEach(btn => {
        btn.addEventListener('click', function(e){
            document.querySelectorAll('.toggle a').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
</div>