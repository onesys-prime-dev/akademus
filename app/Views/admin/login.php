<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($theme['app_name'] ?? 'Akademus') ?> Admin</title>
    <?= theme_favicon_tags() ?>
    <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($theme['font_family'] ?? 'Sora') ?>:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      <?= esc($theme['primary_color'] ?? '#0040a6') ?>;
            --primary-dark: <?= esc($theme['primary_dark']  ?? '#002d7a') ?>;
            --accent:       <?= esc($theme['accent_color']  ?? '#ffb200') ?>;
            --accent-text:  <?= esc($theme['accent_text']   ?? '#002d7a') ?>;
            --radius:       <?= esc($theme['border_radius'] ?? '8px') ?>;
            --font:         '<?= esc($theme['font_family']  ?? 'Sora') ?>';
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font), sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: linear-gradient(-20deg, var(--primary), var(--primary-dark), var(--primary), var(--primary-dark));
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
        }

        @keyframes gradientBG {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ── CARD ── */
        .card {
            display: flex;
            width: 100%;
            max-width: 820px;
            min-height: 480px;
            background: linear-gradient(180deg, rgba(255,255,255,0.62), rgba(138,140,202,0.55));
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,0.3);
        }

        /* ── LEFT PANEL ── */
        .left {
            width: 50%;
            padding: 44px 48px;
            display: flex;
            flex-direction: column;
            gap: 40px;
            border-right: 1px solid rgba(255,255,255,0.4);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            color: var(--accent);
            overflow: hidden;
            flex-shrink: 0;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .brand-text { display: flex; flex-direction: column; }
        .brand-name { font-size: 16px; font-weight: 800; color: var(--primary); line-height: 1.2; max-width: 160px; word-break: break-word; }
        .brand-sub  { font-size: 13px; color: #555; opacity: 0.8; }

        .tagline { display: flex; flex-direction: column; gap: 32px; color: #fff; }
        .tagline-heading { display: flex; flex-direction: column; gap: 4px; }
        .tagline-heading p { font-size: 40px; font-weight: 900; line-height: 1.1; }
        .tagline-heading span { font-size: 40px; font-weight: 900; color: var(--accent); }
        .tagline-sub { font-size: 14px; font-weight: 600; color: rgba(255,255,255,0.85); line-height: 1.6; }

        .features { display: flex; flex-direction: column; gap: 14px; }
        .feature { display: flex; align-items: center; gap: 12px; }
        .feature-dot {
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1.5px solid var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .feature span { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.9); }

        /* ── RIGHT PANEL ── */
        .right {
            width: 50%;
            padding: 44px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 24px;
        }

        .right-header h2 {
            font-size: 30px;
            font-weight: 800;
            color: var(--primary);
            line-height: 1.1;
        }
        .right-header p {
            font-size: 14px;
            color: #3d3d3d;
            opacity: 0.7;
            font-weight: 600;
            margin-top: 4px;
        }

        .form-group { display: flex; flex-direction: column; gap: 14px; }

        .field { display: flex; flex-direction: column; gap: 4px; }

        label {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            margin-left: 2px;
        }

        input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1.5px solid rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.6);
            border-radius: var(--radius);
            font-family: var(--font), sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #0d1e38;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        input::placeholder { color: #797777; opacity: 0.7; font-weight: 500; }

        input:focus {
            border-color: var(--primary);
            background: rgba(255,255,255,0.85);
        }

        .password-field { position: relative; }
        .password-field input { padding-right: 42px; }
        .password-toggle {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            cursor: pointer;
            color: #5a5a5a;
            padding: 0;
        }
        .password-toggle:hover { color: #0d1e38; }
        .password-toggle svg { width: 18px; height: 18px; }
        .password-toggle .icon-hide { display: none; }
        .password-toggle.showing .icon-eye  { display: none; }
        .password-toggle.showing .icon-hide { display: block; }

        .submit-btn {
            width: 100%;
            height: 46px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: var(--font), sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, opacity 0.2s;
            margin-top: 20px;
        }
        .submit-btn:hover:not(:disabled) { background: var(--primary-dark); }
        .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

        /* ── ERROR MODAL ── */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 9999;
            align-items: center; justify-content: center;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(3px);
        }
        .modal-overlay.open { display: flex; }

        .modal-card {
            background: #fff;
            border-radius: 14px;
            padding: 36px 28px;
            max-width: 340px;
            width: 100%;
            margin: 16px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            animation: modalPop 0.2s ease;
        }

        @keyframes modalPop {
            from { transform: scale(0.9); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }

        .modal-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: #fee2e2; color: #dc2626;
            font-size: 22px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .modal-title { font-size: 17px; font-weight: 700; color: #111; margin-bottom: 8px; }
        .modal-message { font-size: 13px; color: #555; line-height: 1.55; margin-bottom: 22px; }
        .modal-btn {
            padding: 10px 28px;
            background: var(--primary); color: #fff;
            border: none; border-radius: var(--radius);
            font-family: var(--font), sans-serif;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: background 0.2s;
        }
        .modal-btn:hover { background: var(--primary-dark); }

        /* ── CREDENTIALS HINT ── */
        .creds-hint {
            background: rgba(255,255,255,0.45);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius);
            padding: 12px 14px;
            font-size: 12px;
        }
        .creds-hint-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.6px; color: rgba(30,50,80,0.6); margin-bottom: 8px;
        }
        .creds-hint-row {
            display: flex; justify-content: space-between; align-items: center;
            gap: 8px; margin-bottom: 4px;
        }
        .creds-hint-row:last-child { margin-bottom: 0; }
        .creds-hint-row span { font-size: 12px; font-weight: 600; color: rgba(30,50,80,0.7); }
        .creds-hint-row code {
            font-family: monospace; font-size: 12px; font-weight: 700;
            color: #0d1e38; background: rgba(255,255,255,0.7);
            padding: 2px 8px; border-radius: 4px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 720px) {
            .card { flex-direction: column; max-width: 440px; }
            .left {
                width: 100%; padding: 28px 28px 24px;
                border-right: none; border-bottom: 1px solid rgba(255,255,255,0.35);
                gap: 20px;
            }
            .tagline-heading p, .tagline-heading span { font-size: 28px; }
            .right { width: 100%; padding: 28px 28px 32px; }
        }

        @media (max-width: 480px) {
            body { padding: 16px 12px; }
            .tagline { gap: 16px; }
            .tagline-heading p, .tagline-heading span { font-size: 24px; }
            .features { display: none; }
        }
    </style>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<script>
(function(){var _f=window.fetch;window.fetch=function(u,o){o=o||{};if(o.method&&o.method.toUpperCase()!=='GET'){var t=document.querySelector('meta[name="csrf-token"]');if(t){o.headers=o.headers||{};if(o.headers instanceof Headers){o.headers.set('X-CSRF-TOKEN',t.content);}else{o.headers['X-CSRF-TOKEN']=t.content;}}}return _f.call(this,u,o);};})();
</script>
</head>
<body>

    <!-- Error Modal -->
    <div class="modal-overlay" id="error-modal">
        <div class="modal-card">
            <div class="modal-icon">&#10006;</div>
            <div class="modal-title">Sign In Failed</div>
            <p class="modal-message" id="error-message">Invalid credentials. Please try again.</p>
            <button class="modal-btn" id="error-close">Try Again</button>
        </div>
    </div>

    <div class="card">

        <!-- LEFT -->
        <div class="left">
            <a href="<?= base_url('/') ?>" class="brand">
                <div class="brand-logo" id="brand-logo-wrap">
                    <?php if (!empty($theme['logo_url'])): ?>
                        <img
                            src="<?= base_url(esc($theme['logo_url'])) ?>"
                            alt="<?= esc($theme['app_name'] ?? 'AK') ?>"
                            onerror="this.style.display='none'; document.getElementById('brand-logo-initials').style.display='flex';"
                        >
                        <span id="brand-logo-initials" style="display:none;"><?= strtoupper(substr($theme['app_name'] ?? 'AK', 0, 2)) ?></span>
                    <?php else: ?>
                        <?= strtoupper(substr($theme['app_name'] ?? 'AK', 0, 2)) ?>
                    <?php endif; ?>
                </div>
                <div class="brand-text">
                    <span class="brand-name"><?= esc($theme['app_name'] ?? 'AKADEMUS') ?></span>
                    <span class="brand-sub">Admin Panel</span>
                </div>
            </a>

            <div class="tagline">
                <div class="tagline-heading">
                    <p>Admin</p>
                    <p>Control <span>Panel</span></p>
                </div>
                <span class="tagline-sub">Manage your admission system — applicants, forms, and settings all in one place.</span>
                <div class="features">
                    <div class="feature">
                        <div class="feature-dot">1</div>
                        <span>Manage applicants & applications</span>
                    </div>
                    <div class="feature">
                        <div class="feature-dot">2</div>
                        <span>Customize forms and requirements</span>
                    </div>
                    <div class="feature">
                        <div class="feature-dot">3</div>
                        <span>Configure theme & system settings</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="right">
            <div class="right-header">
                <h2>Admin Sign In</h2>
                <p>Enter your credentials to continue</p>
            </div>

            <form id="admin-login-form" action="<?= base_url('admin/login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <div class="field">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="admin@akademus.edu.ph" required>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <div class="password-field">
                            <input type="password" name="password" id="admin-password" placeholder="••••••••" required>
                            <button type="button" class="password-toggle" id="password-toggle" aria-label="Show password">
                                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a17.62 17.62 0 0 1-2.5 3.51M6.5 6.5A17.9 17.9 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.5-1.5M1 1l22 22"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="submit-btn" id="login-btn">Sign In to Admin</button>
            </form>

            <div class="creds-hint">
                <div class="creds-hint-label">Default Credentials</div>
                <div class="creds-hint-row"><span>Email</span><code>admin@akademus.edu.ph</code></div>
                <div class="creds-hint-row"><span>Password</span><code>Admin@123</code></div>
            </div>
        </div>

    </div>

<script>
    const form     = document.getElementById('admin-login-form');
    const btn      = document.getElementById('login-btn');
    const modal    = document.getElementById('error-modal');
    const errorMsg = document.getElementById('error-message');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        btn.disabled    = true;
        btn.textContent = 'Signing in…';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                errorMsg.textContent = data.message || 'Invalid credentials. Please try again.';
                modal.classList.add('open');
                btn.disabled    = false;
                btn.textContent = 'Sign In to Admin';
            }
        })
        .catch(() => {
            errorMsg.textContent = 'Something went wrong. Please try again.';
            modal.classList.add('open');
            btn.disabled    = false;
            btn.textContent = 'Sign In to Admin';
        });
    });

    document.getElementById('error-close').addEventListener('click', () => modal.classList.remove('open'));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });

    const pwInput  = document.getElementById('admin-password');
    const pwToggle = document.getElementById('password-toggle');
    pwToggle.addEventListener('click', () => {
        const showing = pwInput.type === 'text';
        pwInput.type = showing ? 'password' : 'text';
        pwToggle.classList.toggle('showing', !showing);
        pwToggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
</script>
</body>
</html>
