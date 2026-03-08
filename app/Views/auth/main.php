<!-- auth/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css') . '?v=' . time(); ?>">
</head>
<body>
    <div class="header">
        <a href="<?= base_url('/') ?>" class="brand">
            <img src="<?= base_url('assets/images/logo_akademus.png') ?>">
        </a>

        <div class="nav-links">
            <a href="<?= base_url('admissions') ?>">
                <p>Admissions</p>
            </a>
            <a href="<?= base_url('academics') ?>">
                <p>Academics</p>
            </a>
            <a href="<?= base_url('about-us') ?>">
                <p>About Us</p>
            </a>
            <a href="<?= base_url('contact-us') ?>">
                <p>Contact Us</p>
            </a>
        </div>

        <div class="external-link-container">
            <a href="#">
                <img class="logo" src="<?= base_url('assets/images/logo_mentora.png') ?>" alt="">
                <div class="label-wrapper">
                    <span>Proceed to Mentora</span>
                    <img src="<?= base_url('assets/images/icons/arrow-right.svg') ?>" alt="">
                </div>
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="main-card">
            <div class="left">
                <div class="left-content-container">
                    <a href="<?= base_url('/') ?>" class="brand">
                        <img src="<?= base_url('assets/images/logo_akademus.png') ?>">
                        <div class="header-wrapper">
                            <p>AKADEMUS</p>
                            <span>Admission System</span>
                        </div>
                    </a>

                    <div class="left-tagline-container">
                        <div class="tagline">
                            <p>Your Future</p>
                            <p>Starts <span>Here</span></p>
                        </div>
                        <span>Complete your admission entirely online — from document submission to enrollment.</span>

                        <div class="steps-container">
                            <div class="step">
                                <div class="step-number">1</div>
                                <span>Create your applicant account</span>
                            </div>

                            <div class="step">
                                <div class="step-number">2</div>
                                <span>Submit required documents</span>
                            </div>

                            <div class="step">
                                <div class="step-number">3</div>
                                <span>Track your application status</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="right">
                <!-- container for sign-in and sign-up cards -->
                <div id="auth-header" class="auth-header-container">
                    <h2><?= $auth_header ?></h2>
                    <span><?= $auth_sub_header ?></span>
                </div>

                <div class="switch-auth-toggle">
                    <div id="toggle-sign-in" class="toggle-btn">
                        Sign In
                    </div>

                    <div id="toggle-sign-up" class="toggle-btn">
                        Sign Up
                    </div>
                </div>

                <div id="auth-form-container">
                    <!-- sign-in and sign-up forms will be loaded here via AJAX -->
                    <?php if (isset($auth_field)) {
                        echo view($auth_field);
                    } ?>

                    <?php if (isset($success)) {
                        echo view($success);
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.authConfig = <?= json_encode([
            "baseUrl" => base_url(),
            "signInUrl" => base_url('sign_in'),
            "signUpUrl" => base_url('sign_up')
        ]) ?>;
    </script>

    <script src="<?= base_url('assets/js/pages/auth.js') ?>"></script>
</body>
</html>