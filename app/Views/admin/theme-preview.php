<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview</title>

<!-- CSS variables — set first so they're available when CSS files parse -->
<style>
:root {
    --primary:       <?= esc($theme['primary_color']  ?? '#0040a6') ?>;
    --primary-dark:  <?= esc($theme['primary_dark']   ?? '#002d7a') ?>;
    --primary-light: <?= esc($theme['primary_light']  ?? '#1a55c0') ?>;
    --accent:        <?= esc($theme['accent_color']   ?? '#ffb200') ?>;
    --accent-text:   <?= esc($theme['accent_text']    ?? '#002d7a') ?>;
    --sidebar:       <?= esc($theme['sidebar_color']  ?? '#002d7a') ?>;
    --radius:        <?= esc($theme['border_radius']  ?? '8px') ?>;
    --font:          '<?= esc($theme['font_family']   ?? 'Sora') ?>', sans-serif;
    --card-gradient: linear-gradient(180deg, rgba(255,255,255,0.61), <?= esc($theme['primary_light'] ?? '#1a55c0') ?>);
}
</style>

<!-- All Google Fonts used by the portal -->
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&family=Raleway:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">

<?php
$authPages    = ['signin', 'signup', 'success'];
$portalPages  = ['dashboard', 'appform', 'documents', 'track'];
$isAuthPage   = in_array($page, $authPages);
$logoUrl      = !empty($theme['logo_url'])
                    ? base_url(esc($theme['logo_url']))
                    : base_url('assets/images/logo_akademus.png');
$appName      = esc($theme['app_name'] ?? 'AKADEMUS');
?>

<?php if ($isAuthPage): ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css') ?>">
<?php else: ?>
<link rel="stylesheet" href="<?= base_url('assets/css/base/reset.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/base/global.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/base/typography.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tokens/colors.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tokens/borders.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tokens/shadows.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tokens/spacing.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/layout/dashboard-layout.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/components/buttons.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/components/card.css') ?>">
<?php if ($page === 'dashboard'): ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/dashboard.css') ?>">
<?php elseif ($page === 'appform'): ?>
<link rel="stylesheet" href="<?= base_url('assets/css/components/form-progress.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/pages/applicant-form.css') ?>">
<?php elseif ($page === 'documents'): ?>
<link rel="stylesheet" href="<?= base_url('assets/css/components/form-progress.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/pages/requirements.css') ?>">
<?php elseif ($page === 'track'): ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/tracking_progress.css') ?>">
<?php endif; ?>
<?php endif; ?>

<!-- Scale content to fit the preview frame -->
<style>
html {
    /* will be overridden by JS once we know the real viewport width */
    overflow: hidden;
}
body { overflow: hidden; }
<?php if (!$isAuthPage): ?>
.dashboard-layout { height: 100vh; overflow: hidden; }
.main-content { overflow: hidden; }
.content-container { overflow-y: auto; }
<?php endif; ?>
</style>
<!-- Run BEFORE first paint so there's no flash of unscaled content -->
<script>
(function() {
    var DESIGN_W = <?= $isAuthPage ? 1200 : 1280 ?>;
    var s = window.innerWidth / DESIGN_W;
    if (s < 1) {
        // zoom scales layout AND renders — best for preview iframes
        document.documentElement.style.zoom = s;
        // compensate the shrunken height so the frame still fills
        document.documentElement.style.height = Math.round(100 / s) + 'vh';
    }
})();
</script>
</head>
<body>

