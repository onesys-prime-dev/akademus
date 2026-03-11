<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akademus | <?= $title ?></title>

<link href="https://fonts.googleapis.com/css2?family=<?= urlencode($theme['font_family'] ?? 'Poppins') ?>:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --primary:      <?= $theme['primary_color'] ?? '#0040A6' ?>;
    --primary-dark: <?= $theme['primary_dark']  ?? '#003080' ?>;
    --primary-light:<?= $theme['primary_light'] ?? '#1a55c0' ?>;
    --accent:       <?= $theme['accent_color']  ?? '#FFB200' ?>;
    --accent-text:  <?= $theme['accent_text']   ?? '#002d7a' ?>;
    --radius:       <?= $theme['border_radius'] ?? '12px'    ?>;
    --font:         '<?= $theme['font_family']  ?? 'Poppins' ?>', sans-serif;
}
</style>

<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') . '?v=' . time(); ?>">
</head>
<body>

    <!-- NAVBAR -->
    <div class="header">
        <a href="<?= base_url('/') ?>" class="brand">
            <img src="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png') ?>">
            <span class="brand-name"><?= esc($theme['app_name'] ?? 'AKADEMUS') ?></span>
        </a>

        <div class="nav-links">
            <!---<a href="<?= base_url('/') ?>"><p>Home</p></a>--->
            <a href="<?= base_url('about-us') ?>"><p>About Us</p></a>
            <a href="<?= base_url('features') ?>"><p>Features</p></a>

            <div class="nav-dropdown">
                <p class="nav-dropdown-trigger">Services ▾</p>
                <div class="nav-dropdown-menu">
                    <a href="#">Mentora</a>
                    <a href="#">Learnix</a>
                    <a href="#">SIS</a>
                </div>
            </div>

            <a href="<?= base_url('contact') ?>"><p>Contact</p></a>
        </div>

        <!--<a href="<?= base_url('sign_in') ?>" class="get-started-btn">Get Started</a> -->
    </div>

    <!-- MAIN CARD -->
    <div class="main-container">
        <div class="main-card">

            <!-- LEFT PANEL -->
            <div class="left">
                <div class="left-content-container">
                    <a href="<?= base_url('/') ?>" class="brand">
                        <img src="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png') ?>">
                        <div class="header-wrapper">
                            <p><?= esc($theme['app_name'] ?? 'AKADEMUS') ?></p>
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

            <!-- RIGHT PANEL -->
            <div class="right">
                <div id="auth-header" class="auth-header-container">
                    <h2><?= $auth_header ?></h2>
                    <span><?= $auth_sub_header ?></span>
                </div>

                <div class="switch-auth-toggle">
                    <div id="toggle-sign-in" class="toggle-btn">Sign In</div>
                    <div id="toggle-sign-up" class="toggle-btn">Sign Up</div>
                </div>

                <div id="auth-form-container">
                    <?php if (isset($auth_field)) echo view($auth_field, ['programs' => $programs ?? []]); ?>
                    <?php if (isset($success)) echo view($success); ?>
                </div>
            </div>

        </div>
    </div>

<script>
    const toggleSignIn = document.getElementById('toggle-sign-in');
    const toggleSignUp = document.getElementById('toggle-sign-up');
    const formContainer = document.getElementById('auth-form-container');
    const headerTitle = document.querySelector('#auth-header h2');
    const headerSubtitle = document.querySelector('#auth-header span');

    function loadAuth(url, activeBtn) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.title = "Akademus | " + data.title;
            headerTitle.textContent = data.header;
            headerSubtitle.textContent = data.subheader;
            formContainer.innerHTML = data.form;

            if (data.programs) {
                const select = document.getElementById('program');
                if (select) {
                    select.innerHTML = '<option value="" disabled selected hidden>Select a Program</option>';
                    data.programs.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        select.appendChild(opt);
                    });
                }
            }

            toggleSignIn.classList.remove('active');
            toggleSignUp.classList.remove('active');
            activeBtn.classList.add('active');
            history.pushState(null, '', url);
        });
    }

    toggleSignIn.addEventListener('click', () => loadAuth('<?= base_url('sign_in') ?>', toggleSignIn));
    toggleSignUp.addEventListener('click', () => loadAuth('<?= base_url('sign_up') ?>', toggleSignUp));

    window.addEventListener('popstate', () => {
        const path = window.location.pathname;
        if (path.includes('sign_up')) loadAuth('<?= base_url('sign_up') ?>', toggleSignUp);
        else loadAuth('<?= base_url('sign_in') ?>', toggleSignIn);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const path = window.location.pathname;
        if (path.includes('sign_up')) loadAuth('<?= base_url('sign_up') ?>', toggleSignUp);
        else if (path.includes('sign_in')) loadAuth('<?= base_url('sign_in') ?>', toggleSignIn);
    });

    if (window.location.pathname.includes('success')) {
        document.querySelector('.switch-auth-toggle').style.display = 'none';
    }
</script>
</body>
</html>