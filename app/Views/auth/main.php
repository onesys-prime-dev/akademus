<!-- auth/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>

    <!-- Base CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/base/global.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/base/reset.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/base/typography.css') . '?v=' . time(); ?>">

    <!-- Tokens CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/colors.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/borders.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/shadows.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/spacing.css') . '?v=' . time(); ?>">

    <!-- Layouts CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/auth-layout.css') . '?v=' . time(); ?>">

    <!-- Components CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/components/buttons.css') . '?v=' . time(); ?>">

    <!-- Pages CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css') . '?v=' . time(); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="header">
            <a href="<?= base_url('/') ?>" class="brand">
                <img src="<?= base_url('assets/images/logo_akademus.png') ?>">
            </a>
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
                            <span class="tagline-paragraph">Complete your admission entirely online — from document submission to enrollment.</span>

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
    </div>

    <script>
        window.authConfig = <?= json_encode([
            "baseUrl" => base_url(),
            "signInUrl" => base_url('sign-in'),
            "signUpUrl" => base_url('sign-up')
        ]) ?>;
    </script>

    <script src="<?= base_url('assets/js/pages/auth.js') . '?v=' . time(); ?>"></script>
</body>
</html>