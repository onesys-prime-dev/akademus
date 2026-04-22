<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akademus | <?= $title ?></title>
<?php $favicon = !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/favicons/favicon-32x32.png'); ?>
<link rel="icon" type="image/png" sizes="32x32" href="<?= $favicon ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/favicons/favicon-16x16.png') ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/favicons/favicon-180x180.png') ?>">

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

<link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css') . '?v=' . time(); ?>">
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
            <a href="<?= base_url('about.php') ?>"><p>About Us</p></a>
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
            <!--<a href="<?= base_url('sign-in') ?>" class="get-started-btn">Get Started</a>-->
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
        <a href="<?= base_url('sign-in') ?>" class="mobile-get-started">Get Started</a>
    </div>

    <!-- OVERLAY -->
    <div class="mobile-overlay" id="mobile-overlay"></div>

    <!-- FORGOT PASSWORD MODAL -->
    <div id="forgotModal" style="
        display:none;position:fixed;inset:0;z-index:99999;
        align-items:center;justify-content:center;
        background:rgba(0,0,0,0.5);backdrop-filter:blur(3px);
    ">
        <div style="
            background:#fff;border-radius:16px;width:100%;max-width:400px;
            margin:16px;box-shadow:0 24px 60px rgba(0,0,0,0.2);overflow:hidden;
        ">
            <div style="background:var(--primary);padding:22px 24px 18px;">
                <h2 style="margin:0;font-size:17px;font-weight:700;color:#fff;">Forgot Password</h2>
                <p style="margin:4px 0 0;font-size:12.5px;color:rgba(255,255,255,0.75);">
                    Enter your email and we'll send you a reset link.
                </p>
            </div>
            <div style="padding:22px 24px;">
                <div id="fpError" style="
                    display:none;margin-bottom:14px;padding:10px 14px;
                    background:#fff5f5;border:1.5px solid #fed7d7;
                    border-radius:8px;font-size:13px;color:#c53030;
                "></div>
                <div id="fpSuccess" style="
                    display:none;margin-bottom:14px;padding:12px 14px;
                    background:#f0fff4;border:1.5px solid #c6f6d5;
                    border-radius:8px;font-size:13px;color:#276749;
                    align-items:flex-start;gap:8px;
                ">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#276749" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;"><path d="M20 6L9 17l-5-5"/></svg>
                    <span id="fpSuccessMsg"></span>
                </div>
                <div id="fpForm">
                    <label style="display:block;font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">
                        EMAIL ADDRESS
                    </label>
                    <input type="email" id="fpEmail" placeholder="yourname@email.com"
                        onkeydown="if(event.key==='Enter') fpSubmit()"
                        style="
                            width:100%;padding:10px 12px;box-sizing:border-box;
                            border:1.5px solid #e2e8f0;border-radius:8px;
                            font-size:13.5px;outline:none;
                            transition:border-color 0.2s;margin-bottom:18px;
                        "
                        onfocus="this.style.borderColor='var(--primary)'"
                        onblur="this.style.borderColor='#e2e8f0'">
                    <button onclick="fpSubmit()" id="fpBtn" style="
                        width:100%;padding:11px;background:var(--primary);color:#fff;
                        border:none;border-radius:8px;font-size:14px;font-weight:600;
                        cursor:pointer;transition:opacity 0.2s;margin-bottom:10px;
                    ">Send Reset Link</button>
                    <button onclick="closeForgotModal()" style="
                        width:100%;padding:10px;background:#f7fafc;color:#4a5568;
                        border:1.5px solid #e2e8f0;border-radius:8px;
                        font-size:13.5px;font-weight:500;cursor:pointer;
                    ">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- LOGIN ERROR MODAL -->
    <div class="auth-modal-overlay" id="auth-error-modal" style="display:none;">
        <div class="auth-modal-card">
            <div class="auth-modal-icon">&#10006;</div>
            <h3 class="auth-modal-title">Sign In Failed</h3>
            <p class="auth-modal-message" id="auth-error-message">Invalid email or password.</p>
            <button class="auth-modal-btn" id="auth-error-close">Try Again</button>
        </div>
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
    // AUTH TOGGLE
    const toggleSignIn  = document.getElementById('toggle-sign-in');
    const toggleSignUp  = document.getElementById('toggle-sign-up');
    const formContainer = document.getElementById('auth-form-container');
    const headerTitle   = document.querySelector('#auth-header h2');
    const headerSubtitle = document.querySelector('#auth-header span');

    // ── Searchable program dropdown ──────────────────────────────────────────
    function initProgramSearch() {
        const wrap    = document.getElementById('prog-wrap');
        const trigger = document.getElementById('prog-trigger');
        const display = document.getElementById('prog-display');
        const arrow   = document.getElementById('prog-arrow');
        const list    = document.getElementById('prog-list');
        const search  = document.getElementById('prog-search');
        const opts    = document.getElementById('prog-options');
        const noRes   = document.getElementById('prog-no-results');
        const value   = document.getElementById('program-value');
        if (!wrap || !trigger) return;

        function openList() {
            list.style.display = 'block';
            if (arrow) arrow.style.transform = 'rotate(180deg)';
            search.value = '';
            filterOpts('');
            setTimeout(() => search.focus(), 50);
        }
        function closeList() {
            list.style.display = 'none';
            if (arrow) arrow.style.transform = '';
        }
        function filterOpts(q) {
            let visible = 0;
            opts.querySelectorAll('.prog-opt').forEach(o => {
                const match = o.dataset.name.toLowerCase().includes(q.toLowerCase());
                o.style.display = match ? 'block' : 'none';
                if (match) visible++;
            });
            noRes.style.display = (visible === 0) ? 'block' : 'none';
        }

        trigger.addEventListener('click', e => {
            e.stopPropagation();
            list.style.display === 'none' ? openList() : closeList();
        });
        search.addEventListener('click', e => e.stopPropagation());
        search.addEventListener('input', function() { filterOpts(this.value); });

        opts.addEventListener('click', function(e) {
            const opt = e.target.closest('.prog-opt');
            if (!opt) return;
            value.value           = opt.dataset.value;
            display.textContent   = opt.dataset.name;
            display.style.color   = '#000000';
            display.style.fontWeight = '600';
            display.style.fontSize = '13px';
            const err = document.getElementById('prog-error');
            if (err) err.style.display = 'none';
            trigger.style.border  = '';
            closeList();
        });

        // Hover styles via JS (avoids needing a stylesheet)
        opts.addEventListener('mouseover', e => {
            const opt = e.target.closest('.prog-opt');
            if (opt) opt.style.background = '#f0f4ff';
        });
        opts.addEventListener('mouseout', e => {
            const opt = e.target.closest('.prog-opt');
            if (opt) opt.style.background = '';
        });

        document.addEventListener('click', e => {
            if (!wrap.contains(e.target)) closeList();
        });
    }
    // ─────────────────────────────────────────────────────────────────────────

    function loadAuth(url, activeBtn) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.title             = "Akademus | " + data.title;
            headerTitle.textContent    = data.header;
            headerSubtitle.textContent = data.subheader;
            formContainer.innerHTML    = data.form;

            // Repopulate custom searchable dropdown with fresh programs from server
            if (data.programs) {
                const optsEl = document.getElementById('prog-options');
                const noRes  = document.getElementById('prog-no-results');
                if (optsEl) {
                    optsEl.querySelectorAll('.prog-opt').forEach(o => o.remove());
                    data.programs.forEach(p => {
                        const div = document.createElement('div');
                        div.className        = 'prog-opt';
                        div.dataset.value    = p.id;
                        div.dataset.name     = p.name;
                        div.style.cssText    = 'padding:11px 16px;cursor:pointer;font-size:14px;color:#1a202c;transition:background .12s;';
                        div.textContent      = p.name;
                        optsEl.insertBefore(div, noRes);
                    });
                }
            }

            initProgramSearch();

            toggleSignIn.classList.remove('active');
            toggleSignUp.classList.remove('active');
            activeBtn.classList.add('active');
            history.pushState(null, '', url);
        });
    }

    toggleSignIn.addEventListener('click', () => loadAuth('<?= base_url('sign-in') ?>', toggleSignIn));
    toggleSignUp.addEventListener('click', () => loadAuth('<?= base_url('sign-up') ?>', toggleSignUp));

    window.addEventListener('popstate', () => {
        const path = window.location.pathname;
        if (path.includes('sign-up')) loadAuth('<?= base_url('sign-up') ?>', toggleSignUp);
        else loadAuth('<?= base_url('sign-in') ?>', toggleSignIn);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const path = window.location.pathname;
        if (path.includes('sign-up')) loadAuth('<?= base_url('sign-up') ?>', toggleSignUp);
        else if (path.includes('sign-in')) loadAuth('<?= base_url('sign-in') ?>', toggleSignIn);
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
            // Validate custom program dropdown
            const progVal = document.getElementById('program-value');
            if (progVal && !progVal.value) {
                const trigger = document.getElementById('prog-trigger');
                const err     = document.getElementById('prog-error');
                if (trigger) trigger.style.border = '1.5px solid #f87171';
                if (err)     err.style.display    = 'block';
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
                                document.querySelector('.auth-modal-title').textContent    = 'Registration Failed';
                                document.getElementById('auth-error-message').textContent = data.message || 'Something went wrong.';
                                document.getElementById('auth-error-modal').style.display = 'flex';
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

    // SIGN-IN AJAX & ERROR MODAL
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.id !== 'sign-in-form') return;
        e.preventDefault();

        const btn = document.getElementById('sign-in-button');
        btn.disabled = true;
        btn.textContent = 'Signing in...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success' || data.redirect) {
                window.location.href = data.redirect || '<?= base_url('dashboard') ?>';
            } else {
                document.getElementById('auth-error-message').textContent = data.message || 'Invalid email or password.';
                document.getElementById('auth-error-modal').style.display = 'flex';
                btn.disabled = false;
                btn.textContent = 'Sign In';
            }
        })
        .catch(() => {
            document.getElementById('auth-error-message').textContent = 'Something went wrong. Please try again.';
            document.getElementById('auth-error-modal').style.display = 'flex';
            btn.disabled = false;
            btn.textContent = 'Sign In';
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'auth-error-close') {
            document.getElementById('auth-error-modal').style.display = 'none';
        }
        if (e.target && e.target.id === 'auth-error-modal') {
            e.target.style.display = 'none';
        }
    });

    // FORGOT PASSWORD MODAL
    function openForgotModal() {
        document.getElementById('forgotModal').style.display = 'flex';
        document.getElementById('fpEmail').focus();
    }

    function closeForgotModal() {
        document.getElementById('forgotModal').style.display = 'none';
        document.getElementById('fpError').style.display     = 'none';
        document.getElementById('fpSuccess').style.display   = 'none';
        document.getElementById('fpForm').style.display      = 'block';
        document.getElementById('fpEmail').value             = '';
        const btn = document.getElementById('fpBtn');
        btn.disabled    = false;
        btn.textContent = 'Send Reset Link';
    }

    document.getElementById('forgotModal').addEventListener('click', function (e) {
        if (e.target === this) closeForgotModal();
    });

    async function fpSubmit() {
        const btn   = document.getElementById('fpBtn');
        const errEl = document.getElementById('fpError');
        const email = document.getElementById('fpEmail').value.trim();

        errEl.style.display = 'none';

        if (!email) {
            errEl.textContent   = 'Please enter your email address.';
            errEl.style.display = 'block';
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Sending...';

        const form = new FormData();
        form.append('email', email);

        try {
            const res  = await fetch('<?= base_url('/forgot-password') ?>', { method: 'POST', body: form });
            const data = await res.json();

            if (data.success) {
                document.getElementById('fpForm').style.display    = 'none';
                document.getElementById('fpSuccessMsg').textContent = data.message;
                document.getElementById('fpSuccess').style.display  = 'flex';
            } else {
                errEl.textContent   = data.message;
                errEl.style.display = 'block';
                btn.disabled        = false;
                btn.textContent     = 'Send Reset Link';
            }
        } catch (e) {
            errEl.textContent   = 'Something went wrong. Please try again.';
            errEl.style.display = 'block';
            btn.disabled        = false;
            btn.textContent     = 'Send Reset Link';
        }
    }

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