<!-- postMessage listener: parent admin page sends theme updates here -->
<script>
window.addEventListener('message', function(e) {
    if (!e.data || e.data.type !== 'theme-update') return;
    var t = e.data.theme;
    var r = document.documentElement;
    if (t.primary_color) r.style.setProperty('--primary',      t.primary_color);
    if (t.primary_dark)  r.style.setProperty('--primary-dark', t.primary_dark);
    if (t.primary_light) r.style.setProperty('--primary-light',t.primary_light);
    if (t.accent_color)  r.style.setProperty('--accent',       t.accent_color);
    if (t.accent_text)   r.style.setProperty('--accent-text',  t.accent_text);
    if (t.sidebar_color) r.style.setProperty('--sidebar',      t.sidebar_color);
    if (t.border_radius) r.style.setProperty('--radius',       t.border_radius);
    if (t.font_family) {
        r.style.setProperty('--font', "'" + t.font_family + "', sans-serif");
        document.body.style.fontFamily = "'" + t.font_family + "', sans-serif";
    }
    if (t.primary_light) {
        r.style.setProperty('--card-gradient',
            'linear-gradient(180deg, rgba(255,255,255,0.61), ' + t.primary_light + ')');
    }
    // Update logo & app name if they changed
    if (t.logo_url !== undefined) {
        document.querySelectorAll('.preview-logo-img').forEach(function(el) {
            el.src = t.logo_url || '<?= base_url('assets/images/logo_akademus.png') ?>';
        });
    }
    if (t.app_name !== undefined) {
        document.querySelectorAll('.preview-app-name').forEach(function(el) {
            el.textContent = t.app_name || 'AKADEMUS';
        });
    }
    if (t.school_year !== undefined) {
        document.querySelectorAll('.preview-school-year').forEach(function(el) {
            el.textContent = t.school_year || 'SY 2025-2026';
        });
    }
});
</script>

<!-- ═══════════════════════════════════════════════════════════════
     AUTH PAGES: SIGN IN / SIGN UP / SUCCESS
     ═══════════════════════════════════════════════════════════════ -->

<?php if ($page === 'signin'): ?>

<!-- NAVBAR -->
<div class="header">
    <a href="#" class="brand">
        <img class="preview-logo-img" src="<?= $logoUrl ?>" alt="logo">
        <span class="brand-name preview-app-name"><?= $appName ?></span>
    </a>
    <div class="nav-links">
        <a href="#"><p>About Us</p></a>
        <a href="#"><p>Features</p></a>
        <div class="nav-dropdown">
            <p class="nav-dropdown-trigger">Services ▾</p>
        </div>
        <a href="#"><p>Contact</p></a>
    </div>
    <div class="nav-right">
        <a href="#" class="get-started-btn">Get Started</a>
    </div>
</div>

<!-- MAIN CARD -->
<div class="main-container">
    <div class="main-card">
        <!-- LEFT -->
        <div class="left">
            <div class="left-content-container">
                <a href="#" class="brand">
                    <img class="preview-logo-img" src="<?= $logoUrl ?>" alt="logo" style="width:52px;height:52px;border-radius:var(--radius);object-fit:contain;">
                    <div class="header-wrapper">
                        <p class="preview-app-name"><?= $appName ?></p>
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
                        <div class="step"><div class="step-number">1</div><span>Create your applicant account</span></div>
                        <div class="step"><div class="step-number">2</div><span>Submit required documents</span></div>
                        <div class="step"><div class="step-number">3</div><span>Track your application status</span></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- RIGHT -->
        <div class="right">
            <div class="auth-header-container">
                <h2>Sign In</h2>
                <span>Welcome back! Sign in to continue.</span>
            </div>
            <div class="switch-auth-toggle">
                <div class="toggle-btn active">Sign In</div>
                <div class="toggle-btn">Sign Up</div>
            </div>
            <div id="auth-form-container">
                <form class="form-container">
                    <div class="input-container">
                        <div class="input-field">
                            <label>EMAIL ADDRESS</label>
                            <input type="email" placeholder="yourname@email.com">
                        </div>
                        <div class="input-field">
                            <label>PASSWORD</label>
                            <input type="password" placeholder="********">
                        </div>
                    </div>
                    <div class="forgot-pass">
                        <a href="#">Forgot Password?</a>
                    </div>
                    <button type="button" class="submit-btn">Sign In</button>
                    <div class="switch-to-sign-up">
                        <span>Don't have an account? <a href="#">Click here</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php elseif ($page === 'signup'): ?>

<!-- NAVBAR -->
<div class="header">
    <a href="#" class="brand">
        <img class="preview-logo-img" src="<?= $logoUrl ?>" alt="logo">
        <span class="brand-name preview-app-name"><?= $appName ?></span>
    </a>
    <div class="nav-links">
        <a href="#"><p>About Us</p></a>
        <a href="#"><p>Features</p></a>
        <div class="nav-dropdown"><p class="nav-dropdown-trigger">Services ▾</p></div>
        <a href="#"><p>Contact</p></a>
    </div>
    <div class="nav-right"><a href="#" class="get-started-btn">Get Started</a></div>
