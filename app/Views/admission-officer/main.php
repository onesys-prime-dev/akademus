<!-- main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>
    <?= theme_favicon_tags() ?>

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

    <!-- Components CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/components/breadcrumb.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/buttons.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/card.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/top-controls.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/program-dropdown.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/status-badge.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/pagination.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/bulk-actions.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/modals.css') . '?v=' . time(); ?>">

    <!-- Layouts CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/dashboard-layout.css') . '?v=' . time(); ?>">

    <!-- Pages CSS Styles -->
    <?php
    $pageName = basename($page_content);
    $cssPath = FCPATH . "assets/css/pages/admission-officer/{$pageName}.css";

    if (file_exists($cssPath)):
    ?>
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/admission-officer/{$pageName}.css?v=" . time()) ?>">
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
            <?= view('admission-officer/components/sidebar', ['theme' => $theme]) ?>
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
                <div class="header-links">
                    <div class="header-links_icon">
                        <img src="<?= base_url('assets/images/icons/mail.svg') ?>" alt="">
                    </div>

                    <a href="<?= base_url('/admission-officer/follow-up') ?>" class="header-links_icon" style="position:relative;text-decoration:none;">
                        <img src="<?= base_url('assets/images/icons/notification-bell.svg') ?>" alt="Messages">
                        <span id="msgBadge" style="
                            display:none;
                            position:absolute;top:-5px;right:-5px;
                            min-width:16px;height:16px;padding:0 4px;
                            background:var(--accent-color,#e53e3e);
                            color:#fff;font-size:9px;font-weight:800;
                            border-radius:999px;
                            align-items:center;justify-content:center;
                            border:2px solid #fff;
                            line-height:1;
                        "></span>
                    </a>
                </div>
            </div>

            <div class="content-container">
                <?= $this->include('admission-officer/components/breadcrumb') ?>    

                <?= view($page_content) ?>
            </div>
        </main>
    </div>

    <script>
    // ── Unread message badge polling ──────────────────────────────────────
    (function () {
        const UNREAD_URL = '<?= base_url('/admission-officer/follow-up/unread-count') ?>';

        function updateBadges(count) {
            const headerBadge  = document.getElementById('msgBadge');
            const sidebarBadge = document.getElementById('sidebarMsgBadge');
            const label        = count > 99 ? '99+' : count;

            if (count > 0) {
                if (headerBadge)  { headerBadge.textContent = label;  headerBadge.style.display  = 'flex'; }
                if (sidebarBadge) { sidebarBadge.textContent = label; sidebarBadge.style.display = 'flex'; }
            } else {
                if (headerBadge)  headerBadge.style.display  = 'none';
                if (sidebarBadge) sidebarBadge.style.display = 'none';
            }
        }

        async function pollUnread() {
            try {
                const res  = await fetch(UNREAD_URL);
                const data = await res.json();
                if (data.success) updateBadges(data.count);
            } catch (e) { /* silent */ }
        }

        pollUnread();
        setInterval(pollUnread, 30000);
    })();
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

    <?= view('admission-officer/components/ai_assistant', ['theme' => $theme]) ?>
</body>
</html>
