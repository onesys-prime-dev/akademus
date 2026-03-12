<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akademus | <?= $title ?></title>

<link href="https://fonts.googleapis.com/css2?family=<?= urlencode($theme['font_family'] ?? 'Poppins') ?>:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary:       <?= esc($theme['primary_color']    ?? '#0040a6') ?>;
        --primary-dark:  <?= esc($theme['primary_dark']     ?? '#002d7a') ?>;
        --primary-light: <?= esc($theme['primary_light']    ?? '#1a55c0') ?>;
        --primary-soft:  <?= esc($theme['primary_soft']     ?? '#e8f0ff') ?>;
        --sidebar:       <?= esc($theme['sidebar_color']    ?? '#002d7a') ?>;
        --accent:        <?= esc($theme['accent_color']     ?? '#ffb200') ?>;
        --accent-text:   <?= esc($theme['accent_text']      ?? '#002d7a') ?>;
        --radius:        <?= esc($theme['border_radius']    ?? '8px') ?>;
        --font:          '<?= esc($theme['font_family']     ?? 'Sora') ?>';
    }
</style>

<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') . '?v=' . time(); ?>">
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
</head>
<body>

    <!-- NAVBAR -->
    <div class="header">
        <a href="<?= base_url('/') ?>" class="brand">
            <img src="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png') ?>">
            <span class="brand-name"><?= esc($theme['app_name'] ?? 'AKADEMUS') ?></span>
        </a>

        <div class="nav-links">
            <!--<a href="<?= base_url('/') ?>"><p>Home</p></a>-->
            <a href="<?= base_url('about-us') ?>"><p>About Us</p></a>
            <a href="<?= base_url('features') ?>"><p>Features</p></a>
            <div class="nav-dropdown">
                <p class="nav-dropdown-trigger">Services ▾</p>
                <div class="nav-dropdown-menu">
                    <a href="#">Learnix</a>
                    <a href="#">Mentora</a>
                    <a href="#">SIS</a>
                </div>
            </div>
            <a href="<?= base_url('contact') ?>"><p>Contact</p></a>
        </div>

        <div class="nav-right">
            <!--<a href="<?= base_url('sign_in') ?>" class="get-started-btn">Get Started</a>-->
            <button class="hamburger" id="hamburger" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobile-menu">
        <!--<a href="<?= base_url('/') ?>">Home</a>-->
        <a href="<?= base_url('about-us') ?>">About Us</a>
        <a href="<?= base_url('features') ?>">Features</a>
        <div class="mobile-dropdown">
            <div class="mobile-dropdown-trigger" id="mobile-services-trigger">Services ▾</div>
            <div class="mobile-dropdown-items" id="mobile-services-items">
                <a href="#">Learnix</a>
                <a href="#">Mentora</a>
                <a href="#">SIS</a>
            </div>
        </div>
        <a href="<?= base_url('contact') ?>">Contact</a>
        <a href="<?= base_url('sign_in') ?>" class="mobile-get-started">Get Started</a>
    </div>

    <!-- OVERLAY -->
    <div class="mobile-overlay" id="mobile-overlay"></div>

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
    // AUTH TOGGLE
    const toggleSignIn  = document.getElementById('toggle-sign-in');
    const toggleSignUp  = document.getElementById('toggle-sign-up');
    const formContainer = document.getElementById('auth-form-container');
    const headerTitle   = document.querySelector('#auth-header h2');
    const headerSubtitle = document.querySelector('#auth-header span');

    function loadAuth(url, activeBtn) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.title           = "Akademus | " + data.title;
            headerTitle.textContent  = data.header;
            headerSubtitle.textContent = data.subheader;
            formContainer.innerHTML  = data.form;

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

    // TURNSTILE MODAL
    let widgetId = null;

    document.addEventListener('click', function(e) {

        // Open modal
        if (e.target && e.target.id === 'sign-up-button') {
            const form = document.getElementById('sign-up-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const modal = document.getElementById('turnstile-modal');
            modal.style.display = 'flex';

            if (widgetId !== null) {
                turnstile.reset(widgetId);
            } else {
                widgetId = turnstile.render('#turnstile-widget', {
                    sitekey: '1x00000000000000000000AA',
                    theme: 'light',
                    callback: function(token) {
                        modal.style.display = 'none';
                        const form = document.getElementById('sign-up-form');
                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.href = data.redirect;
                            } else {
                                const existing = document.querySelector('.alert-error');
                                if (existing) existing.remove();
                                const err = document.createElement('div');
                                err.className = 'alert alert-error';
                                err.textContent = data.message || 'Something went wrong.';
                                form.prepend(err);
                            }
                        });
                    },
                    'error-callback': function() {
                        turnstile.reset(widgetId);
                    }
                });
            }
        }

        // Cancel button
        if (e.target && e.target.id === 'turnstile-cancel') {
            document.getElementById('turnstile-modal').style.display = 'none';
            if (widgetId !== null) turnstile.reset(widgetId);
        }

        // Backdrop click
        if (e.target && e.target.id === 'turnstile-modal') {
            e.target.style.display = 'none';
            if (widgetId !== null) turnstile.reset(widgetId);
        }
    });

    // HAMBURGER MENU
    const hamburger      = document.getElementById('hamburger');
    const mobileMenu     = document.getElementById('mobile-menu');
    const overlay        = document.getElementById('mobile-overlay');
    const servicesTrigger = document.getElementById('mobile-services-trigger');
    const servicesItems  = document.getElementById('mobile-services-items');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileMenu.classList.toggle('open');
        overlay.classList.toggle('open');
    });

    overlay.addEventListener('click', () => {
        hamburger.classList.remove('open');
        mobileMenu.classList.remove('open');
        overlay.classList.remove('open');
    });

    servicesTrigger.addEventListener('click', () => {
        servicesItems.classList.toggle('open');
        servicesTrigger.classList.toggle('open');
    });
</script>
</body>
</html>