</div>

<div class="main-container">
    <div class="main-card">
        <div class="left">
            <div class="left-content-container">
                <a href="#" class="brand">
                    <img class="preview-logo-img" src="<?= $logoUrl ?>" alt="logo" style="width:52px;height:52px;border-radius:var(--radius);object-fit:contain;">
                    <div class="header-wrapper">
                        <p class="preview-app-name"><?= $appName ?></p>
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
                        <div class="step"><div class="step-number">1</div><span>Create your applicant account</span></div>
                        <div class="step"><div class="step-number">2</div><span>Submit required documents</span></div>
                        <div class="step"><div class="step-number">3</div><span>Track your application status</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="auth-header-container">
                <h2>Create Account</h2>
                <span>Fill in your details to get started.</span>
            </div>
            <div class="switch-auth-toggle">
                <div class="toggle-btn">Sign In</div>
                <div class="toggle-btn active">Sign Up</div>
            </div>
            <div id="auth-form-container">
                <form class="form-container">
                    <div class="input-container">
                        <div class="two-input-fields">
                            <div class="input-field">
                                <label>FIRST NAME</label>
                                <input type="text" placeholder="John">
                            </div>
                            <div class="input-field">
                                <label>LAST NAME</label>
                                <input type="text" placeholder="Doe">
                            </div>
                        </div>
                        <div class="input-field">
                            <label>EMAIL ADDRESS</label>
                            <input type="email" placeholder="yourname@email.com">
                        </div>
                        <div class="input-field">
                            <label>MOBILE NUMBER</label>
                            <input type="tel" placeholder="09XXXXXXXXX">
                        </div>
                        <div class="input-field">
                            <label>CHOICE OF PROGRAM</label>
                            <select>
                                <option disabled selected hidden>Select a Program</option>
                                <option>BS Computer Science</option>
                                <option>BS Information Technology</option>
                                <option>BS Nursing</option>
                            </select>
                        </div>
                    </div>
                    <div class="note-container">
                        <span>Note:</span>
                        <p>Your final program will be confirmed in the Applicant Information Form. These preferences help us process your application.</p>
                    </div>
                    <button type="button" class="submit-btn">CREATE ACCOUNT</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php elseif ($page === 'success'): ?>

<div class="header">
    <a href="#" class="brand">
        <img class="preview-logo-img" src="<?= $logoUrl ?>" alt="logo">
        <span class="brand-name preview-app-name"><?= $appName ?></span>
    </a>
    <div class="nav-links">
        <a href="#"><p>About Us</p></a>
        <a href="#"><p>Features</p></a>
        <div class="nav-dropdown"><p class="nav-dropdown-trigger">Services ▾</p></div>
        <a href="#"><p>Contact</p></a>
    </div>
    <div class="nav-right"><a href="#" class="get-started-btn">Get Started</a></div>
</div>

<div class="main-container">
    <div class="main-card">
        <div class="left">
            <div class="left-content-container">
                <a href="#" class="brand">
                    <img class="preview-logo-img" src="<?= $logoUrl ?>" alt="logo" style="width:52px;height:52px;border-radius:var(--radius);object-fit:contain;">
                    <div class="header-wrapper">
                        <p class="preview-app-name"><?= $appName ?></p>
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
                        <div class="step"><div class="step-number">1</div><span>Create your applicant account</span></div>
                        <div class="step"><div class="step-number">2</div><span>Submit required documents</span></div>
                        <div class="step"><div class="step-number">3</div><span>Track your application status</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right" style="justify-content:center;align-items:center;">
            <div style="display:flex;flex-direction:column;gap:20px;width:100%;">
                <div>
                    <h2 style="font-size:28px;font-weight:700;color:var(--primary);line-height:1.2;">Registration Successful! 🎉</h2>
                    <p style="font-size:14px;color:#666;margin-top:6px;">Your account has been created successfully.</p>
                </div>
                <div style="width:100%;padding:20px;background:rgba(0,64,166,0.07);border-radius:var(--radius);border:1.5px solid rgba(0,64,166,0.2);text-align:center;">
                    <p style="font-size:12px;color:var(--primary);font-weight:600;margin-bottom:8px;">Applicant ID</p>
                    <p style="font-size:28px;font-weight:700;color:#031D46;">#APP-2026-00001</p>
                </div>
                <p style="font-size:13px;color:#555;line-height:1.6;">A welcome email with your ID has been sent to your registered email address. Keep this for your records.</p>
                <a href="#" style="display:block;padding:13px;background:var(--primary);color:#fff;border-radius:var(--radius);font-size:14px;font-weight:700;text-align:center;text-decoration:none;">GO BACK TO LOGIN</a>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     PORTAL PAGES: DASHBOARD / APP FORM / DOCUMENTS / TRACK
     ═══════════════════════════════════════════════════════════════ -->

