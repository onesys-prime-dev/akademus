<!-- dashboard/main.php -->

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

    <?= theme_css_vars() ?>

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
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/dashboard-layout.css') . '?v=' . time(); ?>">

    <!-- Components CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/components/buttons.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/card.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/form-field.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/form-progress.css') . '?v=' . time(); ?>">

    <!-- Utilities CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/utilities/utilities.css') . '?v=' . time(); ?>">

    <!-- Pages CSS Styles -->
    <?php
    $pageName = basename($page_content);
    $cssPath = FCPATH . "assets/css/pages/{$pageName}.css";

    if (file_exists($cssPath)):
    ?>
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/{$pageName}.css?v=" . time()) ?>">
    <?php endif; ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">
<script>
(function(){var _f=window.fetch;window.fetch=function(u,o){o=o||{};if(o.method&&o.method.toUpperCase()!=='GET'){var t=document.querySelector('meta[name="csrf-token"]');if(t){o.headers=o.headers||{};if(o.headers instanceof Headers){o.headers.set('X-CSRF-TOKEN',t.content);}else{o.headers['X-CSRF-TOKEN']=t.content;}}}return _f.call(this,u,o);};})();
</script>
</head>

<body>
    <div class="dashboard-layout">
        <!-- Mobile sidebar overlay backdrop -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <div class="sidebar-container" id="sidebar">
            <!-- Mobile close button inside sidebar -->
            <button class="sidebar-close" id="sidebar-close" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <?= view('applicant/components/sidebar', ['theme' => $theme]) ?>
        </div>

        <main class="main-content">
            <div class="header">
                <!-- Hamburger button (hidden on desktop via CSS) -->
                <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Open menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6"  x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1><?= $title ?></h1>
                <!-- Notification Bell -->
                <div style="position:relative;" id="notifWrap">
                    <button id="notifBell" onclick="toggleNotifDropdown()" style="
                        position:relative;background:none;border:none;
                        cursor:pointer;display:flex;align-items:center;padding:4px;
                    ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="color:var(--primary);">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <span id="applicantMsgBadge" style="
                            display:none;position:absolute;top:-3px;right:-3px;
                            min-width:16px;height:16px;padding:0 4px;
                            background:#e53e3e;color:#fff;font-size:9px;font-weight:800;
                            border-radius:999px;align-items:center;justify-content:center;
                            border:2px solid #fff;line-height:1;
                        "></span>
                    </button>

                    <!-- Dropdown -->
                    <div id="notifDropdown" style="
                        display:none;position:absolute;top:calc(100% + 10px);right:0;
                        width:320px;background:#fff;
                        border:1.5px solid #e2e8f0;border-radius:12px;
                        box-shadow:0 12px 40px rgba(0,0,0,0.12);
                        z-index:9999;overflow:hidden;
                    ">
                        <!-- Header -->
                        <div style="
                            display:flex;align-items:center;justify-content:space-between;
                            padding:14px 16px;border-bottom:1.5px solid #f0f4f8;
                        ">
                            <span style="font-size:13px;font-weight:700;color:#1a202c;">Notifications</span>
                            <a href="<?= base_url('/follow-up-messages') ?>" style="
                                font-size:11px;font-weight:600;color:var(--primary);text-decoration:none;
                            ">View all</a>
                        </div>
                        <!-- Items -->
                        <div id="notifList" style="max-height:320px;overflow-y:auto;"></div>
                        <!-- Empty state -->
                        <div id="notifEmpty" style="
                            display:none;padding:28px 16px;text-align:center;
                            color:#a0aec0;font-size:13px;
                        ">No new notifications</div>
                    </div>
                </div>
            </div>

            <div class="content-container">
                <?= view($page_content) ?>
            </div>
        </main>
    </div>

    <?php if (uri_string() !== 'faq'): ?>
        <?= view('applicant/components/chat_widget', ['theme' => $theme]) ?>
    <?php endif; ?>

    <?php if (session()->get('temp_password')): ?>
    <!-- Force password change modal -->
    <div id="changePwModal" style="
        position:fixed;inset:0;z-index:99999;
        display:flex;align-items:center;justify-content:center;
        background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);
    ">
        <div style="
            background:#fff;border-radius:16px;width:100%;max-width:420px;
            margin:16px;box-shadow:0 24px 60px rgba(0,0,0,0.2);
            overflow:hidden;
        ">
            <!-- Header -->
            <div style="background:var(--primary);padding:24px 24px 20px;">
                <div style="
                    width:44px;height:44px;border-radius:50%;
                    background:rgba(255,255,255,0.15);
                    display:flex;align-items:center;justify-content:center;
                    margin-bottom:12px;
                ">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h2 style="margin:0;font-size:18px;font-weight:700;color:#fff;">Set Your Password</h2>
                <p style="margin:4px 0 0;font-size:13px;color:rgba(255,255,255,0.75);">
                    You're using a temporary password. Please set a new one to continue.
                </p>
            </div>

            <!-- Form -->
            <div style="padding:24px;">
                <div id="changePwError" style="
                    display:none;margin-bottom:14px;padding:10px 14px;
                    background:#fff5f5;border:1.5px solid #fed7d7;
                    border-radius:8px;font-size:13px;color:#c53030;
                "></div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">
                        New Password
                    </label>
                    <div style="position:relative;">
                        <input type="password" id="cpNewPw" placeholder="At least 8 characters" style="
                            width:100%;padding:10px 38px 10px 12px;box-sizing:border-box;
                            border:1.5px solid #e2e8f0;border-radius:8px;
                            font-size:13.5px;outline:none;
                            font-family:inherit;transition:border-color 0.2s;
                        " onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                        <button type="button" onclick="cpToggle('cpNewPw',this)" style="
                            position:absolute;right:10px;top:50%;transform:translateY(-50%);
                            background:none;border:none;cursor:pointer;color:#a0aec0;padding:0;
                        ">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">
                        Confirm Password
                    </label>
                    <div style="position:relative;">
                        <input type="password" id="cpConfirmPw" placeholder="Re-enter your password" style="
                            width:100%;padding:10px 38px 10px 12px;box-sizing:border-box;
                            border:1.5px solid #e2e8f0;border-radius:8px;
                            font-size:13.5px;outline:none;
                            font-family:inherit;transition:border-color 0.2s;
                        " onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'"
                        onkeydown="if(event.key==='Enter') cpSubmit()">
                        <button type="button" onclick="cpToggle('cpConfirmPw',this)" style="
                            position:absolute;right:10px;top:50%;transform:translateY(-50%);
                            background:none;border:none;cursor:pointer;color:#a0aec0;padding:0;
                        ">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button onclick="cpSubmit()" id="cpBtn" style="
                    width:100%;padding:11px;background:var(--primary);color:#fff;
                    border:none;border-radius:8px;font-size:14px;font-weight:600;
                    cursor:pointer;font-family:inherit;transition:opacity 0.2s;
                ">Save Password</button>
            </div>
        </div>
    </div>

    <script>
    function cpToggle(inputId, btn) {
        const input = document.getElementById(inputId);
        input.type  = input.type === 'password' ? 'text' : 'password';
    }

    async function cpSubmit() {
        const btn     = document.getElementById('cpBtn');
        const errBox  = document.getElementById('changePwError');
        const newPw   = document.getElementById('cpNewPw').value;
        const confPw  = document.getElementById('cpConfirmPw').value;

        errBox.style.display = 'none';
        btn.disabled  = true;
        btn.textContent = 'Saving...';

        const form = new FormData();
        form.append('new_password',     newPw);
        form.append('confirm_password', confPw);

        try {
            const res  = await fetch('<?= base_url('/change-password') ?>', { method: 'POST', body: form });
            const data = await res.json();

            if (data.success) {
                document.getElementById('changePwModal').remove();
            } else {
                errBox.textContent   = data.message;
                errBox.style.display = 'block';
                btn.disabled         = false;
                btn.textContent      = 'Save Password';
            }
        } catch (e) {
            errBox.textContent   = 'Something went wrong. Please try again.';
            errBox.style.display = 'block';
            btn.disabled         = false;
            btn.textContent      = 'Save Password';
        }
    }
    </script>
    <?php endif; ?>

    <script>
    // ── Notification bell dropdown ────────────────────────────────────────
    const UNREAD_URL   = '<?= base_url('/follow-up-messages/unread-count') ?>';
    const MESSAGES_URL = '<?= base_url('/follow-up-messages') ?>';

    function timeAgo(dateStr) {
        const diff = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T'))) / 1000);
        if (diff < 60)   return 'just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return Math.floor(diff / 86400) + 'd ago';
    }

    function renderNotifications(notifications) {
        const list  = document.getElementById('notifList');
        const empty = document.getElementById('notifEmpty');

        if (!notifications.length) {
            list.innerHTML   = '';
            empty.style.display = 'block';
            return;
        }

        empty.style.display = 'none';
        list.innerHTML = notifications.map(n => {
            const safeMsg = String(n.message).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            const preview = safeMsg.length > 60 ? safeMsg.slice(0, 60) + '…' : safeMsg;
            return `
            <a href="${MESSAGES_URL}" style="
                display:flex;align-items:flex-start;gap:10px;
                padding:12px 16px;text-decoration:none;
                border-bottom:1px solid #f0f4f8;
                transition:background 0.15s;
            " onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''"
            >
                <div style="
                    width:34px;height:34px;border-radius:50%;flex-shrink:0;
                    background:var(--primary);display:flex;align-items:center;
                    justify-content:center;font-size:15px;
                ">🎓</div>
                <div style="flex:1;min-width:0;">
                    <p style="margin:0;font-size:12.5px;font-weight:600;color:#1a202c;line-height:1.4;">
                        Admission Officer replied to your message
                    </p>
                    <p style="
                        margin:3px 0 0;font-size:12px;color:#4a5568;
                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                    ">"${preview}"</p>
                    <span style="font-size:10.5px;color:#a0aec0;margin-top:3px;display:block;">
                        ${timeAgo(n.created_at)}
                    </span>
                </div>
                <div style="
                    width:8px;height:8px;border-radius:50%;background:#e53e3e;
                    flex-shrink:0;margin-top:4px;
                "></div>
            </a>`;
        }).join('');
    }

    async function pollUnread() {
        try {
            const res  = await fetch(UNREAD_URL);
            const data = await res.json();
            if (!data.success) return;

            const badge = document.getElementById('applicantMsgBadge');
            const count = data.count ?? 0;

            if (count > 0) {
                badge.textContent   = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }

            renderNotifications(data.notifications ?? []);
        } catch (e) { /* silent */ }
    }

    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('notifWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('notifDropdown').style.display = 'none';
        }
    });

    pollUnread();
    setInterval(pollUnread, 30000);
    </script>

    <script>
        (function () {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const closeBtn  = document.getElementById('sidebar-close');

            function openSidebar() {
                sidebar.classList.add('sidebar-open');
                overlay.classList.add('sidebar-open');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('sidebar-open');
                overlay.classList.remove('sidebar-open');
                document.body.style.overflow = '';
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
            if (overlay)   overlay.addEventListener('click', closeSidebar);

            // Close sidebar on window resize if viewport grows past mobile breakpoint
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) closeSidebar();
            });
        })();
    </script>
</body>
</html>
