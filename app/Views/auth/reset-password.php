<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akademus | Reset Password</title>
<?= theme_favicon_tags() ?>

<link href="https://fonts.googleapis.com/css2?family=<?= urlencode($theme['font_family'] ?? 'Poppins') ?>:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary:      <?= esc($theme['primary_color'] ?? '#0040a6') ?>;
        --primary-dark: <?= esc($theme['primary_dark']  ?? '#002d7a') ?>;
        --font:         '<?= esc($theme['font_family']  ?? 'Poppins') ?>';
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: var(--font), sans-serif;
        background: #f0f4f8;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .card {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        overflow: hidden;
    }

    .card-header {
        background: var(--primary);
        padding: 28px 28px 22px;
    }

    .card-header .icon {
        width: 46px; height: 46px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 14px;
    }

    .card-header h1 { font-size: 19px; font-weight: 700; color: #fff; }
    .card-header p  { font-size: 13px; color: rgba(255,255,255,0.72); margin-top: 4px; }

    .card-body { padding: 26px 28px; }

    .alert {
        padding: 11px 14px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 18px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .alert-error   { background: #fff5f5; border: 1.5px solid #fed7d7; color: #c53030; }
    .alert-success { background: #f0fff4; border: 1.5px solid #c6f6d5; color: #276749; }
    .alert-warning { background: #fffbeb; border: 1.5px solid #fde68a; color: #92400e; }

    label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 6px;
    }

    .input-wrap { position: relative; margin-bottom: 16px; }

    .input-wrap input {
        width: 100%;
        padding: 10px 40px 10px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font), sans-serif;
        outline: none;
        transition: border-color 0.2s;
    }
    .input-wrap input:focus { border-color: var(--primary); }

    .toggle-pw {
        position: absolute; right: 10px; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer; color: #a0aec0; padding: 0;
    }

    .btn-primary {
        width: 100%; padding: 11px;
        background: var(--primary); color: #fff;
        border: none; border-radius: 8px;
        font-size: 14px; font-weight: 600;
        cursor: pointer; font-family: var(--font), sans-serif;
        transition: opacity 0.2s;
        margin-top: 4px;
    }
    .btn-primary:hover    { opacity: 0.9; }
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 16px;
        font-size: 13px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    .back-link:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1>Reset Password</h1>
        <p><?= $valid ? 'Enter your new password below.' : 'This link is no longer valid.' ?></p>
    </div>

    <div class="card-body">

        <?php if (!$valid): ?>
            <div class="alert alert-warning">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>This password reset link has <strong>expired or already been used</strong>. Please request a new one.</span>
            </div>
            <a href="<?= base_url('/sign-in') ?>" class="btn-primary" style="display:block;text-align:center;text-decoration:none;line-height:1.6;">
                Back to Sign In
            </a>

        <?php else: ?>
            <div id="rpError" style="
                display:none;padding:11px 14px;border-radius:8px;font-size:13px;
                margin-bottom:18px;background:#fff5f5;border:1.5px solid #fed7d7;color:#c53030;
            "></div>
            <div id="rpSuccess" style="
                display:none;padding:12px 14px;border-radius:8px;font-size:13px;
                margin-bottom:18px;background:#f0fff4;border:1.5px solid #c6f6d5;color:#276749;
                align-items:flex-start;gap:8px;
            ">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#276749" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;"><path d="M20 6L9 17l-5-5"/></svg>
                <span>Password updated! Redirecting to sign in...</span>
            </div>

            <div id="rpForm">
                <label>NEW PASSWORD</label>
                <div class="input-wrap">
                    <input type="password" id="rpNewPw" placeholder="At least 8 characters"
                        onkeydown="if(event.key==='Enter') document.getElementById('rpConfirmPw').focus()">
                    <button type="button" class="toggle-pw" onclick="togglePw('rpNewPw')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>

                <label>CONFIRM PASSWORD</label>
                <div class="input-wrap">
                    <input type="password" id="rpConfirmPw" placeholder="Re-enter your password"
                        onkeydown="if(event.key==='Enter') rpSubmit()">
                    <button type="button" class="toggle-pw" onclick="togglePw('rpConfirmPw')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>

                <button class="btn-primary" id="rpBtn" onclick="rpSubmit()">Reset Password</button>
            </div>

            <a href="<?= base_url('/sign-in') ?>" class="back-link">Back to Sign In</a>
        <?php endif; ?>

    </div>
</div>

<script>
const RP_TOKEN = '<?= esc($token) ?>';
const RP_URL   = '<?= base_url('/reset-password') ?>';
const SIGNIN_URL = '<?= base_url('/sign-in') ?>';

function togglePw(id) {
    const el = document.getElementById(id);
    el.type  = el.type === 'password' ? 'text' : 'password';
}

async function rpSubmit() {
    const btn    = document.getElementById('rpBtn');
    const errEl  = document.getElementById('rpError');
    const newPw  = document.getElementById('rpNewPw').value;
    const confPw = document.getElementById('rpConfirmPw').value;

    errEl.style.display = 'none';
    btn.disabled        = true;
    btn.textContent     = 'Updating...';

    const form = new FormData();
    form.append('token',            RP_TOKEN);
    form.append('new_password',     newPw);
    form.append('confirm_password', confPw);

    try {
        const res  = await fetch(RP_URL, { method: 'POST', body: form });
        const data = await res.json();

        if (data.success) {
            document.getElementById('rpForm').style.display    = 'none';
            document.getElementById('rpSuccess').style.display = 'flex';
            setTimeout(() => window.location.href = SIGNIN_URL, 2500);
        } else {
            errEl.textContent   = data.message;
            errEl.style.display = 'block';
            btn.disabled        = false;
            btn.textContent     = 'Reset Password';
        }
    } catch (e) {
        errEl.textContent   = 'Something went wrong. Please try again.';
        errEl.style.display = 'block';
        btn.disabled        = false;
        btn.textContent     = 'Reset Password';
    }
}
</script>
</body>
</html>