<?php else: ?>

<?php
$activePage = $page;
$navLinks = [
    ['key' => 'dashboard', 'href' => '#', 'label' => 'Dashboard'],
    ['key' => 'appform',   'href' => '#', 'label' => 'Applicant Form'],
    ['key' => 'documents', 'href' => '#', 'label' => 'Requirements'],
    ['key' => 'track',     'href' => '#', 'label' => 'Tracking Progress'],
];
$pageTitles = [
    'dashboard' => 'Dashboard',
    'appform'   => 'Applicant Form',
    'documents' => 'Requirements',
    'track'     => 'Tracking Progress',
];
?>

<div class="dashboard-layout">

    <!-- SIDEBAR -->
    <div class="sidebar-container" id="sidebar">
        <nav>
            <div class="sidebar-top-and-links">
                <div class="sidebar-top">
                    <a href="#" class="brand">
                        <img class="preview-logo-img" src="<?= $logoUrl ?>" alt="logo">
                        <div class="header-wrapper">
                            <p class="preview-app-name"><?= $appName ?></p>
                            <span>Admission Portal</span>
                        </div>
                    </a>
                    <div class="applicant-id-box">
                        <span>Applicant ID</span>
                        <p>#APP-2026-00001</p>
                    </div>
                </div>

                <div class="sidebar-links">
                    <div class="group-container">
                        <p>MENU</p>
                        <div class="grouped-links">
                            <?php foreach ($navLinks as $nl): ?>
                            <a href="#" class="sidebar-nav-link <?= $activePage === $nl['key'] ? 'active' : '' ?>">
                                <div class="dotted-link"></div>
                                <span><?= $nl['label'] ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="group-container">
                        <p>PAYMENTS</p>
                        <div class="grouped-links">
                            <a href="#" class="sidebar-nav-link"><div class="dotted-link"></div><span>Admission Processing Fee</span></a>
                            <a href="#" class="sidebar-nav-link"><div class="dotted-link"></div><span>Reservation Fee</span></a>
                        </div>
                    </div>
                    <div class="group-container">
                        <p>SUPPORT</p>
                        <div class="grouped-links">
                            <a href="#" class="sidebar-nav-link"><div class="dotted-link"></div><span>FAQ &amp; Chat Support</span></a>
                            <a href="#" class="sidebar-nav-link"><div class="dotted-link"></div><span>Follow-Up Messages</span></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidebar-bottom">
                <div class="profile-container">
                    <div class="profile-picture"><span>JD</span></div>
                    <div class="profile-info">
                        <p>Juan Dela Cruz</p>
                        <span>Applicant</span>
                    </div>
                </div>
                <a href="#"><span>Logout</span></a>
            </div>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="header">
            <h1><?= esc($pageTitles[$activePage] ?? 'Dashboard') ?></h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="application-status">Approved</span>
            </div>
        </div>

        <div class="content-container">

            <!-- ─── DASHBOARD ─── -->
            <?php if ($page === 'dashboard'): ?>

            <div class="dashboard-container">

                <div class="profile-banner">
                    <div class="top-most-span">
                        <span class="preview-school-year"><?= esc($theme['school_year'] ?? 'SY 2025-2026') ?> · <?= $appName ?></span>
                    </div>
                    <div class="banner-name-header">
                        <h1>Hello, <span>Juan.</span></h1>
                    </div>
                    <div class="banner-header-info">
                        <p>Your application is in progress. Upload the remaining documents to proceed with your admission.</p>
                    </div>
                    <div class="lower-profile-banner">
                        <div class="profile-banner-applicant-id-box">
                            <span>Applicant ID</span>
                            <p>#APP-2026-00001</p>
                        </div>
                        <div class="quick-actions">
                            <a href="#" style="padding:10px 20px;background:var(--accent);color:var(--accent-text);border-radius:var(--radius);font-size:13px;font-weight:700;text-decoration:none;">Upload Documents</a>
                            <a href="#" style="padding:10px 20px;background:rgba(255,255,255,0.15);color:#fff;border-radius:var(--radius);font-size:13px;font-weight:700;text-decoration:none;border:1px solid rgba(255,255,255,0.3);">Pay Reservation Fee</a>
                        </div>
                    </div>
                </div>

                <div class="application-progress">
                    <div class="app-progress-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <h2 style="font-size:18px;font-weight:700;color:#1e1e1e;">Overall Application Progress</h2>
                        <span style="font-size:18px;font-weight:700;color:var(--primary);">60%</span>
                    </div>
                    <div class="steps-container" style="display:flex;justify-content:space-between;">
                        <?php
                        $steps = [
                            ['num'=>1,'label'=>'Account','status'=>'done'],
                            ['num'=>2,'label'=>'Form','status'=>'inprogress'],
                            ['num'=>3,'label'=>'Documents','status'=>''],
                            ['num'=>4,'label'=>'Verified','status'=>''],
                            ['num'=>5,'label'=>'Enrolled','status'=>''],
                        ];
                        foreach ($steps as $s):
                            $barClass    = $s['status']==='done' ? 'step-bar-done'    : ($s['status']==='inprogress' ? 'step-bar-inprogress'    : '');
                            $lineClass   = $s['status']==='done' ? 'step-line-done'   : ($s['status']==='inprogress' ? 'step-line-inprogress'   : '');
                            $circleClass = $s['status']==='done' ? 'step-circle-done' : ($s['status']==='inprogress' ? 'step-circle-inprogress' : '');
                            $labelClass  = $s['status']==='done' ? 'step-label-done'  : ($s['status']==='inprogress' ? 'step-label-inprogress'  : '');
                        ?>
                        <div class="step-box">
                            <div class="step-bar-container"><div class="step-bar <?= $barClass ?>"></div></div>
                            <div class="step-bottom">
                                <div class="step-ring">
                                    <?php if ($s['num']===1): ?><div class="step-line left-line"></div><?php else: ?><div class="step-line <?= $lineClass ?>"></div><?php endif; ?>
                                    <div class="step-circle <?= $circleClass ?>"><?= $s['status']==='done' ? '✓' : $s['num'] ?></div>
                                    <?php if ($s['num']===5): ?><div class="step-line right-line"></div><?php else: ?><div class="step-line <?= $lineClass ?>"></div><?php endif; ?>
                                </div>
                                <span class="<?= $labelClass ?>"><?= $s['label'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="checklist-summary-container" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:16px;">
                    <!-- Form Checklist -->
                    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:var(--radius);padding:20px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <h3 style="font-size:16px;font-weight:700;color:#1e1e1e;">Applicant Form</h3>
                            <span style="font-size:12px;color:#6e6e6e;">2 of 4 sections</span>
                        </div>
                        <div style="height:6px;background:#f0f4f8;border-radius:99px;margin-bottom:14px;overflow:hidden;">
                            <div style="height:100%;width:50%;background:var(--primary);border-radius:99px;"></div>
                        </div>
                        <?php foreach(['Personal Information' => true, 'Academic Background' => true, 'Contact Details' => false, 'Review & Submit' => false] as $label => $done): ?>
                        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f5f5f5;">
                            <div style="width:18px;height:18px;border-radius:50%;background:<?= $done ? 'var(--accent)' : '#e5e7eb' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:700;color:<?= $done ? 'var(--accent-text)' : '#9ca3af' ?>;"><?= $done ? '✓' : '' ?></div>
                            <span style="font-size:13px;color:<?= $done ? '#1e1e1e' : '#9ca3af' ?>;font-weight:<?= $done ? 600 : 400 ?>;"><?= $label ?></span>
                        </div>
                        <?php endforeach; ?>
                        <a href="#" style="display:inline-block;margin-top:14px;padding:8px 18px;background:var(--primary);color:#fff;border-radius:var(--radius);font-size:13px;font-weight:700;text-decoration:none;">Continue</a>
                    </div>

                    <!-- Documents Checklist -->
                    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:var(--radius);padding:20px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <h3 style="font-size:16px;font-weight:700;color:#1e1e1e;">Required Documents</h3>
                            <span style="font-size:12px;color:#6e6e6e;">3 of 10 uploaded</span>
                        </div>
                        <div style="height:6px;background:#f0f4f8;border-radius:99px;margin-bottom:14px;overflow:hidden;">
                            <div style="height:100%;width:30%;background:var(--primary);border-radius:99px;"></div>
                        </div>
                        <?php
                        $docs = ['Birth Certificate'=>'uploaded','Form 138 / Report Card'=>'uploaded','Good Moral Certificate'=>'uploaded','2x2 ID Photo'=>'pending','Medical Certificate'=>'missing'];
                        foreach($docs as $docName => $docStatus):
                            $color = $docStatus==='uploaded' ? '#22c55e' : ($docStatus==='pending' ? '#f59e0b' : '#9ca3af');
                            $bg    = $docStatus==='uploaded' ? '#f0fdf4' : ($docStatus==='pending' ? '#fffbeb' : '#f9fafb');
                            $icon  = $docStatus==='uploaded' ? '✓' : ($docStatus==='pending' ? '⏳' : '—');
                        ?>
                        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f5f5f5;">
                            <div style="width:18px;height:18px;border-radius:50%;background:<?= $bg ?>;border:1px solid <?= $color ?>22;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;color:<?= $color ?>;"><?= $icon ?></div>
                            <span style="font-size:13px;color:#1e1e1e;flex:1;"><?= $docName ?></span>
                            <span style="font-size:11px;font-weight:600;color:<?= $color ?>;"><?= ucfirst($docStatus) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <a href="#" style="display:inline-block;margin-top:14px;padding:8px 18px;background:var(--primary);color:#fff;border-radius:var(--radius);font-size:13px;font-weight:700;text-decoration:none;">Upload Documents</a>
                    </div>
                </div>

            </div><!-- /.dashboard-container -->

            <!-- ─── APPLICATION FORM ─── -->
            <?php elseif ($page === 'appform'): ?>

            <div class="applicant-form-container">

                <div class="applicant-form-progress-container">
                    <h4>Application Form Progress</h4>
                    <div class="progress-timeline-container">
                        <?php
                        $formSteps = ['Personal Info', 'Academic Background', 'Contact Details', 'Review & Submit'];
                        foreach ($formSteps as $i => $stepLabel):
                            $isDone   = $i < 1;
                            $isActive = $i === 1;
                        ?>
                        <div class="progress-box">
                            <div class="progress-circle <?= $isDone ? 'progress-circle-done' : ($isActive ? 'progress-circle-active' : '') ?>"
                                 style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;
                                        background:<?= $isDone ? 'var(--accent)' : ($isActive ? 'var(--primary)' : '#e5e7eb') ?>;
                                        color:<?= $isDone ? 'var(--accent-text)' : ($isActive ? '#fff' : '#9ca3af') ?>;">
                                <?= $isDone ? '✓' : ($i + 1) ?>
                            </div>
                            <span style="font-size:12px;color:<?= $isActive ? 'var(--primary)' : ($isDone ? '#1e1e1e' : '#9ca3af') ?>;font-weight:<?= $isActive ? 700 : 500 ?>;"><?= $stepLabel ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="background:#fff;border:1px solid rgba(0,0,0,0.1);border-radius:var(--radius);box-shadow:0 0 10px rgba(0,0,0,0.08);overflow:hidden;">
                    <div style="padding:20px 40px;border-bottom:1px solid #f0f0f0;">
                        <h3 style="font-size:18px;font-weight:600;color:#1e1e1e;margin-bottom:4px;">Academic Background</h3>
                        <p style="font-size:13px;color:#6e6e6e;">Please fill in your academic information below.</p>
                    </div>
                    <div style="padding:24px 40px;display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <?php
                        $fields = [
                            'Previous School' => 'St. Paul University Manila',
                            'Year Level'      => 'Grade 12 / Senior High',
                            'Program Applying For' => 'BS Computer Science',
                            'GWA / Grade Average'  => '95.50',
                            'Strand / Track' => 'STEM',
                            'School Address' => 'Manila, Philippines',
                        ];
                        foreach ($fields as $label => $value):
                        ?>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            <label style="font-size:12px;font-weight:700;color:#5a6a85;text-transform:uppercase;letter-spacing:0.5px;"><?= $label ?></label>
                            <div style="padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:var(--radius);font-size:13px;color:#1e1e1e;background:#fafafa;"><?= $value ?></div>
                        </div>
                        <?php endforeach; ?>
                        <div style="grid-column:1/-1;display:flex;flex-direction:column;gap:6px;">
                            <label style="font-size:12px;font-weight:700;color:#5a6a85;text-transform:uppercase;letter-spacing:0.5px;">Honors & Awards</label>
                            <div style="padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:var(--radius);font-size:13px;color:#6e6e6e;background:#fafafa;min-height:60px;">With Honors, Academic Excellence Award 2024, Best in Mathematics</div>
                        </div>
                    </div>
                    <div style="padding:16px 40px;border-top:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                        <a href="#" style="padding:10px 22px;border:1.5px solid #e5e7eb;border-radius:var(--radius);font-size:13px;font-weight:600;color:#6e6e6e;text-decoration:none;">← Back</a>
                        <a href="#" style="padding:10px 22px;background:var(--primary);color:#fff;border-radius:var(--radius);font-size:13px;font-weight:700;text-decoration:none;">Save & Continue →</a>
                    </div>
                </div>

            </div>

            <!-- ─── DOCUMENTS / REQUIREMENTS ─── -->
            <?php elseif ($page === 'documents'): ?>

            <div class="requirements">

                <!-- Progress bar -->
                <section class="form-progress card">
                    <h4 class="form-progress__title">Step 1 of 4 — Primary Documents</h4>
                    <div class="form-progress__timeline">
                        <div class="form-progress__step">
                            <div class="form-progress__circle form-progress__circle-done">1</div>
                            <span>Primary Docs</span>
                        </div>
                        <div class="form-progress__line form-progress__line-done"></div>
                        <div class="form-progress__step">
                            <div class="form-progress__circle form-progress__circle-active">2</div>
                            <span>Academic</span>
                        </div>
                        <div class="form-progress__line"></div>
                        <div class="form-progress__step">
                            <div class="form-progress__circle">3</div>
                            <span>Medical</span>
                        </div>
                        <div class="form-progress__line"></div>
                        <div class="form-progress__step">
                            <div class="form-progress__circle">4</div>
                            <span>Review</span>
                        </div>
                    </div>
                </section>

                <!-- Content card -->
                <section class="requirements__content card">
                    <form class="requirements-step">

                        <div class="requirements-section__header">
                            <div class="requirements-section__header-top">
                                <h3>Academic Documents</h3>
                                <span class="upload-counter">2/3 required uploaded</span>
                            </div>
                            <p>Submit certified copies of your academic records.</p>
                        </div>

                        <div class="form-uploads">
                            <div class="form-field">
                                <label class="upload-field">
                                    <div class="requirement-upload-info">
                                        <p class="requirement-title">Form 138 / Report Card <span class="required">REQUIRED</span></p>
                                        <p class="requirement-description">Official copy from your previous school.</p>
                                    </div>
                                    <div class="requirement-upload-actions">
                                        <div class="requirement-upload-action btn-primary">Upload</div>
                                    </div>
                                </label>
                            </div>
                            <div class="form-field">
                                <label class="upload-field">
                                    <div class="requirement-upload-info">
                                        <p class="requirement-title">Good Moral Certificate <span class="required">REQUIRED</span></p>
                                        <p class="requirement-description">Issued by your school principal or guidance counselor.</p>
                                    </div>
                                    <div class="requirement-upload-actions">
                                        <div class="requirement-upload-action btn-primary">Upload</div>
                                    </div>
                                </label>
                            </div>
                            <div class="form-field">
                                <label class="upload-field">
                                    <div class="requirement-upload-info">
                                        <p class="requirement-title">Birth Certificate (PSA Copy)</p>
                                        <p class="requirement-description">Authenticated copy from PSA / NSO.</p>
                                    </div>
                                    <div class="requirement-upload-actions">
                                        <div class="requirement-upload-action btn-primary">Upload</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                    </form>

                    <footer class="requirements-actions">
                        <span class="requirements-actions__status">Auto-saved as draft</span>
                        <div class="requirements-actions__buttons">
                            <button type="button" class="btn-secondary">Back</button>
                            <button type="button" class="btn-primary">Save &amp; Continue</button>
                        </div>
                    </footer>
                </section>

            </div>

            <!-- ─── TRACKING PROGRESS ─── -->
            <?php elseif ($page === 'track'): ?>

            <div class="tp-page">
                <div class="tp-status-card tp-status-card--success">
                    <div class="tp-status-left">
                        <h2 class="tp-status-title">Application Approved</h2>
                        <p class="tp-status-desc">Congratulations! Your application has been approved. Please proceed with the next steps.</p>
                    </div>
                    <div class="tp-status-right">
                        <span class="tp-applicant-id-label">Applicant ID</span>
                        <span class="tp-applicant-id-number">#APP-2026-00001</span>
                    </div>
                </div>

                <?php
                $timeline = [
                    ['label'=>'Application Submitted',   'date'=>'Feb 10, 2026', 'status'=>'done'],
                    ['label'=>'Documents Verified',       'date'=>'Feb 14, 2026', 'status'=>'done'],
                    ['label'=>'Entrance Exam Completed',  'date'=>'Feb 20, 2026', 'status'=>'done'],
                    ['label'=>'Interview',                'date'=>'Mar 5, 2026',  'status'=>'done'],
                    ['label'=>'Final Decision',           'date'=>'Mar 12, 2026', 'status'=>'active'],
                    ['label'=>'Enrollment',               'date'=>'Pending',      'status'=>''],
                ];
                ?>
                <div style="background:#fff;border:0.3px solid rgba(0,0,0,0.15);border-radius:15px;padding:28px 44px;">
                    <h3 style="font-size:18px;font-weight:600;color:#111;margin-bottom:24px;">Application Timeline</h3>
                    <?php foreach ($timeline as $i => $step): ?>
                    <div style="display:flex;gap:16px;margin-bottom:<?= $i < count($timeline)-1 ? '16px' : '0' ?>;">
                        <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;">
                            <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                        background:<?= $step['status']==='done' ? 'var(--accent)' : ($step['status']==='active' ? 'var(--primary)' : '#e5e7eb') ?>;
                                        border:2px solid <?= $step['status']==='done' ? 'var(--accent)' : ($step['status']==='active' ? 'var(--primary)' : '#e5e7eb') ?>;">
                                <?php if ($step['status']==='done'): ?>
                                    <svg viewBox="0 0 32 33" fill="none" style="width:18px;height:18px;"><path d="M14.1067 21.4225C13.84 21.4225 13.5867 21.3125 13.4 21.12L9.62667 17.2288C9.24 16.83 9.24 16.17 9.62667 15.7713C10.0133 15.3725 10.6533 15.3725 11.04 15.7713L14.1067 18.9338L20.96 11.8663C21.3467 11.4675 21.9867 11.4675 22.3733 11.8663C22.76 12.265 22.76 12.925 22.3733 13.3237L14.8133 21.12C14.6267 21.3125 14.3733 21.4225 14.1067 21.4225Z" fill="var(--accent-text)"/></svg>
                                <?php elseif ($step['status']==='active'): ?>
                                    <svg viewBox="0 0 32 33" fill="none" style="width:18px;height:18px;"><circle cx="16" cy="16.5" r="5" fill="#fff"/></svg>
                                <?php else: ?>
                                    <span style="font-size:12px;font-weight:700;color:#9ca3af;"><?= $i+1 ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($i < count($timeline)-1): ?>
                            <div style="width:2px;flex:1;min-height:20px;background:<?= $step['status']==='done' ? 'var(--accent)' : '#e5e7eb' ?>;margin:4px 0;"></div>
                            <?php endif; ?>
                        </div>
                        <div style="padding-bottom:<?= $i < count($timeline)-1 ? '4px' : '0' ?>;">
                            <p style="font-size:15px;font-weight:<?= $step['status']==='active' ? 700 : 600 ?>;
                                      color:<?= $step['status']==='active' ? 'var(--primary)' : ($step['status']==='done' ? '#1e1e1e' : '#9ca3af') ?>;
                                      margin:0 0 2px;"><?= $step['label'] ?></p>
                            <p style="font-size:12px;color:#9ca3af;margin:0;"><?= $step['date'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <?php endif; ?>

        </div><!-- /.content-container -->
    </main>

</div><!-- /.dashboard-layout -->

<?php endif; ?>

</body>
</html>
